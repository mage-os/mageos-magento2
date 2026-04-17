// @ts-check

import { test } from '@playwright/test';
import {UIReference, slugs, inputValues} from '@config';

import LoginPage from '@poms/frontend/login.page';
import MainMenuPage from '@poms/frontend/mainmenu.page';
import ProductPage from '@poms/frontend/product.page';
import { requireEnv } from '@utils/env.utils';

// no resetting storageState, mainmenu has more functionalities when logged in.

test.describe('User tests (logged in)', () => {
  // Before each test, log in
  test.beforeEach(async ({ page, browserName }) => {
    const browserEngine = browserName?.toUpperCase() || "UNKNOWN";
    const emailInputValue = requireEnv(`MAGENTO_EXISTING_ACCOUNT_EMAIL_${browserEngine}`);
    const passwordInputValue = requireEnv('MAGENTO_EXISTING_ACCOUNT_PASSWORD');

    const loginPage = new LoginPage(page);
    await loginPage.login(emailInputValue, passwordInputValue);
  });

  /**
   * @feature Logout
   * @scenario The user can log out
   *  @given I am logged in
   *  @and I am on any Magento 2 page
   *    @when I open the account menu
   *    @and I click the Logout option
   *  @then I should see a message confirming I am logged out
   */
  test('User_logs_out', { tag: ['@mainmenu', '@hot'] }, async ({page}) => {
    const mainMenu = new MainMenuPage(page);
    await mainMenu.logout();
  });

  /**
   * @feature Navigate to account page
   * @scenario user navigates to account page
   * @given I am logged in
   * @and I am on any magento 2 page
   * @when I open the account menu
   * @and I click the account button
   * @and I click the 'my account' button
   * @then I should be navigated to my account
   */
  test('Navigate_to_account_page', { tag: ['@mainmenu', '@hot'] }, async ({page}) => {
    const mainMenu = new MainMenuPage(page);
    await mainMenu.gotoMyAccount();
  });

  /**
   * @feature Navigate to wishlist
   * @scenario user navigates to their wishlist
   * @given I am logged in
   * @and I am on any magento 2 page
   * @when I open the account menu
   * @and I click on the wishlist button
   * @then I should be navigated to the wishlist page
   */
  test('Navigate_to_wishlist', { tag: ['@mainmenu', '@hot'] }, async ({page}) => {
    const mainMenu = new MainMenuPage(page);
    await mainMenu.goToWishList();
  });

  /**
   * @feature Navigate to orders overview
   * @scenario user navigates to their order history
   * @given I am logged in
   * @and I am on any magento 2 page
   * @when I open the account menu
   * @and I click on the 'My orders' button
   * @then I should be navigated to the page with my order history
   */
  test('Navigate_to_orders', { tag: ['@mainmenu', '@hot'] }, async ({page}) => {
    const mainMenu = new MainMenuPage(page);
    await mainMenu.goToOrders();
  });

  /**
   * @feature Navigate to address book
   * @scenario user navigates to their address book
   * @given I am logged in
   * @and I am on any Magento 2 page
   * @when I open the account menu
   * @and I click on the 'Address book' button
   * @then I should be navigated to the page with my order history
   * @and I should see an appropriate title based on whether an address has been added
   */
  test('Navigate_to_address_book', { tag: ['@mainmenu', '@hot'] }, async ({page}) => {
    const mainMenu = new MainMenuPage(page);
    await mainMenu.goToAddressBook();
  });
});





