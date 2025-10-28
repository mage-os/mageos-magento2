<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Block\Address;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Block\Address\Edit;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Customer\Model\Session;
use Magento\Customer\Test\Unit\Helper\SessionTestHelper;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Page\Title;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EditTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var RequestInterface|MockObject
     */
    protected $requestMock;

    /**
     * @var AddressRepositoryInterface|MockObject
     */
    protected $addressRepositoryMock;

    /**
     * @var Session|MockObject
     */
    protected $customerSessionMock;

    /**
     * @var Config|MockObject
     */
    protected $pageConfigMock;

    /**
     * @var DataObjectHelper|MockObject
     */
    protected $dataObjectHelperMock;

    /**
     * @var AddressInterfaceFactory|MockObject
     */
    protected $addressDataFactoryMock;

    /**
     * @var CurrentCustomer|MockObject
     */
    protected $currentCustomerMock;

    /**
     * @var Edit
     */
    protected $model;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);

        $this->requestMock = $this->createMock(RequestInterface::class);

        $this->addressRepositoryMock = $this->createMock(AddressRepositoryInterface::class);

        $this->customerSessionMock = new SessionTestHelper();

        $this->pageConfigMock = $this->createMock(Config::class);

        $this->dataObjectHelperMock = $this->createMock(DataObjectHelper::class);

        $this->addressDataFactoryMock = $this->createPartialMock(
            AddressInterfaceFactory::class,
            ['create']
        );

        $this->currentCustomerMock = $this->createMock(CurrentCustomer::class);

        $this->model = $this->objectManager->getObject(
            Edit::class,
            [
                'request' => $this->requestMock,
                'addressRepository' => $this->addressRepositoryMock,
                'customerSession' => $this->customerSessionMock,
                'pageConfig' => $this->pageConfigMock,
                'dataObjectHelper' => $this->dataObjectHelperMock,
                'addressDataFactory' => $this->addressDataFactoryMock,
                'currentCustomer' => $this->currentCustomerMock
            ]
        );
    }

    /**
     * @return void
     */
    public function testSetLayoutWithOwnAddressAndPostedData(): void
    {
        $addressId = 1;
        $customerId = 1;
        $title = __('Edit Address');
        $postedData = [
            'region_id' => 1,
            'region' => 'region'
        ];
        $newPostedData = $postedData;
        $newPostedData['region'] = $postedData;

        $layoutMock = $this->getMockBuilder(LayoutInterface::class)
            ->getMock();

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('id', null)
            ->willReturn($addressId);

        $addressMock = $this->getMockBuilder(AddressInterface::class)
            ->getMock();
        $this->addressRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($addressId)
            ->willReturn($addressMock);

        $addressMock->expects($this->once())
            ->method('getCustomerId')
            ->willReturn($customerId);

        $addressMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($addressId);

        $pageTitleMock = $this->createMock(Title::class);
        $this->pageConfigMock->expects($this->once())
            ->method('getTitle')
            ->willReturn($pageTitleMock);

        $pageTitleMock->expects($this->once())
            ->method('set')
            ->with($title)
            ->willReturnSelf();

        $this->customerSessionMock->setCustomerId($customerId);
        $this->customerSessionMock->setAddressFormData($postedData);

        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with(
                $addressMock,
                $newPostedData,
                AddressInterface::class
            )->willReturnSelf();

        $this->assertEquals($this->model, $this->model->setLayout($layoutMock));
        $this->assertEquals($layoutMock, $this->model->getLayout());
    }

    /**
     * @return void
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testSetLayoutWithAlienAddress(): void
    {
        $addressId = 1;
        $customerId = 1;
        $customerPrefix = 'prefix';
        $customerFirstName = 'firstname';
        $customerMiddlename = 'middlename';
        $customerLastname = 'lastname';
        $customerSuffix = 'suffix';
        $title = __('Add New Address');

        $layoutMock = $this->createMock(LayoutInterface::class);

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('id', null)
            ->willReturn($addressId);

        $addressMock = $this->createMock(AddressInterface::class);
        $this->addressRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($addressId)
            ->willReturn($addressMock);

        $addressMock->expects($this->once())
            ->method('getCustomerId')
            ->willReturn($customerId);

        $this->customerSessionMock->setCustomerId($customerId + 1);

        $newAddressMock = $this->createMock(AddressInterface::class);
        $this->addressDataFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($newAddressMock);

        $customerMock = $this->createMock(CustomerInterface::class);
        $this->currentCustomerMock->expects($this->once())
            ->method('getCustomer')
            ->willReturn($customerMock);

        $customerMock->expects($this->once())
            ->method('getPrefix')
            ->willReturn($customerPrefix);
        $customerMock->expects($this->once())
            ->method('getFirstname')
            ->willReturn($customerFirstName);
        $customerMock->expects($this->once())
            ->method('getMiddlename')
            ->willReturn($customerMiddlename);
        $customerMock->expects($this->once())
            ->method('getLastname')
            ->willReturn($customerLastname);
        $customerMock->expects($this->once())
            ->method('getSuffix')
            ->willReturn($customerSuffix);

        $newAddressMock->expects($this->once())
            ->method('setPrefix')
            ->with($customerPrefix)
            ->willReturnSelf();
        $newAddressMock->expects($this->once())
            ->method('setFirstname')
            ->with($customerFirstName)
            ->willReturnSelf();
        $newAddressMock->expects($this->once())
            ->method('setMiddlename')
            ->with($customerMiddlename)
            ->willReturnSelf();
        $newAddressMock->expects($this->once())
            ->method('setLastname')
            ->with($customerLastname)
            ->willReturnSelf();
        $newAddressMock->expects($this->once())
            ->method('setSuffix')
            ->with($customerSuffix)
            ->willReturnSelf();

        $newAddressMock->expects($this->once())
            ->method('getId')
            ->willReturn(null);

        $pageTitleMock = $this->createMock(Title::class);
        $this->pageConfigMock->expects($this->once())
            ->method('getTitle')
            ->willReturn($pageTitleMock);

        $pageTitleMock->expects($this->once())
            ->method('set')
            ->with($title)
            ->willReturnSelf();

        $this->assertEquals($this->model, $this->model->setLayout($layoutMock));
        $this->assertEquals($layoutMock, $this->model->getLayout());
    }

    /**
     * @return void
     */
    public function testSetLayoutWithoutAddressId(): void
    {
        $customerPrefix = 'prefix';
        $customerFirstName = 'firstname';
        $customerMiddlename = 'middlename';
        $customerLastname = 'lastname';
        $customerSuffix = 'suffix';
        $title = 'title';

        $layoutMock = $this->createMock(LayoutInterface::class);

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('id', null)
            ->willReturn('');

        $newAddressMock = $this->createMock(AddressInterface::class);
        $this->addressDataFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($newAddressMock);

        $customerMock = $this->createMock(CustomerInterface::class);
        $this->currentCustomerMock->expects($this->once())
            ->method('getCustomer')
            ->willReturn($customerMock);

        $customerMock->expects($this->once())
            ->method('getPrefix')
            ->willReturn($customerPrefix);
        $customerMock->expects($this->once())
            ->method('getFirstname')
            ->willReturn($customerFirstName);
        $customerMock->expects($this->once())
            ->method('getMiddlename')
            ->willReturn($customerMiddlename);
        $customerMock->expects($this->once())
            ->method('getLastname')
            ->willReturn($customerLastname);
        $customerMock->expects($this->once())
            ->method('getSuffix')
            ->willReturn($customerSuffix);

        $newAddressMock->expects($this->once())
            ->method('setPrefix')
            ->with($customerPrefix)
            ->willReturnSelf();
        $newAddressMock->expects($this->once())
            ->method('setFirstname')
            ->with($customerFirstName)
            ->willReturnSelf();
        $newAddressMock->expects($this->once())
            ->method('setMiddlename')
            ->with($customerMiddlename)
            ->willReturnSelf();
        $newAddressMock->expects($this->once())
            ->method('setLastname')
            ->with($customerLastname)
            ->willReturnSelf();
        $newAddressMock->expects($this->once())
            ->method('setSuffix')
            ->with($customerSuffix)
            ->willReturnSelf();

        $pageTitleMock = $this->getMockBuilder(Title::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->pageConfigMock->expects($this->once())
            ->method('getTitle')
            ->willReturn($pageTitleMock);

        $this->model->setData('title', $title);

        $pageTitleMock->expects($this->once())
            ->method('set')
            ->with($title)
            ->willReturnSelf();

        $this->assertEquals($this->model, $this->model->setLayout($layoutMock));
        $this->assertEquals($layoutMock, $this->model->getLayout());
    }

    /**
     * @return void
     */
    public function testSetLayoutWithoutAddress(): void
    {
        $addressId = 1;
        $customerPrefix = 'prefix';
        $customerFirstName = 'firstname';
        $customerMiddlename = 'middlename';
        $customerLastname = 'lastname';
        $customerSuffix = 'suffix';
        $title = 'title';

        $layoutMock = $this->createMock(LayoutInterface::class);

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('id', null)
            ->willReturn($addressId);

        $this->addressRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($addressId)
            ->willThrowException(
                NoSuchEntityException::singleField('addressId', $addressId)
            );

        $newAddressMock = $this->createMock(AddressInterface::class);
        $this->addressDataFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($newAddressMock);

        $customerMock = $this->createMock(CustomerInterface::class);
        $this->currentCustomerMock->expects($this->once())
            ->method('getCustomer')
            ->willReturn($customerMock);

        $customerMock->expects($this->once())
            ->method('getPrefix')
            ->willReturn($customerPrefix);
        $customerMock->expects($this->once())
            ->method('getFirstname')
            ->willReturn($customerFirstName);
        $customerMock->expects($this->once())
            ->method('getMiddlename')
            ->willReturn($customerMiddlename);
        $customerMock->expects($this->once())
            ->method('getLastname')
            ->willReturn($customerLastname);
        $customerMock->expects($this->once())
            ->method('getSuffix')
            ->willReturn($customerSuffix);

        $newAddressMock->expects($this->once())
            ->method('setPrefix')
            ->with($customerPrefix)
            ->willReturnSelf();
        $newAddressMock->expects($this->once())
            ->method('setFirstname')
            ->with($customerFirstName)
            ->willReturnSelf();
        $newAddressMock->expects($this->once())
            ->method('setMiddlename')
            ->with($customerMiddlename)
            ->willReturnSelf();
        $newAddressMock->expects($this->once())
            ->method('setLastname')
            ->with($customerLastname)
            ->willReturnSelf();
        $newAddressMock->expects($this->once())
            ->method('setSuffix')
            ->with($customerSuffix)
            ->willReturnSelf();

        $pageTitleMock = $this->getMockBuilder(Title::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->pageConfigMock->expects($this->once())
            ->method('getTitle')
            ->willReturn($pageTitleMock);

        $this->model->setData('title', $title);

        $pageTitleMock->expects($this->once())
            ->method('set')
            ->with($title)
            ->willReturnSelf();

        $this->assertEquals($this->model, $this->model->setLayout($layoutMock));
        $this->assertEquals($layoutMock, $this->model->getLayout());
    }
}
