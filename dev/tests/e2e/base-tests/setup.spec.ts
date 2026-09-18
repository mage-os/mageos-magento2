// @ts-check

/**
 * Copyright elgentos. All rights reserved.
 * https://elgentos.nl/
 *
 * @fileOverview adjusts necessary settings and records for testing purposes.
 */

import { test, expect } from '@playwright/test';

import { requireEnv } from '@utils/env.utils';
import ApiClient from '@utils/apiClient.utils';

import { inputValues } from '@config';

import AdminLogin from '@poms/admin/adminlogin.page';
import AdminMarketing from '@poms/admin/marketing.page';

/**
 * Set variables we'll be using throughout the file.
 */
const magentoAdminUsername = requireEnv(`MAGENTO_ADMIN_USERNAME`);
const magentoAdminPassword = requireEnv(`MAGENTO_ADMIN_PASSWORD`);
let APIClient : ApiClient;

// Set up an API Client
test.beforeAll(`Initialize API Client`, async() => {
	APIClient = await new ApiClient().create();
});

/**
 * Disable the Login CAPTCHA to ensure Playwright can log in.
 *
 * @param page - Playwright Page instance (fixture)
 * @param browserName - the name of the browser running the test.
 */
test('Disable_login_captcha_and_enable_multiple_login', {
	tag: '@setup'}, async ({ page, browserName }) => {

	test.skip( browserName !== 'chromium',
		`Disabling login captcha through Chromium. This is ${browserName}, therefore test is skipped.`
	);

	const adminLoginPage = new AdminLogin(page);

	await test.step(`Step: Login to admin environment`, async() => {
		await adminLoginPage.loginAdmin(magentoAdminUsername, magentoAdminPassword);
	});

	await test.step(`Step: Disable login CAPTCHA`, async() => {
		await adminLoginPage.navigateToStoreSettings();
		await adminLoginPage.disableLoginCaptcha();
	});

	await test.step(`Step: Enable multiple admin login`, async() => {
		await expect(async () => {
			await expect(page.getByRole('link', {name: 'Customer Configuration'}),
				`"Customer Configuration" under General section is visible.`).toBeVisible();
		}).toPass();

		await adminLoginPage.enableMultipleAdminLogins();
	});

});

/**
 * Set up test accounts through the Magento API
 *
 * @param browserName - used to identify the browser the test is running in.
 * @param testInfo - Playwright class that allows annotations to the report and more.
 */
test(`Create_test_accounts`, { tag: ['@setup', '@api']}, async ({ browserName }, testInfo) => {
	test.slow(); // Mark as slow to double test time.

	// Skip if not Chromium
	test.skip( browserName !== 'chromium', `Accounts are made through API call - only one browser is required.`);

	/**
	 * Test step: create generic test accounts
	 */
	await test.step(`Creating accounts for general testing`, async() => {
		// Start by checking if the accounts already exist
		const allCustomers = await APIClient.get(
			`/rest/V1/customers/search` +
			`?searchCriteria[filterGroups][0][filters][0][field]=email` +
			`&searchCriteria[filterGroups][0][filters][0][value]=%25playwright_user%25` +
			`&searchCriteria[filterGroups][0][filters][0][conditionType]=like`);
		const testAccountsPresent = allCustomers.items ?? [];

		// Check for test accounts, create them if not found
		if(testAccountsPresent.length > 0) {
			test.info().annotations.push({
				type: `test accounts found`,
				description: `We found testing accounts. Please check if the following is correct:
				${JSON.stringify(testAccountsPresent, null, 2)}`
			});
		} else {
			for(let accountId = 0; accountId < 13; accountId++) {
				const customerPayload = {
					customer : {
						email: `playwright_user_${accountId}@elgentos.nl`,
						firstname: `${inputValues.account.firstName}`,
						lastname: `${inputValues.account.lastName}`
					},
					password: `${requireEnv('MAGENTO_ADMIN_PASSWORD')}`
				};

				// Send payload to database
				const addCustomerResponse = await APIClient.post(`/rest/V1/customers`, customerPayload);

				// Annotate report with relevant info
				test.info().annotations.push({
					type: `accounts created!`,
					description: `The following accounts have been created:
				${JSON.stringify(addCustomerResponse, null, 2)}`
				});
			}
		}
	});
});

