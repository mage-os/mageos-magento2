// @ts-check

import { expect, type Locator, type Page } from '@playwright/test';
import { requireEnv } from '@utils/env.utils';
import { UIReference } from '@config';


class AdminLogin {
	// General
	readonly page: Page;
	readonly pageHeadingOne: Locator;
	readonly saveConfigButton: Locator;
	// Input Fields
	readonly adminLoginEmailField: Locator;
	readonly adminLoginPasswordField: Locator;
	readonly adminLoginButton: Locator;
	// Navigation
	readonly mainMenuStoresButton: Locator;
	readonly storesConfigurationButton: Locator;
	readonly storesCustomersTab: Locator;
	readonly advancedSettingsTab: Locator;
	readonly customerConfigurationLink: Locator;
	readonly adminSettingsLink:Locator;
	// Settings
	readonly customerCaptchaAccordion: Locator;
	readonly adminSecurityAccordion: Locator;
	readonly storeFrontCaptchaOption: Locator;
	readonly adminSharingOption: Locator;
	readonly customerCAPTCHAInheritCheckbox: Locator;
	readonly adminInheritCheckbox: Locator;


	constructor(page: Page) {
		// General
		this.page = page;
		this.pageHeadingOne = page.locator(UIReference.general.headingOneLocator);
		this.saveConfigButton = page.getByRole('button', { name: UIReference.general.saveConfigButton });
		// Input Fields
		this.adminLoginEmailField = page.locator(UIReference.authentication.adminUsernameFieldId);
		this.adminLoginPasswordField = page.locator(UIReference.authentication.adminPasswordFieldId);
		this.adminLoginButton = page.locator(UIReference.authentication.adminLoginButtonClass);
		// Navigation
		this.mainMenuStoresButton = page.getByRole('link', {name: UIReference.admin.storesButton});
		this.storesConfigurationButton = page.getByRole('link', {name: UIReference.admin.configuration}).first();
		this.storesCustomersTab = page.locator(UIReference.admin.configTabLocator).getByText(UIReference.admin.customers);
		this.advancedSettingsTab = page.getByRole('strong').filter({hasText: UIReference.admin.advanced});
		this.customerConfigurationLink = page.getByRole('link', { name: UIReference.admin.customerConfiguration });
		this.adminSettingsLink = page.getByRole('link', {name: UIReference.admin.admin, exact: true});
		// Settings
		this.customerCaptchaAccordion = page.getByRole('link', { name: 'CAPTCHA' }).filter({hasNotText: 'documentation'});
		this.adminSecurityAccordion = page.getByRole('link', { name: UIReference.general.security });
		this.storeFrontCaptchaOption = page.getByLabel(UIReference.admin.captchaEnabled);
		this.adminSharingOption = page.getByLabel(UIReference.admin.adminSharing);
		this.customerCAPTCHAInheritCheckbox = page.locator(UIReference.admin.customerCAPTCHAInheritLocator);
		this.adminInheritCheckbox = page.locator(UIReference.admin.customerInheritLocator);
	}

	/**
	 * Disable the CAPTCHAs that prevent Playwright tests from functioning.
	 */
	async disableLoginCaptcha(){
		await this.storesCustomersTab.click();
		// Confirm the link for customer configuration is visible.
		await expect(async() => {
			await expect(this.customerConfigurationLink, `"Customer Configuration" link is visible`).toBeVisible();
		}).toPass();

		await this.customerConfigurationLink.click();

		if(!await this.storeFrontCaptchaOption.isVisible()){
			// option not visible, tab is closed.
			await this.customerCaptchaAccordion.click();
			// Confirm captcha option is now open
			await expect(this.storeFrontCaptchaOption, `"enable CAPTCHA on storefront" option is open`).toBeVisible();
		}

		// if the 'use system value' checkbox is checked, uncheck it.
		if(await this.customerCAPTCHAInheritCheckbox.isChecked()) {
			await this.customerCAPTCHAInheritCheckbox.uncheck();
			await expect(this.storeFrontCaptchaOption, `CAPTCHA option can be changed`).toBeEnabled();
		}

		// check if CAPTCHA is already disabled
		if(await this.storeFrontCaptchaOption.inputValue() == '0'){
			await expect(this.storeFrontCaptchaOption, `CAPTCHA is disabled for customers`).toHaveValue('0');
		} else {
			// Disabled the CAPTCHA
			await this.storeFrontCaptchaOption.selectOption('0');
			await expect(this.storeFrontCaptchaOption, `CAPTCHA is disabled for customers`).toHaveValue('0');

			await this.saveConfigButton.click();
			await expect(this.page.locator(UIReference.general.adminMessageLocator),
				`Notification "Configuration Saved" is visible.`).toContainText(UIReference.admin.configurationSavedText);
		}
	}

