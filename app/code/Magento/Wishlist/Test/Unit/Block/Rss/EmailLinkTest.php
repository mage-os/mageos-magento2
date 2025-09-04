<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);


namespace Magento\Wishlist\Test\Unit\Block\Rss;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\Rss\UrlBuilderInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Url\EncoderInterface;
use Magento\Wishlist\Block\Rss\EmailLink;
use Magento\Wishlist\Helper\Data;
use Magento\Wishlist\Model\Wishlist;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class EmailLinkTest extends TestCase
{
    /** @var EmailLink */
    protected $link;

    /** @var Context|MockObject */
    protected $context;

    /** @var Data|MockObject */
    protected $wishlistHelper;

    /** @var UrlBuilderInterface|MockObject */
    protected $urlBuilder;

    /**
     * @var EncoderInterface|MockObject
     */
    protected $urlEncoder;

    protected function setUp(): void
    {
        $wishlist = new class extends Wishlist {
            public function __construct()
            {
 /* Skip parent constructor */
            }
            public function getSharingCode()
            {
                return 'somesharingcode';
            }
            public function getId()
            {
                return 5;
            }
        };
        $customer = $this->createMock(CustomerInterface::class);
        $customer->method('getId')->willReturn(8);
        $customer->method('getEmail')->willReturn('test@example.com');

        $this->wishlistHelper = $this->createPartialMock(Data::class, ['getWishlist', 'getCustomer']);
        $this->urlEncoder = $this->createPartialMock(EncoderInterface::class, ['encode']);

        $this->wishlistHelper->expects($this->any())->method('getWishlist')->willReturn($wishlist);
        $this->wishlistHelper->expects($this->any())->method('getCustomer')->willReturn($customer);
        $this->urlEncoder->expects($this->any())
            ->method('encode')
            ->willReturnCallback(function ($url) {
                return strtr(base64_encode($url), '+/=', '-_,');
            });

        $this->urlBuilder = $this->createMock(UrlBuilderInterface::class);
        $this->context = $this->createMock(Context::class);

        $this->link = new EmailLink(
            $this->context,
            $this->wishlistHelper,
            $this->urlBuilder,
            $this->urlEncoder
        );
    }

    public function testGetLink()
    {
        $this->urlBuilder->expects($this->atLeastOnce())->method('getUrl')
            ->with([
                'type' => 'wishlist',
                'data' => 'OCx0ZXN0QGV4YW1wbGUuY29t',
                '_secure' => false,
                'wishlist_id' => 5,
                'sharing_code' => 'somesharingcode',
            ])
            ->willReturn('http://url.com/rss/feed/index/type/wishlist/wishlist_id/5');
        $this->assertEquals('http://url.com/rss/feed/index/type/wishlist/wishlist_id/5', $this->link->getLink());
    }
}