/**
 * Set up coupon codes through the Magento API
 *
 * @param browserName - used to create the specific coupon code
 */
test(`Set_coupon_codes`, {
	tag: ['@setup', '@api']}, async ({ browserName }, ) => {

	// TODO: Clean up code
	// TODO: Move to marketing.page.ts
	// TODO: Remove the use of the requireEnv(), since it's not necessary anymore.

	const browserEngine = browserName?.toUpperCase() || "UNKNOWN";
	const couponCode = requireEnv(`MAGENTO_COUPON_CODE_${browserEngine}`);

	// Find all coupon codes, then check if testing coupon exists
	const couponCheckResponse = await APIClient.get(`/rest/V1/coupons/search?searchCriteria=all`);
	const codePresent = couponCheckResponse.items.some((item: { code: string; }) => item.code === `${couponCode}`);

	// If coupon is present, check if it's enabled.
	if(codePresent) {
		// Retrieve sales rule
		const coupon = couponCheckResponse.items.find((item: { code: string; }) => item.code === `${couponCode}`);
		const ruleId = coupon.rule_id;
		const rule = await APIClient.get(`/rest/V1/salesRules/${ruleId}`);

		// If not active, set to active.
		if(!rule.is_active) {
			rule.is_active = true;
			const updateCoupon = await APIClient.put(`/rest/V1/salesRules/${ruleId}`, { rule: rule });

			if(updateCoupon.is_active) {
				test.info().annotations.push({type: 'Coupon notice', description: `Your code "${coupon.code}" was found, but we had to activate it manually.`});
			}
			return;
		} else {
			// code is present and enabled.
			test.info().annotations.push({type: 'Coupon notice', description: `Your code "${coupon.code}" was found. Active status: ${rule.is_active}.`});
			return;
		}

	} else {
		// Not present. Set coupon code, then check.
		const rules = await APIClient.get(`/rest/V1/salesRules/search?searchCriteria=all`);
		const websiteInfo = await APIClient.get(`/rest/V1/store/websites`);
		const customerGroups = await APIClient.get(`/rest/V1/customerGroups/search?searchCriteria=all`);
		let websiteIds: any[] = [];
		let customerGroupsIds: any[] = [];

		websiteInfo.forEach((website: { name: string; id: any; }) => {
			if(website.name !== 'admin') {
				websiteIds.push(website.id);
			}
		});

		customerGroups.items.forEach((customerGroup: { id: any; }) => {
			customerGroupsIds.push(customerGroup.id);
		});

		const newRule = {
			name : 'Test Coupon',
			website_ids: websiteIds,
			customer_group_ids: customerGroupsIds,
			from_date: '2025-01-20',
			uses_per_customer: 0,
			is_active: true,
			stop_rules_processing: true,
			is_advanced: true,
			sort_order: 0,
			discount_amount: 10,
			discount_step: 0,
			apply_to_shipping: false,
			times_used: 0,
			is_rss: true,
			coupon_type: 'SPECIFIC_COUPON',
			use_auto_generation: false,
			uses_per_coupon: 0
		};

		const newCouponRule = await APIClient.post(`/rest/V1/salesRules`, {rule: newRule});

		const couponAPIJSON = {
			rule_id: newCouponRule.rule_id,
			code: couponCode,
			times_used: 0,
			is_primary: true
		};

		const createNewCoupon = await APIClient.post(`/rest/V1/coupons`, {coupon: couponAPIJSON});
		test.info().annotations.push({type: `Coupon Created`, description: `Created coupon: ${JSON.stringify(createNewCoupon)}`});
	}
});
