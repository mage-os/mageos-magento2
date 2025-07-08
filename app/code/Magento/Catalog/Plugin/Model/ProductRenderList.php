<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */

declare(strict_types=1);

namespace Magento\Catalog\Plugin\Model;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Catalog\Model\ProductRenderList as Subject;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Group;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Webapi\Rest\Request;
use Psr\Log\LoggerInterface;

/**
 * Plugin to fix customer group context in ProductRenderList for API requests
 */
class ProductRenderList
{
    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var State
     */
    private $appState;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Request
     */
    private $request;

    /**
     * @param UserContextInterface $userContext
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerSession $customerSession
     * @param State $appState
     * @param LoggerInterface $logger
     * @param Request $request
     */
    public function __construct(
        UserContextInterface $userContext,
        CustomerRepositoryInterface $customerRepository,
        CustomerSession $customerSession,
        State $appState,
        LoggerInterface $logger,
        Request $request
    ) {
        $this->userContext = $userContext;
        $this->customerRepository = $customerRepository;
        $this->customerSession = $customerSession;
        $this->appState = $appState;
        $this->logger = $logger;
        $this->request = $request;
    }

    /**
     * Before getList - set customer group context for proper pricing
     *
     * @param Subject $subject
     * @param SearchCriteriaInterface $searchCriteria
     * @param int $storeId
     * @param string $currencyCode
     * @return array
     */
    public function beforeGetList(
        Subject $subject,
        SearchCriteriaInterface $searchCriteria,
        $storeId,
        $currencyCode
    ): array {
        try {
            if ($this->appState->getAreaCode() !== 'webapi_rest') {
                return [$searchCriteria, $storeId, $currencyCode];
            }

            $customerGroupId = $this->getCustomerGroupId();

            // Set customer group ID in session for proper pricing context
            if ($customerGroupId !== null) {
                $this->customerSession->setCustomerGroupId($customerGroupId);
            }

        } catch (\Exception $e) {
            $this->logger->error(
                'Error in ProductRenderList plugin: ' . $e->getMessage(),
                ['exception' => $e]
            );
        }

        return [$searchCriteria, $storeId, $currencyCode];
    }

    /**
     * Get customer group ID from current context
     *
     * @return int|null
     */
    private function getCustomerGroupId(): ?int
    {
        try {
            $userType = $this->userContext->getUserType();

            if ($userType === UserContextInterface::USER_TYPE_CUSTOMER) {
                $customerId = $this->userContext->getUserId();
                if ($customerId) {
                    $customer = $this->customerRepository->getById($customerId);
                    return (int)$customer->getGroupId();
                }
            }
            // For guest users, return the not logged in group ID
            return Group::NOT_LOGGED_IN_ID;

        } catch (NoSuchEntityException $e) {
            $this->logger->warning(
                'Customer not found in ProductRenderList plugin: ' . $e->getMessage()
            );
            return Group::NOT_LOGGED_IN_ID;
        } catch (LocalizedException $e) {
            $this->logger->error(
                'Error getting customer group ID in ProductRenderList plugin: ' . $e->getMessage()
            );
            return null;
        }
    }
}
