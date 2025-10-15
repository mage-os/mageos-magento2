<?php
/**
 * Copyright 2020 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Ui\Component;

/**
 * Image Uploader component
 */
class ImageUploaderStandAlone extends ImageUploader
{

    /**
     * @inheritdoc
     */
    public function prepare(): void
    {
        parent::prepare();
        $this->setData(
            'config',
            array_replace_recursive(
                (array) $this->getData('config'),
                [
                    'actionsPath' => 'standalone_media_gallery_listing.standalone_media_gallery_listing' .
                        '.media_gallery_columns.thumbnail_url',
                    'directoriesPath' => 'standalone_media_gallery_listing.standalone_media_gallery_listing' .
                        '.media_gallery_directories',
                    'messagesPath' => 'standalone_media_gallery_listing.standalone_media_gallery_listing.messages'
                ]
            )
        );
    }
}
