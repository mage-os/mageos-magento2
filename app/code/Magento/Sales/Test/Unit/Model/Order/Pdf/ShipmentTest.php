<?php
/**
 * Copyright 2020 Adobe
 * All Rights Reserved.
 */
namespace Magento\Sales\Test\Unit\Model\Order\Pdf;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\MediaStorage\Helper\File\Storage\Database;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Sales\Model\Order\Shipment;
use Magento\Store\Model\ScopeInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\MockCreationTrait;

/**
 * Class ShipmentTest
 *
 * Tests Sales Order Shipment PDF model
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ShipmentTest extends TestCase
{
    use MockCreationTrait;

    /**
     * @var \Magento\Sales\Model\Order\Pdf\Invoice
     */
    protected $model;

    /**
     * @var \Magento\Sales\Model\Order\Pdf\Config|MockObject
     */
    protected $pdfConfigMock;

    /**
     * @var Database|MockObject
     */
    protected $databaseMock;

    /**
     * @var ScopeConfigInterface|MockObject
     */
    protected $scopeConfigMock;

    /**
     * @var \Magento\Framework\Filesystem\Directory\Write|MockObject
     */
    protected $directoryMock;

    /**
     * @var Renderer|MockObject
     */
    protected $addressRendererMock;

    /**
     * @var \Magento\Payment\Helper\Data|MockObject
     */
    protected $paymentDataMock;

    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    private $appEmulation;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->pdfConfigMock = $this->getMockBuilder(\Magento\Sales\Model\Order\Pdf\Config::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->directoryMock = $this->createMock(\Magento\Framework\Filesystem\Directory\Write::class);
        $this->directoryMock->expects($this->any())->method('getAbsolutePath')->willReturnCallback(
            function ($argument) {
                return BP . '/' . $argument;
            }
        );
        $filesystemMock = $this->createMock(\Magento\Framework\Filesystem::class);
        $filesystemMock->expects($this->any())
            ->method('getDirectoryRead')
            ->willReturn($this->directoryMock);
        $filesystemMock->expects($this->any())
            ->method('getDirectoryWrite')
            ->willReturn($this->directoryMock);

        $this->databaseMock = $this->createMock(Database::class);
        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);
        $this->addressRendererMock = $this->createMock(Renderer::class);
        $this->paymentDataMock = $this->createMock(\Magento\Payment\Helper\Data::class);
        $this->appEmulation = $this->createMock(\Magento\Store\Model\App\Emulation::class);

        $helper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->model = $helper->getObject(
            \Magento\Sales\Model\Order\Pdf\Shipment::class,
            [
                'filesystem' => $filesystemMock,
                'pdfConfig' => $this->pdfConfigMock,
                'fileStorageDatabase' => $this->databaseMock,
                'scopeConfig' => $this->scopeConfigMock,
                'addressRenderer' => $this->addressRendererMock,
                'string' => new \Magento\Framework\Stdlib\StringUtils(),
                'paymentData' => $this->paymentDataMock,
                'appEmulation' => $this->appEmulation
            ]
        );
    }

    /**
     * @return void
     */
    public function testInsertLogoDatabaseMediaStorage(): void
    {
        $filename = 'image.jpg';
        $path = '/sales/store/logo/';
        $storeId = 1;

        $this->appEmulation->expects($this->once())
            ->method('startEnvironmentEmulation')
            ->with(
                $storeId,
                \Magento\Framework\App\Area::AREA_FRONTEND,
                true
            )
            ->willReturnSelf();
        $this->appEmulation->expects($this->once())
            ->method('stopEnvironmentEmulation')
            ->willReturnSelf();
        $this->pdfConfigMock->expects($this->once())
            ->method('getRenderersPerProduct')
            ->with('shipment')
            ->willReturn(['product_type_one' => 'Renderer_Type_One_Product_One']);
        $this->pdfConfigMock->expects($this->any())
            ->method('getTotals')
            ->willReturn([]);

        $block = $this->createPartialMockWithReflection(\Magento\Framework\View\Element\Template::class, ['setIsSecureMode', 'toPdf']);
        $block->expects($this->any())
            ->method('setIsSecureMode')
            ->willReturn($block);
        $block->expects($this->any())
            ->method('toPdf')
            ->willReturn('');
        $this->paymentDataMock->expects($this->any())
            ->method('getInfoBlock')
            ->willReturn($block);

        $this->addressRendererMock->expects($this->any())
            ->method('format')
            ->willReturn('');

        $this->databaseMock->expects($this->any())
            ->method('checkDbUsage')
            ->willReturn(true);

        $shipmentMock = $this->createMock(Shipment::class);
        $orderMock = $this->createMock(Order::class);
        $addressMock = $this->createMock(Address::class);
        $orderMock->expects($this->any())
            ->method('getBillingAddress')
            ->willReturn($addressMock);
        $orderMock->expects($this->any())
            ->method('getIsVirtual')
            ->willReturn(true);
        $infoMock = $this->createMock(\Magento\Payment\Model\InfoInterface::class);
        $orderMock->expects($this->any())
            ->method('getPayment')
            ->willReturn($infoMock);
        $shipmentMock->expects($this->any())
            ->method('getStoreId')
            ->willReturn($storeId);
        $shipmentMock->expects($this->any())
            ->method('getOrder')
            ->willReturn($orderMock);
        $shipmentMock->expects($this->any())
            ->method('getAllItems')
            ->willReturn([]);

        $this->scopeConfigMock
            ->method('getValue')
            ->willReturnCallback(function ($arg1, $arg2, $arg3) use ($filename) {
                if ($arg1 == 'sales/identity/logo' && $arg2 == ScopeInterface::SCOPE_STORE && is_null($arg3)) {
                    return $filename;
                } elseif ($arg1 == 'sales/identity/address' && $arg2 == ScopeInterface::SCOPE_STORE && is_null($arg3)) {
                    return null;
                }
            });

        $this->directoryMock->expects($this->any())
            ->method('isFile')
            ->with($path . $filename)
            ->willReturnOnConsecutiveCalls(false, false);

        $this->databaseMock->expects($this->once())
            ->method('saveFileToFilesystem')
            ->with($path . $filename);

        $this->model->getPdf([$shipmentMock]);
    }
}
