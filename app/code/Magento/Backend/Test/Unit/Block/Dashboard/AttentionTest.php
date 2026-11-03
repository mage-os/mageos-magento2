<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Block\Dashboard;

use Magento\Backend\Block\Dashboard\Attention;
use Magento\Backend\Model\Dashboard\Attention\CappedCounter;
use Magento\Backend\Model\Dashboard\Attention\ItemInterface;
use Magento\Backend\Model\Dashboard\Attention\Pool;
use Magento\Backend\Model\Dashboard\Config;
use Magento\Backend\Model\Dashboard\StatisticsCache;
use Magento\Framework\App\ObjectManager as AppObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Phrase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Website;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class AttentionTest extends TestCase
{
    /**
     * @var StatisticsCache|MockObject
     */
    private $statisticsCache;

    /**
     * @var AuthorizationInterface|MockObject
     */
    private $authorization;

    /**
     * @var LoggerInterface|MockObject
     */
    private $logger;

    /**
     * @var array
     */
    private array $requestParams = [];

    protected function setUp(): void
    {
        $appObjectManager = $this->createMock(ObjectManagerInterface::class);
        $appObjectManager->method('get')->willReturnCallback(fn (string $type) => $this->createMock($type));
        AppObjectManager::setInstance($appObjectManager);

        $this->statisticsCache = $this->createMock(StatisticsCache::class);
        $this->statisticsCache->method('get')
            ->willReturnCallback(fn ($key, $scope, $lifetime, $loader) => $loader());
        $this->authorization = $this->createMock(AuthorizationInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
    }

    private function createItem(
        string $code,
        int $sortOrder,
        string $acl,
        ?int $count,
        ?\Throwable $failure = null
    ): ItemInterface {
        $item = $this->createMock(ItemInterface::class);
        $item->method('getCode')->willReturn($code);
        $item->method('getLabel')->willReturn(new Phrase(ucfirst($code)));
        $item->method('getUrlPath')->willReturn($code . '/index');
        $item->method('getAclResource')->willReturn($acl);
        $item->method('getSortOrder')->willReturn($sortOrder);
        if ($failure) {
            $item->method('getCount')->willThrowException($failure);
        } else {
            $item->method('getCount')->willReturn($count);
        }
        return $item;
    }

    private function createBlock(array $items): Attention
    {
        $request = $this->createMock(RequestInterface::class);
        $request->method('getParam')->willReturnCallback(
            fn ($name, $default = null) => $this->requestParams[$name] ?? $default
        );
        $urlBuilder = $this->createMock(UrlInterface::class);
        $urlBuilder->method('getUrl')->willReturnCallback(fn ($path) => 'https://admin.test/' . $path);

        $website = $this->createMock(Website::class);
        $website->method('getStoreIds')->willReturn(['2', '3']);
        $storeManager = $this->createMock(StoreManagerInterface::class);
        $storeManager->method('getWebsite')->willReturn($website);

        $config = $this->createMock(Config::class);
        $config->method('getTotalsCacheLifetime')->willReturn(300);

        return (new ObjectManager($this))->getObject(
            Attention::class,
            [
                'request' => $request,
                'urlBuilder' => $urlBuilder,
                'authorization' => $this->authorization,
                'storeManager' => $storeManager,
                'pool' => new Pool($items),
                'statisticsCache' => $this->statisticsCache,
                'dashboardConfig' => $config,
                'cappedCounter' => new CappedCounter(),
                'logger' => $this->logger,
            ]
        );
    }

    public function testItemsAreCountedFilteredByAclAndFormatted(): void
    {
        $this->authorization->method('isAllowed')->willReturnCallback(fn ($acl) => $acl !== 'Denied::acl');
        $this->requestParams = ['website' => '1'];

        $orders = $this->createItem('orders', 10, 'Magento_Sales::sales_order', 1001);
        $orders->expects($this->once())->method('getCount')->with([2, 3]);
        $reviews = $this->createItem('reviews', 20, 'Magento_Review::pending', 0);
        $hidden = $this->createItem('hidden', 30, 'Magento_Indexer::index', null);
        $denied = $this->createItem('denied', 5, 'Denied::acl', 3);

        $block = $this->createBlock([$hidden, $reviews, $orders, $denied]);
        $items = $block->getItems();

        $this->assertSame(['orders', 'reviews'], array_column($items, 'code'));
        $this->assertSame('1000+', $items[0]['display']);
        $this->assertTrue($items[0]['active']);
        $this->assertSame('https://admin.test/orders/index', $items[0]['url']);
        $this->assertSame('0', $items[1]['display']);
        $this->assertFalse($items[1]['active']);
        $this->assertSame($items, $block->getItems(), 'Result is memoized');
    }

    public function testNothingRenderedWhenAdminMaySeeNoTile(): void
    {
        $this->authorization->method('isAllowed')->willReturn(false);
        $this->statisticsCache->expects($this->never())->method('get');

        $item = $this->createItem('orders', 10, 'Magento_Sales::sales_order', 5);
        $item->expects($this->never())->method('getCount');

        $this->assertSame([], $this->createBlock([$item])->getItems());
    }

    public function testFailingItemIsLoggedAndHiddenWithoutBreakingOthers(): void
    {
        $this->authorization->method('isAllowed')->willReturn(true);
        $this->logger->expects($this->once())->method('error')->with($this->stringContains('broken'));

        $broken = $this->createItem('broken', 10, 'A::b', null, new \RuntimeException('db gone'));
        $fine = $this->createItem('fine', 20, 'A::b', 2);

        $items = $this->createBlock([$broken, $fine])->getItems();

        $this->assertSame(['fine'], array_column($items, 'code'));
        $this->assertSame(2, $items[0]['count']);
    }
}
