// @ts-check
/**
 * Copyright Elgentos. All rights reserved.
 * https://elgentos.nl/
 *
 * @fileoverview various tests to confirm menu functionality.
 */

// Import test and expect from utils to ensure authenticated state.
import { test } from '@utils/fixtures.utils';
import { requireEnv } from '@utils/env.utils';

import { inputValues} from '@config';

import MainMenuPage from '@poms/frontend/mainmenu.page';

test.describe('User tests (logged in)', () => {
	// Authentication is handled by the storage state fixture (fixtures.utils).
	// Each POM method navigates to the homepage and waits for customer section data.

	/**
	 * Test: a user logs out, using the menu
	 * @assume the user is already logged in
	 * @param page - Playwright page instance used to interact with the website.
	 */
	test('User_logs_out', { tag: ['@mainmenu', '@hot'] }, async ({page}) => {
		const mainMenu = new MainMenuPage(page);
		await mainMenu.logout();
	});

	/**
	 * Test: a user navigates to their account page, using the menu
	 * @assume the user is already logged in
	 * @param page - Playwright page instance used to interact with the website.
	 */
	test('Navigate_to_account_page', { tag: ['@mainmenu', '@hot'] }, async ({page}) => {
		const mainMenu = new MainMenuPage(page);
		await mainMenu.openAccountMenu(true);
		await mainMenu.gotoMyAccount();
	});

	/**
	 * Test: a user navigates to their wishlist, using the menu
	 * @assume the user is already logged in
	 * @param page - Playwright page instance used to interact with the website.
	 */
	test('Navigate_to_wishlist', { tag: ['@mainmenu', '@hot'] }, async ({page}) => {
		const mainMenu = new MainMenuPage(page);
		await mainMenu.openAccountMenu(true);
		await mainMenu.goToWishList();
	});

	/**
	 * Test: a user navigates to their order history, using the menu
	 * @assume the user is already logged in
	 * @param page - Playwright page instance used to interact with the website.
	 */
	test('Navigate_to_orders', { tag: ['@mainmenu', '@hot'] }, async ({page}) => {
		const mainMenu = new MainMenuPage(page);
		await mainMenu.openAccountMenu(true);
		await mainMenu.goToOrders();
	});

	/**
	 * Test: a user navigates to their address book, using the menu
	 * @assume the user is already logged in
	 * @param page - Playwright page instance used to interact with the website.
	 */
	test('Navigate_to_address_book', { tag: ['@mainmenu', '@hot'] }, async ({page}) => {
		const mainMenu = new MainMenuPage(page);
		await mainMenu.openAccountMenu(true);
		await mainMenu.goToAddressBook();
	});
});

test.describe('Guest tests (not logged in)', () => {
	// We're using the authenticated fixture, we need to log out explicitly.
	test.beforeEach(async({page}) => {
		const mainMenu = new MainMenuPage(page);
		await mainMenu.logout();
	});

	/**
	 * Test: a guest navigates to a category page, using the menu
	 * @param page - Playwright page instance used to interact with the website.
	 */
	test('Navigate_to_category_page', { tag: ['@mainmenu', '@cold'] }, async ({page}) => {
		const mainMenu = new MainMenuPage(page);
		await mainMenu.goToCategoryPage();
	});

	/**
	 * Test: a guest navigates to a subcategory page, using the menu
	 * @param page - Playwright page instance used to interact with the website.
	 */
	test('Navigate_to_subcategory_page', { tag: ['@mainmenu', '@cold'] }, async ({page, browserName}) => {
		test.skip(browserName === 'firefox', 'Skipped due to known issue: https://github.com/microsoft/playwright/issues/27969');
		const mainMenu = new MainMenuPage(page);
		await mainMenu.goToSubCategoryPage();
	});

	/**
	 * Test: a guest opens the mini cart in the menu
	 * @param page - Playwright page instance used to interact with the website.
	 */
	test('Open_the_minicart', { tag: ['@mainmenu', '@cold'] }, async ({page}) => {
		const mainMenu = new MainMenuPage(page);
		await page.goto(requireEnv('PLAYWRIGHT_BASE_URL'));
		await mainMenu.mainMenuMiniCartButton.waitFor();
		await mainMenu.openMiniCart();
	});

	/**
	 * Test: a guest uses the search function to search for products.
	 * @param page - Playwright page instance used to interact with the website.
	 */
	test.fixme('User_searches_for_product', { tag: ['@mainmenu', '@cold'] }, async ({page}) => {
		test.info().annotations.push({type: `fixme notice`, description: `See ticket 414 in Gitlab.`});
		const mainMenu = new MainMenuPage(page);
		await mainMenu.searchForProduct(inputValues.search.queryMultipleResults);
	});
});
