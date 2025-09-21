<?php
/**
 * Copyright 2014 Adobe
 * All Rights Reserved.
 */
namespace Magento\Review\Controller\Adminhtml\Rating;

use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Review\Controller\Adminhtml\Rating as RatingController;
use Magento\Framework\Controller\ResultFactory;

class Edit extends RatingController implements HttpGetActionInterface
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $this->initEntityId();
        /** @var \Magento\Review\Model\Rating $ratingModel */
        $ratingModel = $this->_objectManager->create(\Magento\Review\Model\Rating::class);
        if ($this->getRequest()->getParam('id')) {
            $ratingModel->load($this->getRequest()->getParam('id'));
        }
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Magento_Review::catalog_reviews_ratings_ratings');
        $resultPage->getConfig()->getTitle()->prepend(__('Ratings'));
        $resultPage->getConfig()->getTitle()->prepend(
            $ratingModel->getId() ? $ratingModel->getRatingCode() : __('New Rating')
        );
        $resultPage->addBreadcrumb(__('Manage Ratings'), __('Manage Ratings'));
        $resultPage->addContent($resultPage->getLayout()->createBlock(
            \Magento\Review\Block\Adminhtml\Rating\Edit::class
        ))->addLeft($resultPage->getLayout()->createBlock(\Magento\Review\Block\Adminhtml\Rating\Edit\Tabs::class));
        return $resultPage;
    }
}
