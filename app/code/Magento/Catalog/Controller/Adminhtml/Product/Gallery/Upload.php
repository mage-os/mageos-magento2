<?php
/**
 * Copyright 2011 Adobe
 * All Rights Reserved.
 */
namespace Magento\Catalog\Controller\Adminhtml\Product\Gallery;

use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\Model\Image\UploadResizeConfigInterface;
use Psr\Log\LoggerInterface;

/**
 * The product gallery upload controller
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Upload extends \Magento\Backend\App\Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Magento_Catalog::products';

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var array
     */
    private $allowedMimeTypes = [
        'jpg' => 'image/jpg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'png' => 'image/png'
    ];

    /**
     * @var \Magento\Framework\Image\AdapterFactory
     */
    private $adapterFactory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;

    /**
     * @var \Magento\Catalog\Model\Product\Media\Config
     */
    private $productMediaConfig;

    /**
     * @var UploadResizeConfigInterface
     */
    private $imageUploadConfig;

    /**
     * @var LoggerInterface
     */
    private $_logger;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\Image\AdapterFactory|null $adapterFactory
     * @param \Magento\Framework\Filesystem|null $filesystem
     * @param \Magento\Catalog\Model\Product\Media\Config|null $productMediaConfig
     * @param UploadResizeConfigInterface|null $imageUploadConfig
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\Image\AdapterFactory $adapterFactory = null,
        \Magento\Framework\Filesystem $filesystem = null,
        \Magento\Catalog\Model\Product\Media\Config $productMediaConfig = null,
        UploadResizeConfigInterface $imageUploadConfig = null
    ) {
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;
        $this->adapterFactory = $adapterFactory ?: ObjectManager::getInstance()
            ->get(\Magento\Framework\Image\AdapterFactory::class);
        $this->filesystem = $filesystem ?: ObjectManager::getInstance()
            ->get(\Magento\Framework\Filesystem::class);
        $this->productMediaConfig = $productMediaConfig ?: ObjectManager::getInstance()
            ->get(\Magento\Catalog\Model\Product\Media\Config::class);
        $this->imageUploadConfig = $imageUploadConfig
            ?: ObjectManager::getInstance()->get(UploadResizeConfigInterface::class);
    }

    /**
     * Upload image(s) to the product gallery.
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        try {
            $uploader = $this->_objectManager->create(
                \Magento\MediaStorage\Model\File\Uploader::class,
                ['fileId' => 'image']
            );
            $uploader->setAllowedExtensions($this->getAllowedExtensions());
            $imageAdapter = $this->adapterFactory->create();
            $uploader->addValidateCallback('catalog_product_image', $imageAdapter, 'validateUploadFile');
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
            $result = $uploader->save(
                $mediaDirectory->getAbsolutePath($this->productMediaConfig->getBaseTmpMediaPath())
            );
            // Resize the image if needed
            $this->processImage(
                $imageAdapter,
                $mediaDirectory->getAbsolutePath($this->productMediaConfig->getBaseTmpMediaPath()),
                $result['file']
            );
            $this->_eventManager->dispatch(
                'catalog_product_gallery_upload_image_after',
                ['result' => $result, 'action' => $this]
            );

            if (is_array($result)) {
                unset($result['tmp_name']);
                unset($result['path']);

                $result['url'] = $this->productMediaConfig->getTmpMediaUrl($result['file']);
                $result['file'] = $result['file'] . '.tmp';
            } else {
                $result = ['error' => 'Something went wrong while saving the file(s).'];
            }
        } catch (LocalizedException $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        } catch (\Throwable $e) {
            $result = ['error' => 'Something went wrong while saving the file(s).', 'errorcode' => 0];
        }

        /** @var \Magento\Framework\Controller\Result\Raw $response */
        $response = $this->resultRawFactory->create();
        $response->setHeader('Content-type', 'text/plain');
        $response->setContents(json_encode($result));
        return $response;
    }

    /**
     * Resize the image
     *
     * @param \Magento\Framework\Image\AdapterFactory $imageAdapter
     * @param string $path
     * @param string $file
     * @return bool
     */
    private function processImage($imageAdapter, $path, $file): bool
    {
        try {
            $filePath = $path . DIRECTORY_SEPARATOR . $file;

            // Open the image file
            $imageAdapter->open($filePath);

            // Get current dimensions
            $imageWidth = $imageAdapter->getOriginalWidth();
            $imageHeight = $imageAdapter->getOriginalHeight();

            // Fetch resizing configurations
            $maxWidth = $this->imageUploadConfig->getMaxWidth();
            $maxHeight = $this->imageUploadConfig->getMaxHeight();

            // Check if resizing is necessary
            if ($this->imageUploadConfig->isResizeEnabled()
                && ($imageWidth > $maxWidth || $imageHeight > $maxHeight)) {
                // Maintain aspect ratio and resize
                $imageAdapter->keepAspectRatio(true);
                $imageAdapter->resize($maxWidth, $maxHeight);

                // Save the resized image
                $imageAdapter->save($filePath);
            }

            return true;
        } catch (\Exception $e) {
            $this->_logger->error($e->getMessage());
            return false;
        }
    }

    /**
     * Get the set of allowed file extensions.
     *
     * @return array
     */
    private function getAllowedExtensions()
    {
        return array_keys($this->allowedMimeTypes);
    }
}
