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
});

