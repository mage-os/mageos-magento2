// @ts-check

// Import test and expect from utils to ensure authenticated state.
import { test, expect } from '@utils/fixtures.utils';
import { UIReference, slugs } from '@config';
import { requireEnv } from '@utils/env.utils';
import MagewireUtils from '@utils/magewire.utils';

import LoginPage from '@poms/frontend/login.page';
import ProductPage from '@poms/frontend/product.page';
import AccountPage from '@poms/frontend/account.page';
import MainMenuPage from '@poms/frontend/mainmenu.page';
import CheckoutPage from '@poms/frontend/checkout.page';

test.describe('Checkout (login required)', () => {
	/**
	 * @feature BeforeEach runs before each test in this group.
	 * @scenario Add product to the cart, confirm it's there, then move to checkout.
	 * @given I am on a page
	 * @when I navigate to a (simple) product page
	 *  @and I add it to my cart
	 *  @then I should see a notification
	 * @when I navigate to the checkout
	 *  @then the checkout page should be shown
	 *  @and I should see the product in the minicart
	 */
	test.beforeEach(async ({ page }) => {
		const magewire = new MagewireUtils(page);
		magewire.startMonitoring();

		const productPage = new ProductPage(page);

		await page.goto(slugs.productPage.simpleProductSlug);
		await productPage.addSimpleProductToCart(UIReference.productPage.simpleProductTitle, slugs.productPage.simpleProductSlug);
		await page.goto(slugs.checkout.checkoutSlug);
	});

	// Before each test, go to checkout
	test.beforeEach(async ({ page }) => {
		await page.goto(slugs.checkout.checkoutSlug);
	});

	/**
	 * @feature Automatically fill in certain data in checkout (if user is logged in)
	 * @scenario When the user navigates to the checkout (with a product), their name and address should be filled in.
	 * @given I am logged in
	 *  @and I have a product in my cart
	 *  @and I have navigated to the checkout page
	 * @then My name and address should already be filled in
	 */
	test('Address_is_pre_filled_in_checkout',{ tag: ['@checkout', '@hot']}, async ({page}) => {
		let signInLink = page.getByRole('link', { name: UIReference.credentials.loginButtonLabel });
		let addressField = page.getByLabel(UIReference.newAddress.streetAddressLabel);
		let addressAlreadyAdded = false;

		if(await signInLink.isVisible()) {
			throw new Error(`Sign in link found, user is not logged in. Please check the test setup.`);
		}

		// name field should NOT be on the page
		await expect(page.getByLabel(UIReference.personalInformation.firstNameLabel)).toBeHidden();

		if(await addressField.isVisible()) {
			if(!addressAlreadyAdded){
				// Address field is visible and addressalreadyAdded is not true, so we need to add an address to the account.
				const accountPage = new AccountPage(page);
				await accountPage.addNewAddress();
			} else {
				throw new Error(`Address field is visible even though an address has been added to the account.`);
			}
		}

		// expect to see radio button to select existing address
		let shippingRadioButton = page.locator(UIReference.checkout.shippingAddressRadioLocator).first();
		await expect(shippingRadioButton, 'Radio button to select address should be visible').toBeVisible();
	});

	/**
	 * @feature Place order for simple product
	 * @scenario User places an order for a simple product
	 * @given I have a product in my cart
	 *  @and I am on any page
	 * @when I navigate to the checkout
	 *  @and I fill in the required fields
	 *  @and I click the button to place my order
	 * @then I should see a confirmation that my order has been placed
	 *  @and a order number should be created and show to me
	 */
	test('Place_order_for_simple_product',{ tag: ['@simple-product-order', '@hot'],}, async ({page}, testInfo) => {
		const checkoutPage = new CheckoutPage(page);
		let orderNumber = await checkoutPage.placeOrder();
		testInfo.annotations.push({ type: 'Order number', description: `${orderNumber}` });
	});
});