	/**
	 * Navigate to the Stores Settings in Magento Admin.
	 */
	async navigateToStoreSettings() {
		const configurationPageLabel = UIReference.admin.configuration;
		await this.mainMenuStoresButton.click();
		await this.storesConfigurationButton.click();

		// Confirm the page has loaded correctly by checking for the presence of text.
		await expect(async () => {
			await expect(this.pageHeadingOne, `Page title is '${configurationPageLabel}'`)
				.toContainText(`${configurationPageLabel}`);

			await expect(this.page.getByRole('link', {name: UIReference.general.general}),
				`"General options" under General section is visible.`).toBeVisible();
		}).toPass();
	}

	/**
	 * Enable multiple admin logins
	 * Assumption is that we're in the 'Store Settings'.
	 */
	async enableMultipleAdminLogins() {
		await this.advancedSettingsTab.click();
		// Confirm the link for 'admin' settings is visible.
		await expect(async() => {
			await expect(this.adminSettingsLink, `"Admin" link under "Advanced" is visible`).toBeVisible();
		}).toPass();

		await this.adminSettingsLink.click();

		if(!await this.adminSharingOption.isVisible()){
			// tab is closed.
			await this.adminSecurityAccordion.click();
			await expect(this.adminSharingOption, `Security tab is opened`).toBeVisible();
		}

		// if the 'use system value' checkbox is checked, uncheck it.
		if(await this.adminInheritCheckbox.isChecked()) {
			await this.adminInheritCheckbox.uncheck();
			await expect(this.adminSharingOption, `Admin Account Sharing option can be changed`).toBeEnabled();
		}

		// check if Admin Account Sharing is already available
		if(await this.adminSharingOption.inputValue() == '1'){
			await expect(this.adminSharingOption, `Account sharing option enabled`).toHaveValue('1');
		} else {
			// Enable account sharing
			await this.adminSharingOption.selectOption('1');
			await expect(this.adminSharingOption, `Account sharing option enabled`).toHaveValue('1');

			await this.saveConfigButton.click();
			await expect(this.page.locator(UIReference.general.adminMessageLocator),
				`Notification "Configuration Saved" is visible.`).toContainText(UIReference.admin.configurationSavedText);
		}

	}

	/**
	 * Log the admin user in to set up the Magento 2 environment
	 * @param username - admin's username, sourced from .env
	 * @param password - admin's password, sourced from .env
	 */
	async loginAdmin(username:string, password:string){
		const dashboardLabel = this.page.getByRole('heading', {name: UIReference.titles.adminDashboardHeading});
		const captchaNotification = this.page.locator(UIReference.general.messageLocator).filter(
			{hasText : UIReference.errors.captchaIncorrect}
		);
		const adminLoginHeading = this.page.getByText(UIReference.authentication.adminLoginText);

		if(await dashboardLabel.isVisible()){
			// already logged in
			return;
		}

		await this.page.goto(`${requireEnv(`MAGENTO_ADMIN_SLUG`)}`, { waitUntil: 'load'});

		// Confirm the page has loaded correctly by checking for the presence of text.
		await expect(async() => {
			await expect(adminLoginHeading, `"Please sign in" text is visible`).toBeVisible();
		}).toPass();

		await this.adminLoginEmailField.fill(username);
		await this.adminLoginPasswordField.fill(password);
		await this.adminLoginButton.click();

		if(await captchaNotification.isVisible()){
			throw new Error(`CAPTCHA field found, automated login failed.`);
		}

		// Confirm the page has loaded correctly by checking for the presence of text.
		await expect(async() => {
			await expect(dashboardLabel, `Dashboard Title is visible`).toBeVisible();
		}).toPass();
	}
}

export default AdminLogin;
