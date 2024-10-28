<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 *
 * NOTICE: All information contained herein is, and remains
 * the property of Adobe and its suppliers, if any. The intellectual
 * and technical concepts contained herein are proprietary to Adobe
 * and its suppliers and are protected by all applicable intellectual
 * property laws, including trade secret and copyright laws.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained from
 * Adobe.
 */
namespace Magento\Framework\App\PageCache;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Page unique identifier
 */
class Identifier implements IdentifierInterface
{
    /**
     * Pattern detect marketing parameters
     */
    public const PATTERN_MARKETING_PARAMETERS = [
        '/&?gad_source\=[^&]+/',
        '/&?gbraid\=[^&]+/',
        '/&?wbraid\=[^&]+/',
        '/&?_gl\=[^&]+/',
        '/&?dclid\=[^&]+/',
        '/&?gclsrc\=[^&]+/',
        '/&?srsltid\=[^&]+/',
        '/&?msclkid\=[^&]+/',
        '/&?_kx\=[^&]+/',
        '/&?gclid\=[^&]+/',
        '/&?cx\=[^&]+/',
        '/&?ie\=[^&]+/',
        '/&?cof\=[^&]+/',
        '/&?siteurl\=[^&]+/',
        '/&?zanpid\=[^&]+/',
        '/&?origin\=[^&]+/',
        '/&?fbclid\=[^&]+/',
        '/&?mc_(.*?)\=[^&]+/',
        '/&?utm_(.*?)\=[^&]+/',
        '/&?_bta_(.*?)\=[^&]+/',
    ];

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $context;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\App\Http\Context $context
     * @param Json|null $serializer
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\Http\Context $context,
        Json $serializer = null
    ) {
        $this->request = $request;
        $this->context = $context;
        $this->serializer = $serializer ?: ObjectManager::getInstance()->get(Json::class);
    }

    /**
     * Return unique page identifier
     *
     * @return string
     */
    public function getValue()
    {
        $pattern = self::PATTERN_MARKETING_PARAMETERS;
        $replace = array_fill(0, count(self::PATTERN_MARKETING_PARAMETERS), '');
        $data = [
            $this->request->isSecure(),
            preg_replace($pattern, $replace, (string)$this->request->getUriString()),
            $this->request->get(\Magento\Framework\App\Response\Http::COOKIE_VARY_STRING)
                ?: $this->context->getVaryString()
        ];

        return sha1($this->serializer->serialize($data));
    }
}