test.describe('Checkout (guest)', () => {
	test.beforeEach(async({page}) => {
		// log out
		const mainMenu = new MainMenuPage(page);
		await mainMenu.logout();

		// set up magewire monitoring
		const magewire = new MagewireUtils(page);
		magewire.startMonitoring();

		// ensure product in cart
		const productPage = new ProductPage(page);
		await page.goto(slugs.productPage.simpleProductSlug);
		await productPage.addSimpleProductToCart(UIReference.productPage.simpleProductTitle, slugs.productPage.simpleProductSlug);

		// to checkout
		await page.goto(slugs.checkout.checkoutSlug);
	});

	/**
	 * @feature Discount Code
	 * @scenario User adds a discount code to their cart
	 * @given I have a product in my cart
	 *  @and I am on my cart page
	 * @when I click on the 'add discount code' button
	 * @then I fill in a code
	 *  @and I click on 'apply code'
	 * @then I should see a confirmation that my code has been added
	 *  @and the code should be visible in the cart
	 *  @and a discount should be applied to the product
	 */
	test('Add_coupon_code_in_checkout',{ tag: ['@checkout', '@coupon-code', '@cold']}, async ({page, browserName}) => {
		const checkout = new CheckoutPage(page);
		const browserEngine = browserName?.toUpperCase() || "UNKNOWN";
		const discountCode = requireEnv(`MAGENTO_COUPON_CODE_${browserEngine}`);

		await checkout.applyDiscountCodeCheckout(discountCode);
	});

	test('Verify_price_calculations_in_checkout', { tag: ['@checkout', '@price-calculation'] }, async ({ page }) => {
		const productPage = new ProductPage(page);
		const checkoutPage = new CheckoutPage(page);

		// Add product to cart and go to checkout
		await productPage.addSimpleProductToCart(UIReference.productPage.simpleProductTitle, slugs.productPage.simpleProductSlug);
		await page.goto(slugs.checkout.checkoutSlug);

		// Select shipping method to trigger price calculations
		await checkoutPage.shippingMethodOptionFixed.check();

		// Wait for totals to update
		await expect(async () => {
			await page.locator('.magewire\\.messenger').waitFor({state: "hidden"});
		}).toPass();

		// // Wait for totals to update
		// await page.waitForFunction(() => {
		//   const element = document.querySelector('.magewire\\.messenger');
		//   return element && getComputedStyle(element).height === '0px';
		// });

		// Get all price components using the verifyPriceCalculations method from the CheckoutPage fixture
		await checkoutPage.verifyPriceCalculations();
	});

	/**
	 * @feature Remove discount code from checkout
	 * @scenario User has added a discount code, then removes it
	 * @given I have a product in my cart
	 * @and I am on the checkout page
	 * @when I add a discount code
	 * @then I should see a notification
	 * @and the code should be visible in the cart
	 * @and a discount should be applied to a product
	 * @when I click the 'cancel coupon' button
	 * @then I should see a notification the discount has been removed
	 * @and the discount should no longer be visible.
	 */
	test('Remove_coupon_code_from_checkout',{ tag: ['@checkout', '@coupon-code', '@cold']}, async ({page, browserName}) => {
		const checkout = new CheckoutPage(page);
		const browserEngine = browserName?.toUpperCase() || "UNKNOWN";
		const discountCode = requireEnv(`MAGENTO_COUPON_CODE_${browserEngine}`);

		await checkout.applyDiscountCodeCheckout(discountCode);
		await checkout.removeDiscountCode();
	});

	/**
	 * @feature Incorrect discount code check
	 * @scenario The user provides an incorrect discount code, the system should reflect that
	 * @given I have a product in my cart
	 * @and I am on the cart page
	 * @when I enter a wrong discount code
	 * @then I should get a notification that the code did not work.
	 */
	test('Invalid_coupon_code_in_checkout_is_rejected',{ tag: ['@checkout', '@coupon-code', '@cold'] }, async ({page}) => {
		const checkout = new CheckoutPage(page);
		await checkout.enterWrongCouponCode("incorrect discount code");
	});

	/**
	 * @feature Payment Method Selection
	 * @scenario Guest user selects different payment methods during checkout
	 * @given I have a product in my cart
	 *  @and I am on the checkout page as a guest
	 * @when I select a payment method
	 *  @and I complete the checkout process
	 * @then I should see a confirmation that my order has been placed
	 *  @and a order number should be created and shown to me
	 */
	test('Guest_can_select_payment_methods', { tag: ['@checkout', '@payment-methods', '@cold'] }, async ({ page }) => {
		// Marking test as slow to allow more time befoure timeout
		test.slow();
		const checkoutPage = new CheckoutPage(page);

		// Test with check/money order payment
		await test.step('Place order with check/money order payment', async () => {
		await page.goto(slugs.checkout.checkoutSlug);
		await checkoutPage.fillShippingAddress();
		await checkoutPage.selectShippingMethod('fixed');
		await checkoutPage.selectPaymentMethod('check');
		let orderNumber = await checkoutPage.placeOrder();
		expect(orderNumber, 'Order number should be generated and returned').toBeTruthy();
		});
	});
});
