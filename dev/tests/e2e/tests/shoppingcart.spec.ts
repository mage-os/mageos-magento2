// @ts-check

import { test, expect } from '@playwright/test';
import { UIReference, slugs, outcomeMarker } from '@config';

import CartPage from '@poms/frontend/shoppingcart.page';
import LoginPage from '@poms/frontend/login.page';
import ProductPage from '@poms/frontend/product.page';
import { requireEnv } from '@utils/env.utils';
import NotificationValidatorUtils from '@utils/notificationValidator.utils';

test.describe('Cart functionalities (guest)', () => {
  /**
   * @feature BeforeEach runs before each test in this group.
   * @scenario Add a product to the cart and confirm it's there.
   * @given I am on any page
   * @when I navigate to a (simple) product page
   *  @and I add it to my cart
   *  @then I should see a notification
   * @when I click the cart in the main menu
   *  @then the minicart should become visible
   *  @and I should see the product in the minicart
   */
  test.beforeEach(async ({ page }, testInfo) => {
    const productPage = new ProductPage(page);
    await productPage.addSimpleProductToCart(UIReference.productPage.simpleProductTitle, slugs.productPage.simpleProductSlug);

    const productAddedNotification = `${outcomeMarker.productPage.simpleProductAddedNotification} ${UIReference.productPage.simpleProductTitle}`;
    const notificationValidator = new NotificationValidatorUtils(page, testInfo);
    await notificationValidator.validate('beforeEach add product to cart');

    // await mainMenu.openMiniCart();
    // await expect(page.getByText(outcomeMarker.miniCart.simpleProductInCartTitle)).toBeVisible();
    await page.goto(slugs.cart.cartSlug);
  });

  /**
   * @feature Product can be added to cart
   * @scenario User adds a product to their cart
   * @given I have added a product to my cart
   *  @and I am on the cart page
   * @then I should see the name of the product in my cart
   */
  test('Add_product_to_cart',{ tag: ['@cart', '@cold'],}, async ({page}) => {
    await expect(page.getByRole('heading').getByRole('link', {name: UIReference.productPage.simpleProductTitle}), `Product is visible in cart`).toBeVisible();
  });

  /**
   * @feature Product permanence after login
   * @scenario A product added to the cart should still be there after user has logged in
   * @given I have a product in my cart
   * @when I log in
   * @then I should still have that product in my cart
   */
  test('Product_remains_in_cart_after_login',{ tag: ['@cart', '@account', '@hot']}, async ({page, browserName}) => {
    await test.step('Add another product to cart', async () =>{
      const productpage = new ProductPage(page);
      await page.goto(slugs.productPage.secondSimpleProductSlug);
      await productpage.addSimpleProductToCart(UIReference.productPage.secondSimpleProducTitle, slugs.productPage.secondSimpleProductSlug);
    });

    await test.step('Log in with account', async () =>{
      const browserEngine = browserName?.toUpperCase() || "UNKNOWN";
      const loginPage = new LoginPage(page);
      const emailInputValue = requireEnv(`MAGENTO_EXISTING_ACCOUNT_EMAIL_${browserEngine}`);
      const passwordInputValue = requireEnv('MAGENTO_EXISTING_ACCOUNT_PASSWORD');

      await loginPage.login(emailInputValue, passwordInputValue);
    });

    await page.goto(slugs.cart.cartSlug);
    await expect(page.getByRole('heading').getByRole('link', { name: UIReference.productPage.simpleProductTitle }),`${UIReference.productPage.simpleProductTitle} should still be in cart`).toBeVisible();
    await expect(page.getByRole('heading').getByRole('link', { name: UIReference.productPage.secondSimpleProducTitle }),`${UIReference.productPage.secondSimpleProducTitle} should still be in cart`).toBeVisible();
  });

  /**
   * @feature Remove product from cart
   * @scenario User has added a product and wants to remove it from the cart page
   * @given I have added a product to my cart
   *  @and I am on the cart page
   * @when I click the delete button
   * @then I should see a notification that the product has been removed from my cart
   *  @and I should no longer see the product in my cart
   */
  test('Remove_product_from_cart',{ tag: ['@cart','@cold'],}, async ({page}) => {
    const cart = new CartPage(page);
    await cart.removeProduct(UIReference.productPage.simpleProductTitle);
  });

  /**
   * @feature Change quantity of products in cart
   * @scenario User has added a product and changes the quantity
   * @given I have a product in my cart
   * @and I am on the cart page
   * @when I change the quantity of the product
   * @and I click the update button
   * @then the quantity field should have the new amount
   * @and the subtotal/grand total should update
   */
  test('Change_product_quantity_in_cart',{ tag: ['@cart', '@cold'],}, async ({page}) => {
    const cart = new CartPage(page);
    await cart.changeProductQuantity('2');
  });

  /**
   * @feature Incorrect discount code check
   * @scenario The user provides an incorrect discount code, the system should reflect that
   * @given I have a product in my cart
   * @and I am on the cart page
   * @when I enter a wrong discount code
   * @then I should get a notification that the code did not work.
   */

  test('Invalid_coupon_code_is_rejected',{ tag: ['@cart', '@coupon-code', '@cold'] }, async ({page}) => {
    const cart = new CartPage(page);
    await cart.enterWrongCouponCode("Incorrect Coupon Code");
  });
})
