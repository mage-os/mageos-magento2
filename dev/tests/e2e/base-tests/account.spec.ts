// @ts-check

/**
 * Copyright Elgentos. All rights reserved.
 * https://elgentos.nl/
 *
 * @fileoverview Various tests to check account functionality.
 */

// Import test and expect from utils to ensure authenticated state.
import { test, expect } from '@utils/fixtures.utils';
import { faker } from '@faker-js/faker';

import AccountPage from '@poms/frontend/account.page';
import LoginPage from '@poms/frontend/login.page';
import NewsletterSubscriptionPage from '@poms/frontend/newsletter.page';

import { requireEnv } from '@utils/env.utils';
import ApiClient from '@utils/apiClient.utils';
import { UIReference, outcomeMarker, slugs, inputValues} from '@config';

/**
 * Test group: User credentials tests
 */
test.describe('User credentials tests (API-provisioned)', { annotation:
{type: 'Account Dashboard', description: 'Test for changing credentials using API-provisioned account'}, }, () => {

	let apiClient: ApiClient;

	// Ensure we don't use an authenticated state.
	test.use({ storageState: { cookies: [], origins: [] } });

	test.beforeAll(async () => {
		apiClient = await new ApiClient().create();
	});

	test.afterAll(async () => {
		await apiClient.dispose();
	});

	/**
	 * Test: User changes their password
	 * @param page - Playwright page instance used to interact with the website.
	 * @param request - APIRequestContext instance used to create accounts with the API.
	 */
	test('Change_password', { tag: ['@account-credentials', '@hot'] }, async ({ page, request }) => {
		const accountPage = new AccountPage(page);
		const loginPage = new LoginPage(page);

		const parallelIndex = test.info().parallelIndex;
		const email = `playwright_pwtest_${parallelIndex}@elgentos.nl`;
		const password = requireEnv('MAGENTO_EXISTING_ACCOUNT_PASSWORD');
		const changedPassword = requireEnv('MAGENTO_EXISTING_ACCOUNT_CHANGED_PASSWORD');

		// Ensure a fresh account exists with the original password
		const searchResponse = await apiClient.get(
			`/rest/V1/customers/search` +
			`?searchCriteria[filterGroups][0][filters][0][field]=email` +
			`&searchCriteria[filterGroups][0][filters][0][value]=${email}` +
			`&searchCriteria[filterGroups][0][filters][0][conditionType]=eq`
		);

		if (searchResponse.items?.length > 0) {
			await apiClient.delete(`/rest/V1/customers/${searchResponse.items[0].id}`);
		}

		await apiClient.post('/rest/V1/customers', {
			customer: {
				email,
				firstname: inputValues.account.firstName,
				lastname: inputValues.account.lastName,
			},
			password,
		});

		// Login and change password via UI
		await loginPage.login(email, password);
		await page.goto(slugs.account.changePasswordSlug, { waitUntil: 'load' });
		await expect(page.getByRole('textbox', { name: UIReference.credentials.currentPasswordFieldLabel })).toBeVisible();
		await accountPage.updatePassword(password, changedPassword);

		// Verify the new password works via API
		const tokenResponse = await request.post('/rest/V1/integration/customer/token', {
			data: { username: email, password: changedPassword },
		});
		expect(tokenResponse.ok(), 'Customer token API should accept the new password').toBeTruthy();
	});

	/**
	 * Test: User changes their e-mailaddress
	 * @param page - Playwright page instance used to interact with the website.
	 * @param request - APIRequestContext instance used to create accounts with the API.
	 */
	test('Update_email_address', { tag: ['@account-credentials', '@hot'] }, async ({ page, request }) => {
		const accountPage = new AccountPage(page);
		const loginPage = new LoginPage(page);

		const parallelIndex = test.info().parallelIndex;
		const originalEmail = `playwright_emailtest_${parallelIndex}@elgentos.nl`;
		const updatedEmail = `playwright_emailtest_updated_${parallelIndex}@elgentos.nl`;
		const password = requireEnv('MAGENTO_EXISTING_ACCOUNT_PASSWORD');

		// Ensure a fresh account exists with the original email
		const searchResponse = await apiClient.get(
			`/rest/V1/customers/search` +
			`?searchCriteria[filterGroups][0][filters][0][field]=email` +
			`&searchCriteria[filterGroups][0][filters][0][value]=${originalEmail}` +
			`&searchCriteria[filterGroups][0][filters][0][conditionType]=eq`
		);

		if (searchResponse.items?.length > 0) {
			await apiClient.delete(`/rest/V1/customers/${searchResponse.items[0].id}`);
		}

		// Also clean up any leftover updated email account from a previous run
		const updatedSearchResponse = await apiClient.get(
			`/rest/V1/customers/search` +
			`?searchCriteria[filterGroups][0][filters][0][field]=email` +
			`&searchCriteria[filterGroups][0][filters][0][value]=${updatedEmail}` +
			`&searchCriteria[filterGroups][0][filters][0][conditionType]=eq`
		);

		if (updatedSearchResponse.items?.length > 0) {
			await apiClient.delete(`/rest/V1/customers/${updatedSearchResponse.items[0].id}`);
		}

		await apiClient.post('/rest/V1/customers', {
			customer: {
				email: originalEmail,
				firstname: inputValues.account.firstName,
				lastname: inputValues.account.lastName,
			},
			password,
		});

		// Login and update email via UI
		await loginPage.login(originalEmail, password);
		await page.goto(slugs.account.accountEditSlug, { waitUntil: 'load' });
		await expect(page.locator('#form-validate').
			getByText(UIReference.accountDashboard.accountDashboardTitleLabel),
			`Heading "${UIReference.accountDashboard.accountDashboardTitleLabel}" is visible`).toBeVisible();
		await accountPage.updateEmail(password, updatedEmail);

		// Verify the updated email works via API
		const tokenResponse = await request.post('/rest/V1/integration/customer/token', {
			data: { username: updatedEmail, password: password },
		});
		expect(tokenResponse.ok(), 'Customer token API should accept the updated email').toBeTruthy();
	});
});

