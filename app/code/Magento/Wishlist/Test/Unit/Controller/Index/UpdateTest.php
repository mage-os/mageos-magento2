<?php
/**
 * Copyright 2019 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Wishlist\Test\Unit\Controller\Index;

use Magento\Backend\Model\View\Result\Redirect;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Wishlist\Controller\Index\Update;
use Magento\Wishlist\Controller\WishlistProviderInterface;
use Magento\Wishlist\Helper\Data;
use Magento\Wishlist\Model\Item;
use Magento\Wishlist\Model\LocaleQuantityProcessor;
use Magento\Wishlist\Model\Wishlist;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Test for upate controller wishlist
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class UpdateTest extends TestCase
{
    private const STUB_ITEM_ID = 1;

    private const STUB_WISHLIST_PRODUCT_QTY = 21;

    /**
     * @var MockObject|Validator $formKeyValidatorMock
     */
    private $formKeyValidatorMock;

    /**
     * @var MockObject|WishlistProviderInterface $wishlistProviderMock
     */
    private $wishlistProviderMock;

    /**
     * @var MockObject|LocaleQuantityProcessor $quantityProcessorMock
     */
    private $quantityProcessorMock;

    /**
     * @var Update $updateController
     */
    private $updateController;

    /**
     * @var MockObject|Context $contextMock
     */
    private $contextMock;

    /**
     * @var MockObject|Redirect $resultRedirectMock
     */
    private $resultRedirectMock;

    /**
     * @var MockObject|ResultFactory $resultFactoryMock
     */
    private $resultFactoryMock;

    /**
     * @var MockObject|RequestInterface $requestMock
     */
    private $requestMock;

    /**
     * @var MockObject|ObjectManagerInterface $objectManagerMock
     */
    private $objectManagerMock;

    /**
     * @var MockObject|ManagerInterface $messageManagerMock
     */
    private $messageManagerMock;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->formKeyValidatorMock = $this->createMock(Validator::class);
        $this->wishlistProviderMock = $this->createMock(WishlistProviderInterface::class);
        $this->quantityProcessorMock = $this->createMock(LocaleQuantityProcessor::class);
        $this->contextMock = $this->createMock(Context::class);
        $this->resultRedirectMock = $this->createMock(Redirect::class);
        $this->resultFactoryMock = $this->createPartialMock(
            ResultFactory::class,
            ['create']
        );
        $this->messageManagerMock = $this->createPartialMock(
            \Magento\Framework\Message\Manager::class,
            ['addSuccessMessage', 'addErrorMessage']
        );
        $this->objectManagerMock = $this->createMock(ObjectManagerInterface::class);
        $this->requestMock = $this->createPartialMock(
            \Magento\Framework\App\Request\Http::class,
            ['getPostValue']
        );

        $this->resultFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($this->resultRedirectMock);
        $this->contextMock->expects($this->once())
            ->method('getResultFactory')
            ->willReturn($this->resultFactoryMock);
        $this->contextMock->expects($this->once())
            ->method('getObjectManager')
            ->willReturn($this->objectManagerMock);
        $this->contextMock->expects($this->any())
            ->method('getRequest')
            ->willReturn($this->requestMock);
        $this->contextMock->expects($this->any())
            ->method('getMessageManager')
            ->willReturn($this->messageManagerMock);

        $this->updateController = new Update(
            $this->contextMock,
            $this->formKeyValidatorMock,
            $this->wishlistProviderMock,
            $this->quantityProcessorMock
        );
    }

    /**
     * Test for update method Wishlist controller.
     *
     * @param  array $wishlistDataProvider
     * @param  array $postData
     * @return void
     */
    #[DataProvider('getWishlistDataProvider')]
    public function testUpdate(array $wishlistDataProvider, array $postData): void
    {
        $wishlist = $this->createMock(Wishlist::class);
        $itemMock = $this->createItemMock($wishlistDataProvider['id']);
        $dataMock = $this->createMock(Data::class);
        $productMock = $this->createMock(Product::class);

        $itemMock->setProduct($productMock);

        $this->formKeyValidatorMock->expects($this->once())
            ->method('validate')
            ->with($this->requestMock)
            ->willReturn(true);
        $this->wishlistProviderMock->expects($this->once())
            ->method('getWishlist')
            ->willReturn($wishlist);
        $wishlist->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($wishlistDataProvider['id']);
        $this->requestMock->expects($this->once())
            ->method('getPostValue')
            ->willReturn($postData);
        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*', ['wishlist_id' => $wishlistDataProvider['id']]);
        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with(Item::class)
            ->willReturn($itemMock);

        $this->objectManagerMock->expects($this->exactly(2))
            ->method('get')
            ->with(Data::class)
            ->willReturn($dataMock);
        $dataMock->expects($this->once())
            ->method('defaultCommentString')
            ->willReturn('');
        $dataMock->expects($this->once())
            ->method('calculate');
        $this->quantityProcessorMock->expects($this->once())
            ->method('process')
            ->willReturn($postData['qty']);

        $productMock->expects($this->once())
            ->method('getName')
            ->willReturn('product');
        $this->messageManagerMock->expects($this->once())
            ->method('addSuccessMessage');

        $this->assertEquals($this->resultRedirectMock, $this->updateController->execute());
    }

    /**
     * Verify update method if post data not available
     *
     * @param  array $wishlistDataProvider
     * @param  array $_postData
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    #[DataProvider('getWishlistDataProvider')]
    public function testUpdateRedirectWhenNoPostData(array $wishlistDataProvider, array $_postData): void
    {
        $wishlist = $this->createMock(Wishlist::class);

        $this->formKeyValidatorMock->expects($this->once())
            ->method('validate')
            ->willReturn(true);
        $this->wishlistProviderMock->expects($this->once())
            ->method('getWishlist')
            ->willReturn($wishlist);
        $wishlist->expects($this->exactly(1))
            ->method('getId')
            ->willReturn($wishlistDataProvider['id']);
        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*', ['wishlist_id' => $wishlistDataProvider['id']]);
        $this->requestMock->expects($this->once())
            ->method('getPostValue')
            ->willReturn(null);

        $this->assertEquals($this->resultRedirectMock, $this->updateController->execute());
    }

    /**
     * Check if wishlist not availbale, and exception is shown
     *
     * @return void
     */
    public function testUpdateThrowsNotFoundExceptionWhenWishlistDoNotExist(): void
    {
        $this->formKeyValidatorMock->expects($this->once())
            ->method('validate')
            ->willReturn(true);
        $this->wishlistProviderMock->expects($this->once())
            ->method('getWishlist')
            ->willReturn(null);
        $this->expectException(NotFoundException::class);
        $this->updateController->execute();
    }

    /**
     * Dataprovider for Update test
     *
     * @return array
     */
    public static function getWishlistDataProvider(): array
    {
        return
            [
                [
                    [
                        'id' => self::STUB_ITEM_ID
                    ],
                    [
                        'qty' => [self::STUB_ITEM_ID => self::STUB_WISHLIST_PRODUCT_QTY],
                        'description' => [self::STUB_ITEM_ID => 'Description for item_id 1']
                    ]
                ]
            ];
    }

    private function createItemMock($id)
    {
        // Create Item mock with specific methods
        $item = $this->createPartialMock(Item::class, ['_getResource', 'save']);
        
        // Use reflection to set up the data storage
        $reflection = new \ReflectionClass($item);
        $dataProperty = $reflection->getProperty('_data');
        $dataProperty->setAccessible(true);
        $dataProperty->setValue($item, []);
        
        // Set up resource mock
        $resourceMock = $this->createMock(\Magento\Wishlist\Model\ResourceModel\Item::class);
        $item->method('_getResource')->willReturn($resourceMock);
        $item->method('save')->willReturn($item);
        
        $item->setId($id);
        // Set up data so that getQty() and getDescription() return different values
        // This will trigger the save operation in the controller
        $item->setData('qty', 1); // Different from test qty (21)
        $item->setData('description', 'old_description'); // Different from test description
        $item->setData('wishlist_id', 1); // Set wishlist ID to match the test
        return $item;
    }
}
