<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
 */

declare(strict_types=1);

namespace Magento\Cms\Model\Wysiwyg;

use Magento\Framework\DataObject;
use Magento\Framework\View\Asset\Repository;

/**
 * Class DefaultConfigProvider returns data required to render tinymce editor
 */
class DefaultConfigProvider implements \Magento\Framework\Data\Wysiwyg\ConfigProviderInterface
{
    /**
     * @var Repository
     */
    private Repository $assetRepo;

    /**
     * @param Repository $assetRepo
     */
    public function __construct(Repository $assetRepo)
    {
        $this->assetRepo = $assetRepo;
    }

    /**
     * @inheritdoc
     */
    public function getConfig(DataObject $config) : DataObject
    {
        $config->addData([
            'tinymce' => [
                'toolbar' => 'blocks | fontfamily fontsizeinput lineheight | forecolor backcolor | '
                    . 'bold italic underline | alignleft aligncenter alignright alignjustify | '
                    . 'bullist numlist | link image',
                'plugins' => implode(
                    ' ',
                    [
                        'anchor',
                        'autolink',
                        'charmap',
                        'code',
                        'codesample',
                        'directionality',
                        'emoticons',
                        'help',
                        'image',
                        'link',
                        'lists',
                        'media',
                        'nonbreaking',
                        'preview',
                        'table',
                        'visualblocks',
                        'visualchars',
                        'advlist',
                    ]
                ),
                'content_css' => $this->assetRepo->getUrl('mage/adminhtml/wysiwyg/tiny_mce/themes/ui.css'),
            ],
            'settings' => [
                'menubar' => 'edit insert view format table help',
                'statusbar' => false,
                'image_advtab' => true,
                'promotion' => false,
            ],
        ]);
        return $config;
    }
}
