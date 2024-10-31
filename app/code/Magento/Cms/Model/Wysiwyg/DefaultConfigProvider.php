<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Cms\Model\Wysiwyg;

/**
 * Class DefaultConfigProvider returns data required to render tinymce editor
 */
class DefaultConfigProvider implements \Magento\Framework\Data\Wysiwyg\ConfigProviderInterface
{
    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    private $assetRepo;

    /**
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     */
    public function __construct(\Magento\Framework\View\Asset\Repository $assetRepo)
    {
        $this->assetRepo = $assetRepo;
    }

    /**
     * @inheritdoc
     */
    public function getConfig(\Magento\Framework\DataObject $config) : \Magento\Framework\DataObject
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
