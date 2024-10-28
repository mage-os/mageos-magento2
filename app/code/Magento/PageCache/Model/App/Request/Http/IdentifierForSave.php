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
declare(strict_types=1);

namespace Magento\PageCache\Model\App\Request\Http;

use Magento\Framework\App\Http\Context;
use Magento\Framework\App\PageCache\Identifier;
use Magento\Framework\App\PageCache\IdentifierInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Page unique identifier
 */
class IdentifierForSave implements IdentifierInterface
{
    /**
     * @param Http $request
     * @param Context $context
     * @param Json $serializer
     * @param IdentifierStoreReader $identifierStoreReader
     */
    public function __construct(
        private Http                  $request,
        private Context               $context,
        private Json                  $serializer,
        private IdentifierStoreReader $identifierStoreReader,
    ) {
    }

    /**
     * Return unique page identifier
     *
     * @return string
     */
    public function getValue()
    {
        $pattern = Identifier::PATTERN_MARKETING_PARAMETERS;
        $replace = array_fill(0, count($pattern), '');
        $data = [
            $this->request->isSecure(),
            preg_replace($pattern, $replace, (string)$this->request->getUriString()),
            $this->context->getVaryString()
        ];

        $data = $this->identifierStoreReader->getPageTagsWithStoreCacheTags($data);

        return sha1($this->serializer->serialize($data));
    }
}
