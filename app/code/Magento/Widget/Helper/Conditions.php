<?php
/**
 * Copyright 2014 Adobe
 * All Rights Reserved.
 */
namespace Magento\Widget\Helper;

use Magento\Framework\Data\Wysiwyg\Normalizer;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Widget Conditions helper.
 */
class Conditions
{
    /**
     * @var Json
     */
    private $serializer;

    /**
     * @var Normalizer
     */
    private $normalizer;

    /**
     * @param Json $serializer
     * @param Normalizer $normalizer
     */
    public function __construct(
        ?Json $serializer = null,
        ?Normalizer $normalizer = null
    ) {
        $this->serializer = $serializer ?: ObjectManager::getInstance()->get(Json::class);
        $this->normalizer = $normalizer ?: ObjectManager::getInstance()->get(Normalizer::class);
    }

    /**
     * Encode widget conditions to be used with WYSIWIG.
     *
     * @param array $value
     * @return string
     */
    public function encode(array $value)
    {
        return $this->normalizer->replaceReservedCharacters($this->serializer->serialize($value));
    }

    /**
     * Decode previously encoded widget conditions.
     *
     * @param string $value
     * @return array
     */
    public function decode($value)
    {
        return $this->serializer->unserialize(
            $this->normalizer->restoreReservedCharacters($value)
        );
    }
}
