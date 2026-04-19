<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\SalesRule\Test\Unit\Model\Plugin;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Quote\Model\Quote\Config;
use Magento\SalesRule\Model\Plugin\QuoteConfigProductAttributes;
use Magento\SalesRule\Model\ReadRequestFlag;
use Magento\SalesRule\Model\ResourceModel\Rule;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class QuoteConfigProductAttributesTest extends TestCase
{
    /**
     * @var QuoteConfigProductAttributes|MockObject
     */
    protected $plugin;

    /**
     * @var Rule|MockObject
     */
    protected $ruleResource;

    /**
     * @var ReadRequestFlag|MockObject
     */
    protected $readRequestFlag;

    /**
     * @var CacheInterface|MockObject
     */
    protected $cache;

    /**
     * @var SerializerInterface|MockObject
     */
    protected $serializer;

    /**
     * @var RequestInterface|MockObject
     */
    protected $requestInterface;

    /**
     * @var Config|MockObject
     */
    protected $subject;

    protected function setUp(): void
    {
        $this->ruleResource = $this->createMock(Rule::class);
        $this->readRequestFlag = $this->createMock(ReadRequestFlag::class);
        $this->requestInterface = $this->createMock(RequestInterface::class);
        $this->cache = $this->createMock(CacheInterface::class);
        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->subject = $this->createMock(Config::class);

        $this->plugin = new QuoteConfigProductAttributes(
            $this->ruleResource,
            $this->requestInterface,
            $this->readRequestFlag,
            $this->cache,
            $this->serializer
        );
    }

    public function testAfterGetProductAttributesWithCache()
    {
        $attributeCode = 'code of the attribute';
        $expected = [0 => $attributeCode];
        $serializedData = '["' . $attributeCode . '"]';

        $this->readRequestFlag->expects($this->once())
            ->method('isReadRequest')
            ->willReturn(false);

        $this->cache->expects($this->once())
            ->method('load')
            ->with('salesrule_active_product_attributes')
            ->willReturn($serializedData);

        $this->serializer->expects($this->once())
            ->method('unserialize')
            ->with($serializedData)
            ->willReturn([$attributeCode]);

        $this->ruleResource->expects($this->never())
            ->method('getActiveAttributes');

        $this->assertEquals($expected, $this->plugin->afterGetProductAttributes($this->subject, []));
    }

    public function testAfterGetProductAttributesWithoutCache()
    {
        $attributeCode = 'code of the attribute';
        $expected = [0 => $attributeCode];
        $serializedData = '["' . $attributeCode . '"]';

        $this->readRequestFlag->expects($this->once())
            ->method('isReadRequest')
            ->willReturn(false);

        $this->cache->expects($this->once())
            ->method('load')
            ->with('salesrule_active_product_attributes')
            ->willReturn(false);

        $this->ruleResource->expects($this->once())
            ->method('getActiveAttributes')
            ->willReturn(
                [
                    ['attribute_code' => $attributeCode, 'enabled' => true],
                ]
            );

        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with([$attributeCode])
            ->willReturn($serializedData);

        $this->cache->expects($this->once())
            ->method('save')
            ->with($serializedData, 'salesrule_active_product_attributes', ['salesrule']);

        $this->assertEquals($expected, $this->plugin->afterGetProductAttributes($this->subject, []));
    }

    public function testAfterGetProductAttributesRequestTypePostOrMutation()
    {
        $this->readRequestFlag->expects($this->once())
            ->method('isReadRequest')
            ->willReturn(true);

        $this->assertEquals([], $this->plugin->afterGetProductAttributes($this->subject, []));
    }
}
