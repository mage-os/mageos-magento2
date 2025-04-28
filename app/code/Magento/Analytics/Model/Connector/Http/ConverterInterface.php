<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
 */
namespace Magento\Analytics\Model\Connector\Http;

/**
 * Represents converter interface for http request and response body.
 *
 * @api
 * @since 100.2.0
 */
interface ConverterInterface
{
    /**
     * @param string $body
     *
     * @return array
     * @since 100.2.0
     */
    public function fromBody($body);

    /**
     * @param array $data
     *
     * @return string
     * @since 100.2.0
     */
    public function toBody(array $data);

    /**
     * @return string
     * @since 100.2.0
     */
    public function getContentTypeHeader();

    /**
     * @return string
     * @since 100.3.0
     */
    public function getContentMediaType(): string;
}
