<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Block\Adminhtml\Edit\Tab\View;

use Magento\Backend\Model\Session;
use Magento\Backend\Test\Unit\Helper\SessionTestHelper;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Block\Adminhtml\Edit\Tab\View\PersonalInfo;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Model\Log;
use Magento\Customer\Model\Logger;
use Magento\Customer\Test\Unit\Helper\LogTestHelper;
use Magento\Framework\App\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Phrase;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\DateTime\Timezone;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Test\Unit\Helper\DateTimeTestHelper;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Model\ScopeInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Customer personal information template block test.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PersonalInfoTest extends TestCase
{
    /**
     * @var string
     */
    protected $defaultTimezone = 'America/Los_Angeles';

    /**
     * @var string
     */
    protected $pathToDefaultTimezone = 'path/to/default/timezone';

    /**
     * @var PersonalInfo
     */
    protected $block;

    /**
     * @var Log|MockObject
     */
    protected $customerLog;

    /**
     * @var TimezoneInterface|MockObject
     */
    protected $localeDate;

    /**
     * @var ScopeConfigInterface|MockObject
     */
    protected $scopeConfig;

    /**
     * @var CustomerRegistry
     */
    protected $customerRegistry;

    /**
     * @var Customer
     */
    protected $customerModel;

    /**
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function setUp(): void
    {
        $customer = $this->createMock(CustomerInterface::class);
        $customer->expects($this->any())->method('getId')->willReturn(1);
        $customer->expects($this->any())->method('getStoreId')->willReturn(1);

        $customerDataFactory = $this->createPartialMock(
            CustomerInterfaceFactory::class,
            ['create']
        );
        $customerDataFactory->expects($this->any())->method('create')->willReturn($customer);

        $backendSession = new SessionTestHelper();
        $backendSession->setCustomerData(['account' => []]);

        $this->customerLog = new LogTestHelper();

        $customerLogger = $this->createPartialMock(Logger::class, ['get']);
        $customerLogger->expects($this->any())->method('get')->willReturn($this->customerLog);

        $dateTime = new DateTimeTestHelper();
        $dateTime->setNow('2015-03-04 12:00:00');

        $this->localeDate = $this->createPartialMock(
            Timezone::class,
            ['scopeDate', 'formatDateTime', 'getDefaultTimezonePath']
        );
        $this->localeDate
            ->expects($this->any())
            ->method('getDefaultTimezonePath')
            ->willReturn($this->pathToDefaultTimezone);

        $this->scopeConfig = $this->createPartialMock(Config::class, ['getValue']);
        $this->customerRegistry = $this->createPartialMock(
            CustomerRegistry::class,
            ['retrieve']
        );
        $this->customerModel = $this->createPartialMock(Customer::class, ['isCustomerLocked']);

        $objectManagerHelper = new ObjectManager($this);
        $objectManagerHelper->prepareObjectManager();

        $this->block = $objectManagerHelper->getObject(
            PersonalInfo::class,
            [
                'customerDataFactory' => $customerDataFactory,
                'dateTime' => $dateTime,
                'customerLogger' => $customerLogger,
                'localeDate' => $this->localeDate,
                'scopeConfig' => $this->scopeConfig,
                'backendSession' => $backendSession,
            ]
        );
        $this->block->setCustomerRegistry($this->customerRegistry);
    }

    /**
     * @return void
     */
    public function testGetStoreLastLoginDateTimezone()
    {
        $this->scopeConfig
            ->expects($this->once())
            ->method('getValue')
            ->with(
                $this->pathToDefaultTimezone,
                ScopeInterface::SCOPE_STORE
            )
            ->willReturn($this->defaultTimezone);

        $this->assertEquals($this->defaultTimezone, $this->block->getStoreLastLoginDateTimezone());
    }

    /**
     * @param string $status
     * @param string|null $lastLoginAt
     * @param string|null $lastVisitAt
     * @param string|null $lastLogoutAt
     * @return void
     */
    #[DataProvider('getCurrentStatusDataProvider')]
    public function testGetCurrentStatus($status, $lastLoginAt, $lastVisitAt, $lastLogoutAt)
    {
        $this->scopeConfig->expects($this->any())
            ->method('getValue')
            ->with(
                'customer/online_customers/online_minutes_interval',
                ScopeInterface::SCOPE_STORE
            )
            ->willReturn(240); //TODO: it's value mocked because unit tests run data providers before all testsuite

        $this->customerLog->setLastLoginAt($lastLoginAt);
        $this->customerLog->setLastVisitAt($lastVisitAt);
        $this->customerLog->setLastLogoutAt($lastLogoutAt);

        $this->assertEquals($status, (string) $this->block->getCurrentStatus());
    }

    /**
     * @return array
     */
    public static function getCurrentStatusDataProvider()
    {
        return [
            ['Offline', null, null, null],
            ['Offline', '2015-03-04 11:00:00', null, '2015-03-04 12:00:00'],
            ['Offline', '2015-03-04 11:00:00', '2015-03-04 11:40:00', null],
            ['Online', '2015-03-04 11:00:00', (new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT), null]
        ];
    }

    /**
     * @param string $result
     * @param string|null $lastLoginAt
     * @return void
     */
    #[DataProvider('getLastLoginDateDataProvider')]
    public function testGetLastLoginDate($result, $lastLoginAt)
    {
        $this->customerLog->setLastLoginAt($lastLoginAt);
        $this->localeDate->expects($this->any())->method('formatDateTime')->willReturn($lastLoginAt);

        $this->assertEquals($result, $this->block->getLastLoginDate());
    }

    /**
     * @return array
     */
    public static function getLastLoginDateDataProvider()
    {
        return [
            ['2015-03-04 12:00:00', '2015-03-04 12:00:00'],
            ['Never', null]
        ];
    }

    /**
     * @param string $result
     * @param string|null $lastLoginAt
     * @return void
     */
    #[DataProvider('getStoreLastLoginDateDataProvider')]
    public function testGetStoreLastLoginDate($result, $lastLoginAt)
    {
        $this->customerLog->setLastLoginAt($lastLoginAt);

        $this->localeDate->expects($this->any())->method('scopeDate')->willReturn($lastLoginAt);
        $this->localeDate->expects($this->any())->method('formatDateTime')->willReturn($lastLoginAt);

        $this->assertEquals($result, $this->block->getStoreLastLoginDate());
    }

    /**
     * @return array
     */
    public static function getStoreLastLoginDateDataProvider()
    {
        return [
            ['2015-03-04 12:00:00', '2015-03-04 12:00:00'],
            ['Never', '']
        ];
    }

    /**
     * @param string $expectedResult
     * @param bool $value
     * @return void
     */
    #[DataProvider('getAccountLockDataProvider')]
    public function testGetAccountLock($expectedResult, $value)
    {
        $this->customerRegistry->expects($this->once())->method('retrieve')->willReturn($this->customerModel);
        $this->customerModel->expects($this->once())->method('isCustomerLocked')->willReturn($value);
        $expectedResult =  new Phrase($expectedResult);
        $this->assertEquals($expectedResult, $this->block->getAccountLock());
    }

    /**
     * @return array
     */
    public static function getAccountLockDataProvider()
    {
        return [
            ['expectedResult' => 'Locked', 'value' => true],
            ['expectedResult' => 'Unlocked', 'value' => false]
        ];
    }
}
