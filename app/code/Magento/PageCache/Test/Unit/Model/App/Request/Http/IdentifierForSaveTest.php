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

namespace Magento\PageCache\Test\Unit\Model\App\Request\Http;

use Magento\Framework\App\Http\Context;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\PageCache\Model\App\Request\Http\IdentifierForSave;
use Magento\PageCache\Model\App\Request\Http\IdentifierStoreReader;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class IdentifierForSaveTest extends TestCase
{
    /**
     * Test value for cache vary string
     */
    private const VARY = '123';

    /**
     * @var Context|MockObject
     */
    private mixed $contextMock;

    /**
     * @var HttpRequest|MockObject
     */
    private mixed $requestMock;

    /**
     * @var IdentifierForSave
     */
    private IdentifierForSave $model;

    /**
     * @var Json|MockObject
     */
    private mixed $serializerMock;
    /**
     * @var IdentifierStoreReader|MockObject
     */
    private $identifierStoreReader;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->requestMock = $this->getMockBuilder(HttpRequest::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->serializerMock = $this->getMockBuilder(Json::class)
            ->onlyMethods(['serialize'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->serializerMock->expects($this->any())
            ->method('serialize')
            ->willReturnCallback(
                function ($value) {
                    return json_encode($value);
                }
            );

        $this->identifierStoreReader = $this->getMockBuilder(IdentifierStoreReader::class)
            ->onlyMethods(['getPageTagsWithStoreCacheTags'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = new IdentifierForSave(
            $this->requestMock,
            $this->contextMock,
            $this->serializerMock,
            $this->identifierStoreReader
        );
        parent::setUp();
    }

    /**
     * Test get identifier for save value.
     *
     * @return void
     */
    public function testGetValue(): void
    {
        $this->requestMock->expects($this->any())
            ->method('isSecure')
            ->willReturn(true);

        $this->requestMock->expects($this->any())
            ->method('getUriString')
            ->willReturn('http://example.com/path1/');

        $this->contextMock->expects($this->any())
            ->method('getVaryString')
            ->willReturn(self::VARY);

        $this->identifierStoreReader->method('getPageTagsWithStoreCacheTags')->willReturnCallback(
            function ($value) {
                return $value;
            }
        );

        $this->assertEquals(
            sha1(
                json_encode(
                    [
                        true,
                        'http://example.com/path1/',
                        self::VARY
                    ]
                )
            ),
            $this->model->getValue()
        );
    }

    /**
     * Test get identifier for save value with marketing parameters.
     *
     * @return void
     */
    public function testGetValueWithMarketingParameters(): void
    {
        $this->requestMock->expects($this->any())
            ->method('isSecure')
            ->willReturn(true);

        $this->requestMock->expects($this->any())
            ->method('getUriString')
            ->willReturn('http://example.com/path1/?abc=123&gclid=456&utm_source=abc');

        $this->contextMock->expects($this->any())
            ->method('getVaryString')
            ->willReturn(self::VARY);

        $this->identifierStoreReader->method('getPageTagsWithStoreCacheTags')->willReturnCallback(
            function ($value) {
                return $value;
            }
        );

        $this->assertEquals(
            sha1(
                json_encode(
                    [
                        true,
                        'http://example.com/path1/?abc=123',
                        self::VARY
                    ]
                )
            ),
            $this->model->getValue()
        );
    }
}
