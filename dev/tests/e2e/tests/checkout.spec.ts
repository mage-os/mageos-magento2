// @ts-check

import { test, expect } from '@playwright/test';
import { UIReference, slugs } from '@config';
import { requireEnv } from '@utils/env.utils';
import MagewireUtils from '@utils/magewire.utils';

import LoginPage from '@poms/frontend/login.page';
import ProductPage from '@poms/frontend/product.page';
import AccountPage from '@poms/frontend/account.page';
import CheckoutPage from '@poms/frontend/checkout.page';

/**
 * @feature BeforeEach runs before each test in this group.
 * @scenario Add product to the cart, confirm it's there, then move to checkout.
 * @given I am on any page
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


test.describe('Checkout (login required)', () => {
  // Before each test, log in
  test.beforeEach(async ({ page, browserName }) => {
    const browserEngine = browserName?.toUpperCase() || "UNKNOWN";
    const emailInputValue = requireEnv(`MAGENTO_EXISTING_ACCOUNT_EMAIL_${browserEngine}`);
    const passwordInputValue = requireEnv('MAGENTO_EXISTING_ACCOUNT_PASSWORD');

    const loginPage = new LoginPage(page);
    await loginPage.login(emailInputValue, passwordInputValue);
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
   * @then I should see a confirmation that my order has been pla*  @and a order number should be created and show to me
   */
  test('Place_order_for_simple_product',{ tag: ['@simple-product-order', '@hot'],}, async ({page}, testInfo) => {
    const checkoutPage = new CheckoutPage(page);
    let orderNumber = await checkoutPage.placeOrder();
    testInfo.annotations.push({ type: 'Order number', description: `${orderNumber}` });
  });
});
