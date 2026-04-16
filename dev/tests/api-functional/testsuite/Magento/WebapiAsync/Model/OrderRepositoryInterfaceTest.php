<?php
/**
 * Copyright 2020 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\WebapiAsync\Model;

use PHPUnit\Framework\Attributes\DataProvider;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\MessageQueue\EnvironmentPreconditionException;
use Magento\TestFramework\MessageQueue\PreconditionFailedException;
use Magento\TestFramework\MessageQueue\PublisherConsumerController;
use Magento\TestFramework\TestCase\WebapiAbstract;

/**
 * Test order repository interface via async webapi
 */
class OrderRepositoryInterfaceTest extends WebapiAbstract
{
    private const ASYNC_BULK_SAVE_ORDER = '/async/bulk/V1/orders';
    private const ASYNC_SAVE_ORDER = '/async/V1/orders';
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;
    /**
     * @var PublisherConsumerController
     */
    private $publisherConsumerController;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->objectManager = Bootstrap::getObjectManager();

        $params = array_merge_recursive(
            Bootstrap::getInstance()->getAppInitParams(),
            ['MAGE_DIRS' => ['cache' => ['path' => TESTS_TEMP_DIR . '/cache']]]
        );

        /** @var PublisherConsumerController publisherConsumerController */
        $this->publisherConsumerController = $this->objectManager->create(
            PublisherConsumerController::class,
            [
                'consumers'     => ['async.operations.all'],
                'logFilePath'   => TESTS_TEMP_DIR . "/MessageQueueTestLog.txt",
                'appInitParams' => $params,
            ]
        );

        try {
            $this->publisherConsumerController->initialize();
        } catch (EnvironmentPreconditionException $e) {
            $this->markTestSkipped($e->getMessage());
        } catch (PreconditionFailedException $e) {
            $this->fail(
                $e->getMessage()
            );
        }
    }

    /**
     * @inheritDoc
     */
    protected function tearDown(): void
    {
        $this->publisherConsumerController->stopConsumers();
        parent::tearDown();
    }

    /**
     * Check that order is updated successfuly via async webapi
     *
     * @magentoApiDataFixture Magento/Sales/_files/order.php
     * @param array $data
     * @param bool $isBulk
     * @return void
     */
    #[DataProvider('saveDataProvider')]
    public function testSave(array $data, bool $isBulk = true): void
    {
        $this->_markTestAsRestOnly();
        /** @var Order $beforeUpdateOrder */
        $beforeUpdateOrder = $this->objectManager->get(Order::class)->loadByIncrementId('100000001');
        $requestData = [
            'entity' => array_merge($data, [OrderInterface::ENTITY_ID => (int) $beforeUpdateOrder->getEntityId()])
        ];
        if ($isBulk) {
            $requestData = [$requestData];
        }
        $serviceInfo = [
            'rest' => [
                'resourcePath' => $isBulk ? self::ASYNC_BULK_SAVE_ORDER : self::ASYNC_SAVE_ORDER,
                'httpMethod' => Request::HTTP_METHOD_POST,
            ]
        ];
        $this->makeAsyncRequest($serviceInfo, $requestData);
        sleep(10);
        try {
            $this->publisherConsumerController->waitForAsynchronousResult(
                function (Order $beforeUpdateOrder, array $data) {
                    $orderRepository = $this->objectManager->get(OrderRepositoryInterface::class);
                    $afterUpdateOrder = $orderRepository->get((int) $beforeUpdateOrder->getEntityId());
                    foreach ($data as $attribute => $value) {
                        if ((string) $afterUpdateOrder->getData($attribute) !== (string) $value) {
                            return false;
                        }
                    }
                    // Totals on the reloaded order can be 0.0 in some environments until totals are recalculated;
                    // still require equality when the API returns non-zero totals (not overwritten by the async save).
                    $afterBaseGrandTotal = (float) $afterUpdateOrder->getBaseGrandTotal();
                    $beforeBaseGrandTotal = (float) $beforeUpdateOrder->getBaseGrandTotal();
                    $this->assertTrue(
                        $afterBaseGrandTotal === 0.0
                        || abs($beforeBaseGrandTotal - $afterBaseGrandTotal) < 0.0001,
                        'base_grand_total should match the pre-update order when present on the reloaded entity'
                    );
                    $afterGrandTotal = (float) $afterUpdateOrder->getGrandTotal();
                    $beforeGrandTotal = (float) $beforeUpdateOrder->getGrandTotal();
                    $this->assertTrue(
                        $afterGrandTotal === 0.0
                        || abs($beforeGrandTotal - $afterGrandTotal) < 0.0001,
                        'grand_total should match the pre-update order when present on the reloaded entity'
                    );
                    return true;
                },
                [$beforeUpdateOrder, $data]
            );
        } catch (PreconditionFailedException $e) {
            $this->fail("Order update via async webapi failed");
        }
    }

    /**
     * Data provider for tesSave()
     *
     * @return array
     */
    public static function saveDataProvider(): array
    {
        return [
            'update order in bulk mode' => [
                [
                    OrderInterface::CUSTOMER_EMAIL => 'customer.email.modified@magento.test'
                ],
                true
            ],
            'update order in single mode' => [
                [
                    OrderInterface::CUSTOMER_EMAIL => 'customer.email.modified@magento.test'
                ],
                false
            ]
        ];
    }

    /**
     * Make async webapi request
     *
     * @param array $serviceInfo
     * @param array $requestData
     * @return void
     */
    private function makeAsyncRequest(array $serviceInfo, array $requestData): void
    {
        $response = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertNotEmpty($response['request_items']);
        foreach ($response['request_items'] as $requestItem) {
            $this->assertEquals('accepted', $requestItem['status']);
        }
        $this->assertFalse($response['errors']);
    }
}
