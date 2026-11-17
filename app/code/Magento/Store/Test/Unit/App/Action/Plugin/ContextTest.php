<?php
/**
 * Copyright © Mage-OS, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Store\Test\Unit\App\Action\Plugin;

use Magento\Framework\App\Action\AbstractAction;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Session\SessionManager;
use Magento\Framework\TestFramework\Unit\Helper\MockCreationTrait;
use Magento\Store\Api\Data\GroupInterface;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Api\StoreCookieManagerInterface;
use Magento\Store\App\Action\Plugin\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManager;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ContextTest extends TestCase
{
    use MockCreationTrait;

    private const WEBSITE_DEFAULT_GROUP_IDS = [1 => 1, 2 => 2];

    private const GROUP_DEFAULT_STORE_IDS = [1 => 1, 2 => 3, 3 => 5];

    private const STORES = [
        'default' => ['id' => 1, 'website_id' => 1, 'group_id' => 1, 'currency' => 'USD'],
        'default_alt' => ['id' => 2, 'website_id' => 1, 'group_id' => 1, 'currency' => 'USD'],
        'second_en' => ['id' => 3, 'website_id' => 2, 'group_id' => 2, 'currency' => 'EUR'],
        'second_fr' => ['id' => 4, 'website_id' => 2, 'group_id' => 2, 'currency' => 'EUR'],
        'outlet_en' => ['id' => 5, 'website_id' => 2, 'group_id' => 3, 'currency' => 'EUR'],
        'outlet_fr' => ['id' => 6, 'website_id' => 2, 'group_id' => 3, 'currency' => 'EUR'],
    ];

    /**
     * @var HttpContext
     */
    private HttpContext $httpContext;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->httpContext = new HttpContext([], [], new Json());
    }

    /**
     * @param string|null $runType
     * @param string|null $runCode
     * @param string $storeCode
     * @param bool $useStoreInUrl
     * @param string|null $sessionCurrency
     * @param array $expectedVaryData
     * @return void
     */
    #[DataProvider('varyDataProvider')]
    public function testBeforeDispatchSetsVaryData(
        ?string $runType,
        ?string $runCode,
        string $storeCode,
        bool $useStoreInUrl,
        ?string $sessionCurrency,
        array $expectedVaryData
    ): void {
        $this->createPlugin($useStoreInUrl, $sessionCurrency)
            ->beforeDispatch(
                $this->createMock(AbstractAction::class),
                $this->createRequest($runType, $runCode, $storeCode)
            );

        $this->assertSame($expectedVaryData, $this->httpContext->getData());
    }

    /**
     * @return array
     */
    public static function varyDataProvider(): array
    {
        $website = ScopeInterface::SCOPE_WEBSITE;
        $group = ScopeInterface::SCOPE_GROUP;
        $store = ScopeInterface::SCOPE_STORE;

        return [
            'website run type, default website, default store view' => [
                $website, 'base', 'default', false, null, [],
            ],
            'website run type, default website, other store view' => [
                $website, 'base', 'default_alt', false, null, ['store' => 'default_alt'],
            ],
            'website run type, second website, default store view' => [
                $website, 'second', 'second_en', false, null, [],
            ],
            'website run type, second website, other store view' => [
                $website, 'second', 'second_fr', false, null, ['store' => 'second_fr'],
            ],
            'website run type, second website, store view of non-default group' => [
                $website, 'second', 'outlet_en', false, null, ['store' => 'outlet_en'],
            ],
            'website run type, second website, currency changed by visitor' => [
                $website, 'second', 'second_en', false, 'USD', ['current_currency' => 'USD'],
            ],
            'website run type, second website, store code in url' => [
                $website, 'second', 'second_fr', true, null, [],
            ],
            'group run type, default group, default store view' => [
                $group, '1', 'default', false, null, [],
            ],
            'group run type, default group, other store view' => [
                $group, '1', 'default_alt', false, null, ['store' => 'default_alt'],
            ],
            'group run type, default group of second website, default store view' => [
                $group, '2', 'second_en', false, null, [],
            ],
            'group run type, default group of second website, other store view' => [
                $group, '2', 'second_fr', false, null, ['store' => 'second_fr'],
            ],
            'group run type, non-default group of second website, default store view' => [
                $group, '3', 'outlet_en', false, null, [],
            ],
            'group run type, non-default group of second website, other store view' => [
                $group, '3', 'outlet_fr', false, null, ['store' => 'outlet_fr'],
            ],
            'group run type, currency changed by visitor' => [
                $group, '3', 'outlet_en', false, 'USD', ['current_currency' => 'USD'],
            ],
            'group run type, store code in url' => [
                $group, '3', 'outlet_fr', true, null, [],
            ],
            'store run type, store view of run code' => [
                $store, 'second_en', 'second_en', false, null, [],
            ],
            'store run type, other store view' => [
                $store, 'second_en', 'second_fr', false, null, ['store' => 'second_fr'],
            ],
            'no run type, default store view' => [
                null, null, 'default', false, null, [],
            ],
            'no run type, store view of second website' => [
                null, null, 'second_en', false, null, ['store' => 'second_en', 'current_currency' => 'EUR'],
            ],
        ];
    }

    /**
     * @param string|null $runType
     * @param string|null $runCode
     * @return void
     */
    #[DataProvider('invalidStoreDataProvider')]
    public function testBeforeDispatchSetsCurrentStoreForInvalidStoreCode(?string $runType, ?string $runCode): void
    {
        $exception = null;
        try {
            $this->createPlugin(false, null)
                ->beforeDispatch(
                    $this->createMock(AbstractAction::class),
                    $this->createRequest($runType, $runCode, '')
                );
        } catch (NotFoundException $exception) {
        }

        $this->assertInstanceOf(NotFoundException::class, $exception);
        $this->assertSame('second_en', $this->httpContext->getValue(StoreManagerInterface::CONTEXT_STORE));
        $this->assertSame([], $this->httpContext->getData());
    }

    /**
     * @return array
     */
    public static function invalidStoreDataProvider(): array
    {
        return [
            'website run type' => [ScopeInterface::SCOPE_WEBSITE, 'second'],
            'group run type' => [ScopeInterface::SCOPE_GROUP, '3'],
        ];
    }

    /**
     * @return void
     */
    public function testBeforeDispatchKeepsContextAlreadySet(): void
    {
        $this->httpContext->setValue(StoreManagerInterface::CONTEXT_STORE, 'second_fr', 'second_en');
        $storeManager = $this->createMock(StoreManagerInterface::class);
        $storeManager->expects($this->never())->method('getStore');

        $plugin = new Context(
            $this->createMock(SessionManager::class),
            $this->httpContext,
            $storeManager,
            $this->createMock(StoreCookieManagerInterface::class)
        );
        $plugin->beforeDispatch(
            $this->createMock(AbstractAction::class),
            $this->createRequest(ScopeInterface::SCOPE_WEBSITE, 'second', 'second_en')
        );

        $this->assertSame(['store' => 'second_fr'], $this->httpContext->getData());
    }

    /**
     * @param bool $useStoreInUrl
     * @param string|null $sessionCurrency
     * @return Context
     */
    private function createPlugin(bool $useStoreInUrl, ?string $sessionCurrency): Context
    {
        $session = $this->createPartialMockWithReflection(SessionManager::class, ['getCurrencyCode']);
        $session->method('getCurrencyCode')->willReturn($sessionCurrency);
        $storeCookieManager = $this->createMock(StoreCookieManagerInterface::class);
        $storeCookieManager->method('getStoreCodeFromCookie')->willReturn(null);

        return new Context(
            $session,
            $this->httpContext,
            $this->createStoreManager($useStoreInUrl),
            $storeCookieManager
        );
    }

    /**
     * @param string|null $runType
     * @param string|null $runCode
     * @param string $storeCode
     * @return Http
     */
    private function createRequest(?string $runType, ?string $runCode, string $storeCode): Http
    {
        $request = $this->createMock(Http::class);
        $request->method('getParam')->willReturn($storeCode);
        $request->method('getServerValue')->willReturnMap([
            [StoreManager::PARAM_RUN_TYPE, null, $runType],
            [StoreManager::PARAM_RUN_CODE, null, $runCode],
        ]);

        return $request;
    }

    /**
     * @param bool $useStoreInUrl
     * @return StoreManagerInterface
     */
    private function createStoreManager(bool $useStoreInUrl): StoreManagerInterface
    {
        $storeMap = [];
        $stores = [];
        foreach (self::STORES as $code => $data) {
            $stores[$code] = $this->createConfiguredMock(
                Store::class,
                [
                    'getId' => $data['id'],
                    'getCode' => $code,
                    'getWebsiteId' => $data['website_id'],
                    'getStoreGroupId' => $data['group_id'],
                    'getDefaultCurrencyCode' => $data['currency'],
                    'isUseStoreInUrl' => $useStoreInUrl,
                ]
            );
            $storeMap[] = [$code, $stores[$code]];
            $storeMap[] = [$data['id'], $stores[$code]];
        }
        $storeMap[] = [null, $stores['second_en']];

        $websiteMap = [];
        foreach (self::WEBSITE_DEFAULT_GROUP_IDS as $websiteId => $groupId) {
            $websiteMap[] = [
                $websiteId,
                $this->createConfiguredMock(WebsiteInterface::class, ['getDefaultGroupId' => $groupId]),
            ];
        }

        $groupMap = [];
        foreach (self::GROUP_DEFAULT_STORE_IDS as $groupId => $storeId) {
            $groupMap[] = [
                $groupId,
                $this->createConfiguredMock(GroupInterface::class, ['getDefaultStoreId' => $storeId]),
            ];
        }

        $storeManager = $this->createMock(StoreManagerInterface::class);
        $storeManager->method('getStore')->willReturnMap($storeMap);
        $storeManager->method('getWebsite')->willReturnMap($websiteMap);
        $storeManager->method('getGroup')->willReturnMap($groupMap);
        $storeManager->method('getDefaultStoreView')->willReturn($stores['default']);

        return $storeManager;
    }
}
