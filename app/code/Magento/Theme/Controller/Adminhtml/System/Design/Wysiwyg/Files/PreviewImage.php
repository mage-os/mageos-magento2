<?php
/**
 * Copyright 2014 Adobe
 * All Rights Reserved.
 */
namespace Magento\Theme\Controller\Adminhtml\System\Design\Wysiwyg\Files;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class PreviewImage extends \Magento\Theme\Controller\Adminhtml\System\Design\Wysiwyg\Files
{
    /**
     * Preview image action
     *
     * @return ResponseInterface|void
     */
    public function execute()
    {
        $file = $this->getRequest()->getParam('file');
        /** @var $helper \Magento\Theme\Helper\Storage */
        $helper = $this->_objectManager->get(\Magento\Theme\Helper\Storage::class);
        try {
            return $this->_fileFactory->create(
                $file,
                ['type' => 'filename', 'value' => $helper->getThumbnailPath($file)],
                DirectoryList::MEDIA
            );
        } catch (\Exception $e) {
            $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
            $this->_redirect('core/index/notFound');
        }
    }
}
