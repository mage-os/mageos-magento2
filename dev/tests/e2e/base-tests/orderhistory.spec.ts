// @ts-check

import { test } from '@playwright/test';
import { UIReference, slugs } from '@config';
import { requireEnv } from '@utils/env.utils';

import LoginPage from '@poms/frontend/login.page';
import ProductPage from '@poms/frontend/product.page';
import CheckoutPage from '@poms/frontend/checkout.page';
import OrderHistoryPage from '@poms/frontend/orderhistory.page';

test('Recent_order_is_visible_in_history', async ({ page, browserName }) => {
  const browserEngine = browserName?.toUpperCase() || 'UNKNOWN';
  const emailInputValue = requireEnv(`MAGENTO_EXISTING_ACCOUNT_EMAIL_${browserEngine}`);
  const passwordInputValue = requireEnv('MAGENTO_EXISTING_ACCOUNT_PASSWORD');

  const loginPage = new LoginPage(page);
  const productPage = new ProductPage(page);
  const checkoutPage = new CheckoutPage(page);
  const orderHistoryPage = new OrderHistoryPage(page);

  await loginPage.login(emailInputValue, passwordInputValue);

  await page.goto(slugs.productPage.simpleProductSlug);
  await productPage.addSimpleProductToCart(UIReference.productPage.simpleProductTitle, slugs.productPage.simpleProductSlug);
  await page.goto(slugs.checkout.checkoutSlug);
  const orderNumberLocator = await checkoutPage.placeOrder();
  const orderNumberText = await orderNumberLocator.innerText();
  const orderNumber = orderNumberText.replace(/\D/g, '');

  await orderHistoryPage.open();
  await orderHistoryPage.verifyOrderPresent(orderNumber);
});
