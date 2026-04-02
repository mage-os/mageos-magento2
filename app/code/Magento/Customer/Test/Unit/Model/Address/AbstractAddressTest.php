<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Model\Address;

use Magento\Customer\Model\Address\AbstractAddress;
use Magento\Customer\Model\Address\CompositeValidator;
use Magento\Customer\Model\ResourceModel\Customer;
use Magento\Directory\Helper\Data;
use Magento\Directory\Model\Country;
use Magento\Directory\Model\CountryFactory;
use Magento\Directory\Model\Region;
use Magento\Directory\Model\RegionFactory;
use Magento\Directory\Model\ResourceModel\Region\Collection;
use Magento\Eav\Model\Config;
use Magento\Framework\Api\AttributeInterface;
use Magento\Framework\Api\AttributeValue;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\Customer\Model\Address\AbstractAddress\RegionModelsCache;
use Magento\Customer\Model\Address\AbstractAddress\CountryModelsCache;
use Magento\Framework\TestFramework\Unit\Helper\MockCreationTrait;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AbstractAddressTest extends TestCase
{
    use MockCreationTrait;

    /** @var Context|MockObject  */
    protected $contextMock;

    /** @var Registry|MockObject  */
    protected $registryMock;

    /** @var Data|MockObject  */
    protected $directoryDataMock;

    /** @var Config|MockObject  */
    protected $eavConfigMock;

    /** @var \Magento\Customer\Model\Address\Config|MockObject  */
    protected $addressConfigMock;

    /** @var RegionFactory|MockObject  */
    protected $regionFactoryMock;

    /** @var CountryFactory|MockObject  */
    protected $countryFactoryMock;

    /** @var Customer|MockObject  */
    protected $resourceMock;

    /** @var AbstractDb|MockObject  */
    protected $resourceCollectionMock;

    /** @var AbstractAddress  */
    protected $model;

    /** @var ObjectManager */
    private $objectManager;

    /** @var CompositeValidator|MockObject  */
    private $compositeValidatorMock;

    /**
     * @var \Magento\Customer\Helper\Address|MockObject
     */
    private $addressHelperMock;

    protected function setUp(): void
    {
        $eventManagerMock = $this->getMockForAbstractClass(\Magento\Framework\Event\ManagerInterface::class);
        $this->contextMock = $this->createMock(Context::class);
        $this->contextMock->method('getEventDispatcher')->willReturn($eventManagerMock);
        $this->registryMock = $this->createMock(Registry::class);
        $this->directoryDataMock = $this->createMock(Data::class);
        $this->eavConfigMock = $this->createMock(Config::class);
        $this->addressConfigMock = $this->createMock(\Magento\Customer\Model\Address\Config::class);
        $this->regionFactoryMock = $this->createPartialMock(RegionFactory::class, ['create']);
        $this->countryFactoryMock = $this->createPartialMock(
            CountryFactory::class,
            ['create']
        );
        $regionCollectionMock = $this->createMock(Collection::class);
        $regionCollectionMock->expects($this->any())
            ->method('getSize')
            ->willReturn(0);
        $countryMock = $this->createMock(Country::class);
        $countryMock->expects($this->any())
            ->method('getRegionCollection')
            ->willReturn($regionCollectionMock);
        $this->countryFactoryMock->expects($this->any())
            ->method('create')
            ->willReturn($countryMock);

        $this->resourceMock = $this->createMock(Customer::class);
        $this->resourceCollectionMock = $this->createMock(AbstractDb::class);
        $this->objectManager = new ObjectManager($this);
        $this->compositeValidatorMock = $this->createMock(CompositeValidator::class);
        $this->model = $this->objectManager->getObject(
            AbstractAddress::class,
            [
                'context' => $this->contextMock,
                'registry' => $this->registryMock,
                'directoryData' => $this->directoryDataMock,
                'eavConfig' => $this->eavConfigMock,
                'addressConfig' => $this->addressConfigMock,
                'regionFactory' => $this->regionFactoryMock,
                'countryFactory' => $this->countryFactoryMock,
                'resource' => $this->resourceMock,
                'resourceCollection' => $this->resourceCollectionMock,
                'compositeValidator' => $this->compositeValidatorMock,
                'countryModelsCache' => new CountryModelsCache,
                'regionModelsCache' => new RegionModelsCache,
            ]
        );
    }

    public function testGetRegionWithRegionId()
    {
        $countryId = 1;
        $this->prepareGetRegion($countryId);

        $this->model->setData('region_id', 1);
        $this->model->setData('region', '');
        $this->model->setData('country_id', $countryId);
        $this->assertEquals('RegionName', $this->model->getRegion());
    }

    public function testGetRegionWithRegion()
    {
        $countryId = 2;
        $this->prepareGetRegion($countryId);

        $this->model->setData('region_id', '');
        $this->model->setData('region', 2);
        $this->model->setData('country_id', $countryId);
        $this->assertEquals('RegionName', $this->model->getRegion());
    }

    public function testGetRegionWithRegionName()
    {
        $this->regionFactoryMock->expects($this->never())->method('create');

        $this->model->setData('region_id', '');
        $this->model->setData('region', 'RegionName');
        $this->assertEquals('RegionName', $this->model->getRegion());
    }

    public function testGetRegionWithoutRegion()
    {
        $this->regionFactoryMock->expects($this->never())->method('create');

        $this->assertNull($this->model->getRegion());
    }

    public function testGetRegionCodeWithRegionId()
    {
        $countryId = 1;
        $this->prepareGetRegionCode($countryId);

        $this->model->setData('region_id', 3);
        $this->model->setData('region', '');
        $this->model->setData('country_id', $countryId);
        $this->assertEquals('UK', $this->model->getRegionCode());
    }

    /**
     * Test regionid for empty value
     *
     * @inheritdoc
     * @return void
     */
    public function testGetRegionId()
    {
        $this->model->setData('region_id', 0);
        $this->model->setData('region', '');
        $this->model->setData('country_id', 'GB');
        $region = $this->createPartialMockWithReflection(
            Region::class,
            ['getCountryId', 'getCode', '__wakeup', 'load', 'loadByCode', 'getId']
        );
        $region->method('loadByCode')
            ->willReturnSelf();
        $this->regionFactoryMock->method('create')
            ->willReturn($region);
        $this->assertEquals(0, $this->model->getRegionId());
    }

    public function testGetRegionCodeWithRegion()
    {
        $countryId = 2;
        $this->prepareGetRegionCode($countryId);

        $this->model->setData('region_id', '');
        $this->model->setData('region', 4);
        $this->model->setData('country_id', $countryId);
        $this->assertEquals('UK', $this->model->getRegionCode());
    }

    public function testGetRegionCodeWithRegionName()
    {
        $this->regionFactoryMock->expects($this->never())->method('create');

        $this->model->setData('region_id', '');
        $this->model->setData('region', 'UK');
        $this->assertEquals('UK', $this->model->getRegionCode());
    }

    public function testGetRegionCodeWithoutRegion()
    {
        $this->regionFactoryMock->expects($this->never())->method('create');

        $this->assertNull($this->model->getRegionCode());
    }

    /**
     * @param $countryId
     */
    protected function prepareGetRegion($countryId, $regionName = 'RegionName')
    {
        $region = $this->createPartialMockWithReflection(
            Region::class,
            ['getCountryId', 'getName', '__wakeup', 'load']
        );
        $region->expects($this->once())
            ->method('getName')
            ->willReturn($regionName);
        $region->expects($this->once())
            ->method('getCountryId')
            ->willReturn($countryId);
        $this->regionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($region);
    }

    /**
     * @param $countryId
     */
    protected function prepareGetRegionCode($countryId, $regionCode = 'UK')
    {
        $region = $this->createPartialMockWithReflection(
            Region::class,
            ['getCountryId', 'getCode', '__wakeup', 'load', 'loadByCode']
        );
        $region->method('loadByCode')
            ->willReturnSelf();
        $region->expects($this->once())
            ->method('getCode')
            ->willReturn($regionCode);
        $region->expects($this->once())
            ->method('getCountryId')
            ->willReturn($countryId);
        $this->regionFactoryMock->method('create')
            ->willReturn($region);
    }

    /**
     * Test for setData method
     *
     * @return void
     */
    public function testSetData()
    {
        $key = [
            'key' => 'value'
        ];

        $this->model->setData($key);
        $this->assertEquals($key, $this->model->getData());
    }

    /**
     * Test for setData method with multidimensional array in "key" argument
     *
     * @return void
     */
    public function testSetDataWithMultidimensionalArray()
    {
        $expected = [
            'key' => 'value',
            'street' => 'value1',
        ];

        $key = [
            'key' => 'value',
            'street' => [
                'key1' => 'value1',
            ]
        ];

        $this->model->setData($key);
        $this->assertEquals($expected, $this->model->getData());
    }

    /**
     * Test for setData method with "value" argument
     *
     * @return void
     */
    public function testSetDataWithValue()
    {
        $value = [
            'street' => 'value',
        ];

        $this->model->setData('street', $value);
        $this->assertEquals($value, $this->model->getData());
    }

    /**
     * Test for setData method with "value" argument
     *
     * @return void
     */
    public function testSetDataWithObject()
    {
        $value = [
            'key' => new DataObject(),
        ];
        $expected = [
            'key' => [
                'key' => new DataObject()
            ]
        ];
        $this->model->setData('key', $value);
        $this->assertEquals($expected, $this->model->getData());
    }

    /**
     * @param array $data
     * @param array|bool $expected
     * @return void
     * */
    #[DataProvider('validateDataProvider')]
    public function testValidate(array $data, $expected)
    {
        $this->compositeValidatorMock->method('validate')->with($this->model)->willReturn($expected);

        foreach ($data as $key => $value) {
            $this->model->setData($key, $value);
        }

        $actual = $this->model->validate();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array
     */
    public static function validateDataProvider()
    {
        $countryId = 1;
        $data = [
            'firstname' => 'First Name',
            'lastname' => 'Last Name',
            'street' => "Street 1\nStreet 2",
            'city' => 'Odessa',
            'telephone' => '555-55-55',
            'country_id' => $countryId,
            'postcode' => 07201,
            'region_id' => 1,
            'company' => 'Magento',
            'fax' => '222-22-22'
        ];
        return [
            'firstname' => [
                array_merge(array_diff_key($data, ['firstname' => '']), ['country_id' => $countryId++]),
                ['"firstname" is required. Enter and try again.'],
            ],
            'lastname' => [
                array_merge(array_diff_key($data, ['lastname' => '']), ['country_id' => $countryId++]),
                ['"lastname" is required. Enter and try again.'],
            ],
            'street' => [
                array_merge(array_diff_key($data, ['street' => '']), ['country_id' => $countryId++]),
                ['"street" is required. Enter and try again.'],
            ],
            'city' => [
                array_merge(array_diff_key($data, ['city' => '']), ['country_id' => $countryId++]),
                ['"city" is required. Enter and try again.'],
            ],
            'telephone' => [
                array_merge(array_diff_key($data, ['telephone' => '']), ['country_id' => $countryId++]),
                ['"telephone" is required. Enter and try again.'],
            ],
            'postcode' => [
                array_merge(array_diff_key($data, ['postcode' => '']), ['country_id' => $countryId++]),
                ['"postcode" is required. Enter and try again.'],
            ],
            'region_id' => [
                array_merge($data, ['country_id' => $countryId++, 'region_id' => 2]),
                ['Invalid value of "2" provided for the regionId field.'],
            ],
            'country_id' => [
                array_diff_key($data, ['country_id' => '']),
                ['"countryId" is required. Enter and try again.'],
            ],
            'validated' => [array_merge($data, ['country_id' => $countryId++]), true],
        ];
    }

    /** */
    #[DataProvider('getStreetFullDataProvider')]
    public function testGetStreetFullAlwaysReturnsString($expectedResult, $street)
    {
        $this->model->setData('street', $street);
        $this->assertEquals($expectedResult, $this->model->getStreetFull());
    }

    /** */
    #[DataProvider('getStreetFullDataProvider')]
    public function testSetDataStreetAlwaysConvertedToString($expectedResult, $street)
    {
        $this->model->setData('street', $street);
        $this->assertEquals($expectedResult, $this->model->getData('street'));
    }

    /**
     * @return array
     */
    public static function getStreetFullDataProvider()
    {
        return [
            [null, null],
            ['', []],
            ["first line\nsecond line", ['first line', 'second line']],
            ['single line', ['single line']],
            ['single line', 'single line'],
            ['single line', ['single line', null]],
        ];
    }

    /**
     * @return void
     */
    public function testSetCustomerAttributes(): void
    {
        $model = $this->createPartialMock(
            AbstractAddress::class,
            ['getCustomAttributesCodes']
        );
        $customAttributeFactory = $this->createMock(\Magento\Customer\Model\AttributeFactory::class);
        $customAttributeFactory->method('create')
            ->willReturnCallback(
                function ($data) {
                    return new AttributeValue($data);
                }
            );
        $data = [
            'customer_attribute1' => new AttributeValue([
                'attribute_code' => 'customer_attribute1',
                'value' => 'customer_attribute1_value'
            ]),
            'customer_attribute2' => new AttributeValue([
                'attribute_code' => 'customer_attribute2',
                'value' => ['customer_attribute2_value1', 'customer_attribute2_value2']
            ])
        ];
        $model->method('getCustomAttributesCodes')->willReturn(array_keys($data));
        $this->objectManager->setBackwardCompatibleProperty(
            $model,
            'customAttributeFactory',
            $customAttributeFactory
        );
        $model->setData('custom_attributes', $data);
        $this->assertEquals(
            [
                [
                    'attribute_code' => 'customer_attribute1',
                    'value' => 'customer_attribute1_value'
                ],
                [
                    'attribute_code' => 'customer_attribute2',
                    'value' => "customer_attribute2_value1\ncustomer_attribute2_value2"
                ]
            ],
            array_map(
                fn ($attr) => ['attribute_code' => $attr->getAttributeCode(), 'value' => $attr->getValue()],
                $model->getCustomAttributes()
            )
        );
    }

    public function testGetStreetWithTwoLines()
    {
        // Create a partial mock for AddressHelper
        $this->addressHelperMock = $this->getMockBuilder(\Magento\Customer\Helper\Address::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getStreetLines']) // Mock only getStreetLines, keep the real convertStreetLines
            ->getMock();

        // Mock getStreetLines to return 2 by default
        $this->addressHelperMock->method('getStreetLines')->willReturn(2);

        // Use reflection to inject the partial mock into the model
        $reflection = new \ReflectionClass($this->model);
        $property = $reflection->getProperty('addressHelper');
        $property->setAccessible(true);
        $property->setValue($this->model, $this->addressHelperMock);

        $this->addressHelperMock->method('getStreetLines')->willReturn(2);
        $streetData = ["Street Line 1", "Street Line 2", "Street Line 3", "Street Line 4"];
        $this->model->setData('street', $streetData);

        // Call getStreet() which should internally call convertStreetLines()
        $result = $this->model->getStreet();

        // Assert that empty and whitespace-only lines are removed by convertStreetLines
        $this->assertEquals(["Street Line 1 Street Line 2", "Street Line 3 Street Line 4"], $result);
    }

    public function testGetStreetWithThreeLines()
    {
        // Create a partial mock for AddressHelper
        $this->addressHelperMock = $this->getMockBuilder(\Magento\Customer\Helper\Address::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getStreetLines']) // Mock only getStreetLines, keep the real convertStreetLines
            ->getMock();

        // Mock getStreetLines to return 2 by default
        $this->addressHelperMock->method('getStreetLines')->willReturn(3);

        // Use reflection to inject the partial mock into the model
        $reflection = new \ReflectionClass($this->model);
        $property = $reflection->getProperty('addressHelper');
        $property->setAccessible(true);
        $property->setValue($this->model, $this->addressHelperMock);

        $this->addressHelperMock->method('getStreetLines')->willReturn(3);
        $streetData = ["Street Line 1", "Street Line 2", "Street Line 3", "Street Line 4"];
        $this->model->setData('street', $streetData);

        // Call getStreet() which should internally call convertStreetLines()
        $result = $this->model->getStreet();

        // Assert that empty and whitespace-only lines are removed by convertStreetLines
        $this->assertEquals(["Street Line 1 Street Line 2","Street Line 3","Street Line 4"], $result);
    }

    public function testGetStreetWithOneLine()
    {
        // Create a partial mock for AddressHelper
        $this->addressHelperMock = $this->getMockBuilder(\Magento\Customer\Helper\Address::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getStreetLines']) // Mock only getStreetLines, keep the real convertStreetLines
            ->getMock();

        // Mock getStreetLines to return 2 by default
        $this->addressHelperMock->method('getStreetLines')->willReturn(1);

        // Use reflection to inject the partial mock into the model
        $reflection = new \ReflectionClass($this->model);
        $property = $reflection->getProperty('addressHelper');
        $property->setAccessible(true);
        $property->setValue($this->model, $this->addressHelperMock);

        $streetData = ["Street Line 1", "Street Line 2", "Street Line 3", "Street Line 4"];
        $this->model->setData('street', $streetData);

        // Call getStreet() which should internally call convertStreetLines()
        $result = $this->model->getStreet();

        // Assert that empty and whitespace-only lines are removed by convertStreetLines
        $this->assertEquals(["Street Line 1 Street Line 2 Street Line 3 Street Line 4"], $result);
    }

    public function testBeforeSaveTrimsStringFields(): void
    {
        $this->model->setData('firstname', '  John  ');
        $this->model->setData('lastname', '  Doe  ');
        $this->model->setData('middlename', '  M  ');
        $this->model->setData('prefix', '  Mr  ');
        $this->model->setData('suffix', '  Jr  ');
        $this->model->setData('company', '  Acme Corp  ');
        $this->model->setData('city', '  Burlington  ');
        $this->model->setData('telephone', '  555-1234  ');
        $this->model->setData('fax', '  555-5678  ');
        $this->model->setData('postcode', '  05401  ');
        $this->model->setData('vat_id', '  VAT123  ');
        $this->model->setData('email', '  test@example.com  ');

        $this->model->beforeSave();

        $this->assertEquals('John', $this->model->getData('firstname'));
        $this->assertEquals('Doe', $this->model->getData('lastname'));
        $this->assertEquals('M', $this->model->getData('middlename'));
        $this->assertEquals('Mr', $this->model->getData('prefix'));
        $this->assertEquals('Jr', $this->model->getData('suffix'));
        $this->assertEquals('Acme Corp', $this->model->getData('company'));
        $this->assertEquals('Burlington', $this->model->getData('city'));
        $this->assertEquals('555-1234', $this->model->getData('telephone'));
        $this->assertEquals('555-5678', $this->model->getData('fax'));
        $this->assertEquals('05401', $this->model->getData('postcode'));
        $this->assertEquals('VAT123', $this->model->getData('vat_id'));
        $this->assertEquals('test@example.com', $this->model->getData('email'));
    }

    public function testBeforeSaveTrimsStreetArrayImplodedToString(): void
    {
        // setData() implodes street arrays to newline-joined strings via _implodeArrayValues()
        $this->model->setData('street', ['  123 Main St  ', '  Apt 4  ']);

        $this->model->beforeSave();

        // After implode + trim, stored as a newline-joined string with each line trimmed
        $this->assertEquals("123 Main St\nApt 4", $this->model->getData('street'));
    }

    public function testBeforeSaveTrimsStreetString(): void
    {
        $this->model->setData('street', '  123 Main St  ');

        $this->model->beforeSave();

        $this->assertEquals('123 Main St', $this->model->getData('street'));
    }

    public function testBeforeSaveTrimsEachLineInStreetString(): void
    {
        $this->model->setData('street', "street 1\n street 2");

        $this->model->beforeSave();

        $this->assertEquals("street 1\nstreet 2", $this->model->getData('street'));
    }

    public function testBeforeSaveTrimsRegionField(): void
    {
        $this->model->setData('region', '  Vermont  ');

        $this->model->beforeSave();

        $this->assertEquals('Vermont', $this->model->getData('region'));
    }

    public function testBeforeSaveTrimsUnicodeWhitespace(): void
    {
        $nbsp = "\xC2\xA0"; // U+00A0 NON-BREAKING SPACE
        $zeroWidth = "\xE2\x80\x8B"; // U+200B ZERO WIDTH SPACE

        $this->model->setData('firstname', $nbsp . 'John' . $nbsp);
        $this->model->setData('city', $zeroWidth . 'Burlington' . $zeroWidth);
        $this->model->setData('street', $nbsp . '123 Main St' . $nbsp);

        $this->model->beforeSave();

        $this->assertEquals('John', $this->model->getData('firstname'));
        $this->assertEquals('Burlington', $this->model->getData('city'));
        $this->assertEquals('123 Main St', $this->model->getData('street'));
    }

    public function testBeforeSaveHandlesNullFields(): void
    {
        $this->model->setData('firstname', null);
        $this->model->setData('company', null);
        $this->model->setData('street', null);

        $this->model->beforeSave();

        $this->assertNull($this->model->getData('firstname'));
        $this->assertNull($this->model->getData('company'));
        $this->assertNull($this->model->getData('street'));
    }

    public function testBeforeSaveLeavesNonStringFieldsUntouched(): void
    {
        $this->model->setData('postcode', 12345);
        $this->model->setData('firstname', '');

        $this->model->beforeSave();

        $this->assertSame(12345, $this->model->getData('postcode'));
        $this->assertSame('', $this->model->getData('firstname'));
    }

    public function testBeforeSaveIsIdempotent(): void
    {
        $this->model->setData('firstname', 'John');
        $this->model->setData('city', 'Burlington');
        $this->model->setData('street', "123 Main St\nApt 4");

        $this->model->beforeSave();

        $this->assertEquals('John', $this->model->getData('firstname'));
        $this->assertEquals('Burlington', $this->model->getData('city'));
        $this->assertEquals("123 Main St\nApt 4", $this->model->getData('street'));
    }

    protected function tearDown(): void
    {
        $this->objectManager->setBackwardCompatibleProperty(
            $this->model,
            '_countryModels',
            []
        );
    }
}
