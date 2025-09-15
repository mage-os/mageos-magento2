<?php
/**
 * Copyright 2014 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\AdminNotification\Controller\Adminhtml\Notification;

use Magento\AdminNotification\Controller\Adminhtml\Notification;
use Magento\AdminNotification\Model\NotificationService;
use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;

/**
 * AdminNotification AjaxMarkAsRead controller
 */
class AjaxMarkAsRead extends Notification implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Magento_AdminNotification::mark_as_read';

    /**
     * @var NotificationService
     */
    private $notificationService;

    /**
     * @param Action\Context $context
     * @param NotificationService $notificationService
     */
    public function __construct(Action\Context $context, NotificationService $notificationService)
    {
        parent::__construct($context);
        $this->notificationService = $notificationService;
    }

    /**
     * Mark notification as read (AJAX action)
     *
     * @return \Magento\Framework\Controller\Result\Json|void
     * @throws \InvalidArgumentException
     */
    public function execute()
    {
        if (!$this->getRequest()->getPostValue()) {
            return;
        }
        $notificationId = (int)$this->getRequest()->getPost('id');
        $responseData = [];
        try {
            $this->notificationService->markAsRead($notificationId);
            $responseData['success'] = true;
        } catch (\Exception $e) {
            $responseData['success'] = false;
        }

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($responseData);
        return $resultJson;
    }
}
