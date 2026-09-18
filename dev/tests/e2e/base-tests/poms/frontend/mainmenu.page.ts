// @ts-check

import { expect, type Locator, type Page } from '@playwright/test';
import { UIReference, outcomeMarker, slugs } from '@config';
import { requireEnv } from '@utils/env.utils';

// Timeout used to check our authenticated state.
const CUSTOMER_DATA_TIMEOUT = 10_000;

class MainMenuPage {
	readonly page: Page;
	readonly mainMenuElement: Locator;
	readonly mainMenuAccountButton: Locator;
	readonly mainMenuMiniCartButton: Locator;
	readonly mainMenuMyAccountItem: Locator;
	readonly mainMenuSearchButton: Locator;
	readonly mainMenuLoginItem: Locator;
	readonly mainMenuCreateAccountButton: Locator;
	readonly mainMenuWishListButton: Locator;
	readonly mainMenuMyOrdersButton: Locator;
	readonly mainMenuAddressBookButton: Locator;
	readonly mainMenuLogoutItem: Locator;

	constructor(page: Page) {
		this.page = page;
		this.mainMenuElement = page.locator(UIReference.general.headerLocator);
		this.mainMenuAccountButton = this.mainMenuElement.getByRole('button', { name: UIReference.mainMenu.myAccountButtonLabel });
		// this.mainMenuMiniCartButton = this.mainMenuElement.getByLabel(UIReference.mainMenu.miniCartLabel);
		this.mainMenuMiniCartButton = this.mainMenuElement.getByRole('button', {name: UIReference.mainMenu.miniCartLabel});
		this.mainMenuMyAccountItem = this.mainMenuElement.getByTitle(UIReference.mainMenu.myAccountButtonLabel);
		this.mainMenuSearchButton = this.mainMenuElement.getByRole('button', {name: UIReference.mainMenu.searchButtonLabel});

		this.mainMenuLoginItem = this.mainMenuElement.getByRole('link', {name: UIReference.mainMenu.loginButtonLabel});
		this.mainMenuCreateAccountButton = this.mainMenuElement.getByRole('link', {name: UIReference.mainMenu.createAccountButtonLabel});
		this.mainMenuWishListButton = this.mainMenuElement.getByRole('link', {name: UIReference.mainMenu.wishListButtonLabel});
		this.mainMenuMyOrdersButton = this.mainMenuElement.getByRole('link', {name: UIReference.mainMenu.myOrdersButtonLabel});
		this.mainMenuAddressBookButton = this.mainMenuElement.getByRole('link', {name: UIReference.mainMenu.addressBookButtonLabel});
		this.mainMenuLogoutItem = this.mainMenuElement.getByTitle(UIReference.mainMenu.myAccountLogoutItem);
	}

	/**
	 * Opens the account menu and waits for the correct menu items to appear.
	 * Magento's customer section data loads asynchronously via JS/Alpine,
	 * so the menu initially shows guest items before updating.
	 * @param loggedIn - If true, waits for logged-in menu items; if false (default), waits for guest items.
	 */
	async openAccountMenu(loggedIn = false) {
		// Workaround: the homepage has a known issue where the header menu
		// does not update to reflect logged-in state. We navigate to the account page.
		const url = loggedIn ? slugs.account.accountOverviewSlug : requireEnv('PLAYWRIGHT_BASE_URL');
		await this.page.goto(url, { waitUntil: 'load' });

		await this.mainMenuAccountButton.waitFor();
		await this.mainMenuAccountButton.click();

		const expectedItem = loggedIn ? this.mainMenuMyAccountItem : this.mainMenuLoginItem;
		await expectedItem.waitFor({ timeout: CUSTOMER_DATA_TIMEOUT });
	}

	/**
	 * Function for the test Navigate_to_category_page
	 */
	async goToCategoryPage() {
		await this.page.goto(requireEnv('PLAYWRIGHT_BASE_URL'));
		await this.mainMenuAccountButton.waitFor();
		await this.page.getByRole('link', { name: UIReference.categoryPage.categoryPageTitleText, exact: true }).click();

		await expect(this.page.getByRole('heading', {name: UIReference.categoryPage.categoryPageTitleText}),
		`Heading "${UIReference.categoryPage.categoryPageTitleText}" is visible`).toBeVisible();
	}

	/**
	 * Function for the test Navigate_to_subcategory_page
	 */
	async goToSubCategoryPage() {
		await this.page.goto(requireEnv('PLAYWRIGHT_BASE_URL'));
		await this.mainMenuAccountButton.waitFor();
		const categoryLink = this.page.getByRole('link', { name: UIReference.mainMenu.categoryItemText, exact: true });

		// FIREFOX_WORKAROUND: focus on element first (note: does not always work)
		// See: https://github.com/microsoft/playwright/issues/27969
		await categoryLink.focus();
		await categoryLink.hover();
		await expect(this.page.getByRole('link', {name: UIReference.mainMenu.subCategoryItemText})).toBeVisible();
		await this.page.getByRole('link', {name: UIReference.mainMenu.subCategoryItemText}).click();

		await expect(this.page.getByRole('heading',{ name: outcomeMarker.categoryPage.subCategoryPageTitle }),
		`Category page title "${outcomeMarker.categoryPage.subCategoryPageTitle}" is visible`).toBeVisible();
	}

	/**
	 * Function for the test User_navigates_account_page
	 */
	async gotoMyAccount(){
		await this.mainMenuMyAccountItem.click();

		await expect(this.page.getByRole('heading', { name: UIReference.accountDashboard.accountDashboardTitleLabel }),
		'Account dashboard is visible').toBeVisible();
	}

