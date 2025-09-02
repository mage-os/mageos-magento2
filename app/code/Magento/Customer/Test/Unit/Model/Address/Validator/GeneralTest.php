<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Model\Address\Validator;

use Magento\Customer\Model\Address;
use Magento\Customer\Model\Address\AbstractAddress;
use Magento\Customer\Model\Address\Validator\General;
use Magento\Directory\Helper\Data;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Attribute;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\Customer\Model\AddressFactory;
use Magento\Framework\Validator\Factory as ValidatorFactory;
use Magento\Framework\Validator\ValidatorInterface;

/**
 * Magento\Customer\Model\Address\Validator\General tests.
 */
class GeneralTest extends TestCase
{
    /** @var Data|MockObject  */
    private $directoryDataMock;

    /** @var Config|MockObject  */
    private $eavConfigMock;

    /** @var General  */
    private $model;

    /**
     * @var ValidatorFactory|MockObject
     */
    protected $validatorFactoryMock;

    /**
     * @var AddressFactory|MockObject
     */
    protected $addressFactoryMock;

    protected function setUp(): void
    {
        $this->validatorFactoryMock = $this->createMock(ValidatorFactory::class);
        $this->addressFactoryMock = $this->createMock(AddressFactory::class);
        $customerAddressMock = $this->createMock(Address::class);
        $this->addressFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($customerAddressMock);
        $validatorMock = $this->createMock(ValidatorInterface::class);
        $this->validatorFactoryMock->expects($this->once())
            ->method('createValidator')
            ->with('customer_address', 'save')
            ->willReturn($validatorMock);
        $validatorMock->expects($this->once())->method('isValid')->willReturn(true);
        $this->directoryDataMock = $this->createMock(Data::class);
        $this->eavConfigMock = $this->createMock(Config::class);
        $this->model = new General(
            $this->eavConfigMock,
            $this->directoryDataMock,
            $this->validatorFactoryMock,
            $this->addressFactoryMock
        );
    }

    /**
     * @param array $data
     * @param array $expected
     * @return void
     *
     * @dataProvider validateDataProvider
     */
    public function testValidate(array $data, array $expected)
    {
        $addressMock = $this
            ->getMockBuilder(AbstractAddress::class)
            ->disableOriginalConstructor()
            ->addMethods(
                [
                    'getFirstname',
                    'getLastname',
                    'getCity',
                    'getTelephone',
                    'getFax',
                    'getCompany',
                    'getPostcode',
                    'getCountryId',
                ]
            )->onlyMethods(
                [
                    'getStreetLine',
                    'getRegionId'
                ]
            )->getMock();

        $attributeMock = $this->createMock(Attribute::class);
        $attributeMock->expects($this->any())
            ->method('getIsRequired')
            ->willReturn(true);

        $this->eavConfigMock->expects($this->any())
            ->method('getAttribute')
            ->willReturn($attributeMock);

        $this->directoryDataMock->expects($this->once())
            ->method('getCountriesWithOptionalZip')
            ->willReturn([]);

        $addressMock->method('getFirstName')->willReturn($data['firstname']);
        $addressMock->method('getLastname')->willReturn($data['lastname']);
        $addressMock->method('getStreetLine')->with(1)->willReturn($data['street']);
        $addressMock->method('getCity')->willReturn($data['city']);
        $addressMock->method('getTelephone')->willReturn($data['telephone']);
        $addressMock->method('getFax')->willReturn($data['fax']);
        $addressMock->method('getCompany')->willReturn($data['company']);
        $addressMock->method('getPostcode')->willReturn($data['postcode']);
        $addressMock->method('getCountryId')->willReturn($data['country_id']);
        $addressMock->method('getRegionId')->willReturn($data['region_id']);

        $actual = $this->model->validate($addressMock);
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
            'company' => 'Magento',
            'fax' => '222-22-22',
            'region_id' => 43,
        ];
        $result = [
            'firstname' => [
                array_merge(array_merge($data, ['firstname' => '']), ['country_id' => $countryId++]),
                ['"firstname" is required. Enter and try again.'],
            ],
            'lastname' => [
                array_merge(array_merge($data, ['lastname' => '']), ['country_id' => $countryId++]),
                ['"lastname" is required. Enter and try again.'],
            ],
            'street' => [
                array_merge(array_merge($data, ['street' => '']), ['country_id' => $countryId++]),
                ['"street" is required. Enter and try again.'],
            ],
            'city' => [
                array_merge(array_merge($data, ['city' => '']), ['country_id' => $countryId++]),
                ['"city" is required. Enter and try again.'],
            ],
            'telephone' => [
                array_merge(array_merge($data, ['telephone' => '']), ['country_id' => $countryId++]),
                ['"telephone" is required. Enter and try again.'],
            ],
            'postcode' => [
                array_merge(array_merge($data, ['postcode' => '']), ['country_id' => $countryId++]),
                ['"postcode" is required. Enter and try again.'],
            ],
            'validated' => [array_merge($data, ['country_id' => $countryId++]), []],
        ];

        return $result;
    }
}
