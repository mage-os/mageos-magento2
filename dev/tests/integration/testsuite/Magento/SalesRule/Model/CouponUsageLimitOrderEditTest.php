<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\SalesRule\Model;

use Magento\Catalog\Test\Fixture\Product as ProductFixture;
use Magento\Checkout\Test\Fixture\PlaceOrder as PlaceOrderFixture;
use Magento\Checkout\Test\Fixture\SetBillingAddress as SetBillingAddressFixture;
use Magento\Checkout\Test\Fixture\SetDeliveryMethod as SetDeliveryMethodFixture;
use Magento\Checkout\Test\Fixture\SetPaymentMethod as SetPaymentMethodFixture;
use Magento\Checkout\Test\Fixture\SetShippingAddress as SetShippingAddressFixture;
use Magento\Customer\Test\Fixture\Customer as CustomerFixture;
use Magento\Framework\Registry;
use Magento\Quote\Test\Fixture\AddProductToCart as AddProductToCartFixture;
use Magento\Quote\Test\Fixture\ApplyCoupon as ApplyCouponFixture;
use Magento\Quote\Test\Fixture\CustomerCart;
use Magento\Sales\Model\AdminOrder\Create;
use Magento\Sales\Model\AdminOrder\EmailSender;
use Magento\SalesRule\Model\Rule as SalesRule;
use Magento\SalesRule\Test\Fixture\Rule as RuleFixture;
use Magento\SalesRule\Test\Fixture\RuleCoupon as CouponFixture;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\AppIsolation;
use Magento\TestFramework\Fixture\DataFixture;
use Magento\TestFramework\Fixture\DataFixtureStorage;
use Magento\TestFramework\Fixture\DataFixtureStorageManager;
use Magento\TestFramework\Fixture\DbIsolation;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Integration test for cart price rule usage limit handling during admin order edit.
 *
 * @see https://github.com/magento/magento2/issues/40624
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CouponUsageLimitOrderEditTest extends TestCase
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var DataFixtureStorage
     */
    private $fixtures;

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->fixtures = $this->objectManager->get(DataFixtureStorageManager::class)->getStorage();
    }

    /**
     * Verify that a rule with usage limit is still applied when editing the order that used it.
     */
    #[
        DbIsolation(false),
        AppIsolation(true),
        AppArea('adminhtml'),
        DataFixture(
            RuleFixture::class,
            [
                'simple_action' => SalesRule::BY_PERCENT_ACTION,
                'discount_amount' => 10,
                'coupon_type' => SalesRule::COUPON_TYPE_SPECIFIC,
                'uses_per_customer' => 1,
            ],
            'rule'
        ),
        DataFixture(
            CouponFixture::class,
            ['rule_id' => '$rule.id$', 'code' => 'LIMITED1', 'usage_limit' => 1, 'usage_per_customer' => 1],
            'coupon'
        ),
        DataFixture(CustomerFixture::class, ['email' => 'customer@example.com'], 'customer'),
        DataFixture(ProductFixture::class, ['price' => 100], 'product'),
        DataFixture(CustomerCart::class, ['customer_id' => '$customer.id$'], 'cart'),
        DataFixture(
            AddProductToCartFixture::class,
            ['cart_id' => '$cart.id$', 'product_id' => '$product.id$', 'qty' => 1]
        ),
        DataFixture(ApplyCouponFixture::class, ['cart_id' => '$cart.id$', 'coupon_codes' => ['LIMITED1']]),
        DataFixture(SetBillingAddressFixture::class, ['cart_id' => '$cart.id$']),
        DataFixture(SetShippingAddressFixture::class, ['cart_id' => '$cart.id$']),
        DataFixture(SetDeliveryMethodFixture::class, ['cart_id' => '$cart.id$']),
        DataFixture(SetPaymentMethodFixture::class, ['cart_id' => '$cart.id$']),
        DataFixture(PlaceOrderFixture::class, ['cart_id' => '$cart.id$'], 'order'),
    ]
    public function testRuleWithUsageLimitIsPreservedWhenEditingOrder(): void
    {
        $order = $this->fixtures->get('order');
        $this->assertNotEmpty($order->getAppliedRuleIds(), 'Order must have applied rules');
        $this->assertEquals('LIMITED1', $order->getCouponCode());

        $emailSenderMock = $this->createMock(EmailSender::class);
        $orderCreateModel = $this->objectManager->create(
            Create::class,
            ['emailSender' => $emailSenderMock]
        );

        $this->objectManager->get(Registry::class)->unregister('rule_data');
        $orderCreateModel->initFromOrder($order);

        $quote = $orderCreateModel->getQuote();

        $this->assertEquals(
            'LIMITED1',
            $quote->getCouponCode(),
            'Coupon code should be preserved on the new quote during order edit'
        );
        $this->assertNotEmpty(
            $quote->getAppliedRuleIds(),
            'Applied rule IDs should not be empty — the discount must be preserved during order edit'
        );
    }

    /**
     * Verify that original_order_applied_rule_ids is set on the quote during order edit.
     */
    #[
        DbIsolation(false),
        AppIsolation(true),
        AppArea('adminhtml'),
        DataFixture(
            RuleFixture::class,
            [
                'simple_action' => SalesRule::BY_PERCENT_ACTION,
                'discount_amount' => 10,
                'coupon_type' => SalesRule::COUPON_TYPE_SPECIFIC,
                'uses_per_customer' => 1,
            ],
            'rule'
        ),
        DataFixture(
            CouponFixture::class,
            ['rule_id' => '$rule.id$', 'code' => 'LIMITED2', 'usage_limit' => 1, 'usage_per_customer' => 1],
            'coupon'
        ),
        DataFixture(CustomerFixture::class, ['email' => 'customer2@example.com'], 'customer'),
        DataFixture(ProductFixture::class, ['price' => 100], 'product'),
        DataFixture(CustomerCart::class, ['customer_id' => '$customer.id$'], 'cart'),
        DataFixture(
            AddProductToCartFixture::class,
            ['cart_id' => '$cart.id$', 'product_id' => '$product.id$', 'qty' => 1]
        ),
        DataFixture(ApplyCouponFixture::class, ['cart_id' => '$cart.id$', 'coupon_codes' => ['LIMITED2']]),
        DataFixture(SetBillingAddressFixture::class, ['cart_id' => '$cart.id$']),
        DataFixture(SetShippingAddressFixture::class, ['cart_id' => '$cart.id$']),
        DataFixture(SetDeliveryMethodFixture::class, ['cart_id' => '$cart.id$']),
        DataFixture(SetPaymentMethodFixture::class, ['cart_id' => '$cart.id$']),
        DataFixture(PlaceOrderFixture::class, ['cart_id' => '$cart.id$'], 'order'),
    ]
    public function testOriginalOrderRuleIdsAreSetOnQuoteDuringEdit(): void
    {
        $order = $this->fixtures->get('order');

        $emailSenderMock = $this->createMock(EmailSender::class);
        $orderCreateModel = $this->objectManager->create(
            Create::class,
            ['emailSender' => $emailSenderMock]
        );

        $this->objectManager->get(Registry::class)->unregister('rule_data');
        $orderCreateModel->initFromOrder($order);

        $quote = $orderCreateModel->getQuote();

        $this->assertEquals(
            $order->getAppliedRuleIds(),
            $quote->getData('original_order_applied_rule_ids'),
            'Quote should carry the original order applied rule IDs for usage limit offset'
        );
    }
}