/**
 * Test Group: Account address book actions
 */
test.describe.serial('Account address book actions', { annotation: {type: 'Account Dashboard', description: 'Tests for the Address Book'},}, () => {

	test.beforeEach(async ({page}) => {
		await page.goto(slugs.account.addressIndexSlug, {waitUntil: "load"});

		// if page navigated to new address, no address had been added yet.
		if(page.url().includes('new')){
			await expect(async () => {
				await expect(page.getByText(UIReference.newAddress.addNewAddressTitle),
				`Heading "${UIReference.newAddress.addNewAddressTitle}" is visible`).toBeVisible();
			}).toPass();
		} else {
			await expect(async () => {
				await expect(page.getByRole('heading',
				{ name: UIReference.address.addressBookTitle }),
				`Heading "${UIReference.address.addressBookTitle}" is visible`).toBeVisible();
			}).toPass();
		}
	});

	/**
	 * Test: The user adds an address to their account
	 * @assume the user is already logged in.
	 * @param page - Playwright page instance used to interact with the website.
	 */
	test('Add_an_address',{ tag: ['@address-actions', '@hot'] }, async ({page}) => {
		await page.goto(slugs.account.addressNewSlug);
		const accountPage = new AccountPage(page);

		const address = `${faker.location.streetAddress()} ${Math.floor(Math.random() * 100 + 1)}`;
		const company = faker.company.name();

		await accountPage.addNewAddress({ company: company, street: address});

		await expect(page.getByText(address).first(), `Expect new address to be listed`).toBeVisible();
		await expect(page.getByText(company).first(), `Expect new company name to be listed`).toBeVisible();
	});

	/**
	 * Test: The user edits an existing address to their account
	 * @assume the user is already logged in.
	 * @param page - Playwright page instance used to interact with the website.
	 */
	test('Edit_existing_address',{ tag: ['@address-actions', '@hot'] }, async ({page}) => {
		const accountPage = new AccountPage(page);
		await page.goto(slugs.account.addressBookSlug);
		let editAddressButton = page.getByRole('link', {name: UIReference.accountDashboard.editAddressIconButton}).first();
		let isDefaultAddress = false;

		if(await editAddressButton.isHidden()){
			// The edit address button was not found, add another address first.
			if(await page.getByRole('link', { name: 'Change Shipping Address arrow' }).isVisible()) {
				isDefaultAddress = true;
			} else {
				expect (page.url(), `Edit address button not found, check URL is to the new address page`).toBe(slugs.account.addressNewSlug);
				await accountPage.addNewAddress();
			}
		}

		// const companyName = faker.company.name();
		const address = `${faker.location.streetAddress()} ${Math.floor(Math.random() * 100 + 1)}`;
		await accountPage.editExistingAddress({street:address}, isDefaultAddress);

		// await expect(page.getByText(companyName)).toBeVisible();
		await expect(page.getByText(address).first()).toBeVisible();
	});

	/**
	 * Test: The user can't add an address if they don't fill in all the required fields
	 * @assume the user is already logged in.
	 * @param page - Playwright page instance used to interact with the website.
	 */
	test('Missing_required_field_prevents_creation',{ tag: ['@address-actions'] }, async ({page}) => {
		await page.goto(slugs.account.addressNewSlug);
		const accountPage = new AccountPage(page);

		await accountPage.phoneNumberField.fill(inputValues.firstAddress.firstPhoneNumberValue);
		await accountPage.saveAddressButton.click();

		const errorMessage = page.getByText(UIReference.general.errorMessageStreetAddressRequiredFieldText).first();
		await errorMessage.waitFor();
		await expect(errorMessage, `Error message "${UIReference.general.errorMessageStreetAddressRequiredFieldText}" is visible`).toBeVisible();
	});
});

/**
 * Test Group: Newsletter tests
 */
test.describe('Newsletter actions', { annotation: {type: 'Account Dashboard', description: 'Newsletter tests'},}, () => {

	/**
	 * Test: The user (un)subscribes from the newsletter
	 * @assume the user is already logged in.
	 * @param page - Playwright page instance used to interact with the website.
	 */
	test('Update_newsletter_subscription',{ tag: ['@newsletter-actions', '@cold'] }, async ({page}) => {
		// Navigate to a page.
		await page.goto(slugs.account.accountOverviewSlug);
		await page.waitForLoadState();

		const newsletterPage = new NewsletterSubscriptionPage(page);
		let newsletterLink = page.getByRole('link', { name: UIReference.accountDashboard.links.newsletterLink });
		const newsletterCheckElement = page.getByLabel(UIReference.newsletterSubscriptions.generalSubscriptionCheckLabel);

		await newsletterLink.click();
		await expect(page.getByText(outcomeMarker.account.newsletterSubscriptionTitle, { exact: true })).toBeVisible();

		let updateSubscription = await newsletterPage.updateNewsletterSubscription();

		await newsletterLink.click();

		if(updateSubscription) {
			await expect(newsletterCheckElement).toBeChecked();
		}
		else {
			await expect(newsletterCheckElement).not.toBeChecked();
		}
	});
});
