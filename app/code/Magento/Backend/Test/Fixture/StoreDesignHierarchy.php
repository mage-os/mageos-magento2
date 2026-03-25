<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Fixture;

use Magento\Framework\Registry;
use Magento\Store\Api\Data\GroupInterfaceFactory;
use Magento\Store\Api\Data\StoreInterfaceFactory;
use Magento\Store\Api\Data\WebsiteInterfaceFactory;
use Magento\Store\Model\ResourceModel\Group as GroupResource;
use Magento\Store\Model\ResourceModel\Store as StoreResource;
use Magento\Store\Model\ResourceModel\Website as WebsiteResource;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Creates and removes website / group / store entities for {@see StoreDesignConfig}.
 */
class StoreDesignHierarchy
{
    public const WEBSITE_CODE = 'design_cfg_test_ws';

    public const GROUP_CODE = 'design_cfg_test_grp';

    public const STORE_CODE = 'design_cfg_test_sv';

    /**
     * @var string[]
     */
    private const STORE_CODES_TO_REMOVE = ['design_cfg_test_sv_rn', self::STORE_CODE];

    /**
     * @param StoreManagerInterface $storeManager
     * @param WebsiteResource $websiteResource
     * @param StoreResource $storeResource
     * @param GroupResource $groupResource
     * @param WebsiteInterfaceFactory $websiteFactory
     * @param GroupInterfaceFactory $groupFactory
     * @param StoreInterfaceFactory $storeFactory
     */
    public function __construct(
        private readonly StoreManagerInterface $storeManager,
        private readonly WebsiteResource $websiteResource,
        private readonly StoreResource $storeResource,
        private readonly GroupResource $groupResource,
        private readonly WebsiteInterfaceFactory $websiteFactory,
        private readonly GroupInterfaceFactory $groupFactory,
        private readonly StoreInterfaceFactory $storeFactory,
    ) {
    }

    /**
     * Create website, store group, and store view.
     *
     * @return void
     */
    public function apply(): void
    {
        $website = $this->websiteFactory->create();
        $website->setCode(self::WEBSITE_CODE)->setName('Design Config Test Website');
        $this->websiteResource->save($website);

        $storeGroup = $this->groupFactory->create();
        $rootCategoryId = (int) $this->storeManager->getDefaultStoreView()->getRootCategoryId();
        $storeGroup->setCode(self::GROUP_CODE)
            ->setRootCategoryId($rootCategoryId)
            ->setName('Design Config Test Store Group')
            ->setWebsite($website);
        $this->groupResource->save($storeGroup);

        $this->storeManager->reinitStores();

        $store = $this->storeFactory->create();
        $store->setCode(self::STORE_CODE)
            ->setWebsiteId((int) $website->getId())
            ->setGroupId((int) $storeGroup->getId())
            ->setName('Design Config Test Store View')
            ->setSortOrder(10)
            ->setIsActive(1);
        $this->storeResource->save($store);

        $this->storeManager->reinitStores();
    }

    /**
     * Remove entities created for design-config store tests.
     *
     * @param Registry $registry
     * @return void
     */
    public function revert(Registry $registry): void
    {
        $registry->unregister('isSecureArea');
        $registry->register('isSecureArea', true);

        $store = $this->storeFactory->create();
        foreach (self::STORE_CODES_TO_REMOVE as $storeCode) {
            $this->storeResource->load($store, $storeCode, 'code');
            if ($store->getId()) {
                $this->storeResource->delete($store);
            }
        }

        $storeGroup = $this->groupFactory->create();
        $this->groupResource->load($storeGroup, self::GROUP_CODE, 'code');
        if ($storeGroup->getId()) {
            $this->groupResource->delete($storeGroup);
        }

        $website = $this->websiteFactory->create();
        $this->websiteResource->load($website, self::WEBSITE_CODE, 'code');
        if ($website->getId()) {
            $this->websiteResource->delete($website);
        }

        $registry->unregister('isSecureArea');
        $registry->register('isSecureArea', false);
    }
}