	/**
	 * Function for the test User_navigates_to_login
	 */
	async goToLoginPage() {
		const loginHeader = this.page.getByRole('heading', {name: outcomeMarker.login.loginHeaderText, exact:true});
		await this.openAccountMenu();

		await this.mainMenuLoginItem.click();
		const loginRegEx = new RegExp(`${slugs.account.loginSlugRegex}`);
		await this.page.waitForURL(loginRegEx);
		await expect(loginHeader, 'Login header text is visible').toBeVisible();
	}

	/**
	 * Function for the test User_navigates_to_create_account
	 */
	async goToCreateAccountPage() {
		const createAccountHeader = this.page.getByRole('heading', {name: outcomeMarker.account.createAccountHeaderText, exact:true});
		await this.openAccountMenu();

		await this.mainMenuCreateAccountButton.click();
		await expect(createAccountHeader, 'Create account header text is visible').toBeVisible();
	}

	/**
	 * Function to navigate to address book using the menu
	 * @assume the user is already on a (loaded) page.
	 */
	async goToAddressBook() {
		await this.mainMenuAddressBookButton.click();

		if(this.page.url().includes('new')) {
			// no address has been added yet
			await expect(this.page.getByRole( 'heading', {name: UIReference.newAddress.addNewAddressTitle, level: 1, exact:true}),
			`Heading "${UIReference.newAddress.addNewAddressTitle}" is visible`).toBeVisible();
		} else {
			await expect(this.page.getByRole('heading', {name: UIReference.address.addressBookTitle, level: 1, exact: true}),
			`Heading "${UIReference.address.addressBookTitle}" is visible`).toBeVisible();
		}
	}

	/**
	 * Function for the test Navigate_to_orders
	 * @assume the user is already on a (loaded) page.
	 */
	async goToOrders() {
		await this.mainMenuMyOrdersButton.click();

		await expect(this.page.getByRole('heading', {name: UIReference.orderHistoryPage.orderHistoryTitle, level: 1, exact:true}),
		`Heading "${UIReference.orderHistoryPage.orderHistoryTitle}" is visible`).toBeVisible();
	}

	/**
	 * Function for the test Navigate_to_wishlist
	 * @assume the user is already on a (loaded) page.
	 */
	async goToWishList() {
		await this.mainMenuWishListButton.click();
		await this.page.waitForURL(new RegExp(slugs.wishList.wishListSlug));

		await expect(this.page.getByRole('heading', {name: UIReference.wishListPage.wishListTitle, exact:true}),
		`Heading "${UIReference.wishListPage.wishListTitle}" is visible`).toBeVisible();
	}

	/**
	 * Function for the test Open_the_minicart
	 */
	async openMiniCart() {
		await this.mainMenuMiniCartButton.waitFor();
		// Trial first, since 'force' skips the actionability check
		await this.mainMenuMiniCartButton.click({trial: true});
		// By adding 'force', we can bypass the 'aria-disabled' tag.
		await this.mainMenuMiniCartButton.click({force: true});

		let miniCartDrawer = this.page.locator(UIReference.miniCart.cartDrawerLocator);
		await expect(async() => {
			await expect(miniCartDrawer.getByText(outcomeMarker.miniCart.miniCartTitle)).toBeVisible();
		}).toPass();
	}

	/**
	 * Used for function User_searches_for_product
	 * @param searchTerm
	 */
	async searchForProduct(searchTerm :string) {
		const searchField = this.page.getByRole('searchbox', { name: UIReference.search.searchBoxPlaceholderText });
		await this.page.goto(requireEnv('PLAYWRIGHT_BASE_URL'), {waitUntil:'load'});
		await this.mainMenuAccountButton.waitFor();

		await this.mainMenuSearchButton.click();
		await expect(searchField, 'Search field is visible').toBeVisible();
		await searchField.fill(searchTerm);
		await expect(this.page.getByText(UIReference.search.searchTermDropdownText, { exact: true }), 'Dropdown with results is visible').toBeVisible();
		await searchField.press('Enter');

		await this.page.waitForURL(`**/?q=${searchTerm}`);
		await expect(this.page.getByRole('heading', { name: `${UIReference.search.searchResultsTitle} \'${searchTerm}\'` }),
		`Title contains search term: "${searchTerm}"`).toBeVisible();
	}

	/**
	 * Function for the test User_logs_out
	 */
	async logout(){
		// Use a server-side check: navigating to account overview redirects to login if not authenticated.
		await this.page.goto(slugs.account.accountOverviewSlug, { waitUntil: 'load' });

		// Redirected to login page, we're already logged out.
		if(this.page.url().includes(slugs.account.loginSlug)) { return; }

		// We're on the account page, so we're logged in. Use the menu to log out.
		await this.mainMenuAccountButton.waitFor();
		await this.mainMenuAccountButton.click();
		await this.mainMenuLogoutItem.waitFor({ timeout: CUSTOMER_DATA_TIMEOUT });
		await this.mainMenuLogoutItem.click();

		//assertions: notification that user is logged out & logout button no longer visible
		await expect(this.page.getByText(outcomeMarker.logout.logoutConfirmationText, { exact: true }),
			"Message shown that confirms you're logged out").toBeVisible();
		await expect(this.mainMenuLogoutItem, `Log out button is no longer visible`).toBeHidden();

		// since the page automatically navigates to the home page, wait until we're there.
		await this.page.waitForURL(requireEnv(`PLAYWRIGHT_BASE_URL`));
	}
}

export default MainMenuPage;
