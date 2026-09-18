// @ts-check

import { expect, type Locator, type Page } from '@playwright/test';
import { UIReference, inputValues, outcomeMarker } from '@config';
import { requireEnv } from '@utils/env.utils';

class AdminLogin {
  readonly page: Page;
  readonly adminLoginEmailField: Locator;
  readonly adminLoginPasswordField: Locator;
  readonly adminLoginButton: Locator;

  constructor(page: Page) {
    this.page = page;
    this.adminLoginEmailField = page.locator(UIReference.adminPage.usernameFieldId);
    this.adminLoginPasswordField = page.locator(UIReference.adminPage.passwordFieldId);
    this.adminLoginButton = page.locator(UIReference.adminPage.loginButtonClass);
  }

  /**
   * @feature Magento Admin Configuration
   * @scenario Disable the login CAPTCHA on the admin panel
   * @given the admin is logged into the Magento dashboard
   * @when the admin navigates to Stores > Configuration > Customers > Customer Configuration > CAPTCHA section
   * @and the "Use system value" checkbox for CAPTCHA is unchecked
   * @and the "Enable CAPTCHA on Admin Login" select field is visible
   * @and the current setting is "Yes"
   * @then the admin changes the setting to "No"
   * @and clicks the Save Config button
   * @then the system displays a success message confirming the configuration was saved
   */
  async disableLoginCaptcha() {
    const mainMenuStoresButton = this.page.getByRole('link', { name: UIReference.adminPage.navigation.storesButtonLabel});
    // selecting first specifically because plugins can place another 'configuration' link in this menu.
    const storeSettingsConfigurationLink = this.page.getByRole('link', { name: UIReference.adminPage.subNavigation.configurationButtonLabel }).first();

    await expect(async () => {
      await mainMenuStoresButton.click();
      await expect(storeSettingsConfigurationLink, `Link to Store Configuration is visible`).toBeVisible();
    }).toPass();

    await storeSettingsConfigurationLink.click();

    const customersTab = this.page.getByRole('tab', { name: UIReference.configurationPage.customersTabLabel });
    const customerConfigurationLink = this.page.getByRole('link', { name: UIReference.configurationPage.customerConfigurationTabLabel });
    await customersTab.click();
    await customerConfigurationLink.waitFor();
    await customerConfigurationLink.click();

    const captchaSettingsBlock = this.page.getByRole('link', { name: UIReference.configurationPage.captchaSectionLabel })
      .filter({hasNotText: 'documentation'});
    const captchaSettingsSystemValueCheckbox = this.page.locator(UIReference.configurationPage.captchaSettingSystemCheckbox);

    await captchaSettingsBlock.waitFor();

    if(!await captchaSettingsSystemValueCheckbox.isVisible()) {
      await captchaSettingsBlock.click();
      await expect(captchaSettingsSystemValueCheckbox, `Checkbox "Use system value" for CAPTCHA is visible`).toBeVisible();
    }

    if(await captchaSettingsSystemValueCheckbox.isChecked()){
      await captchaSettingsSystemValueCheckbox.uncheck();
    }

    const captchaSettingSelectField = this.page.locator(UIReference.configurationPage.captchaSettingSelectField);
    const selectedOption = await captchaSettingSelectField.locator('option:checked').textContent();

    // We only have to perform these steps if the option is set to 'Yes'
    if(selectedOption == 'Yes') {
      await captchaSettingSelectField.selectOption({label: inputValues.captcha.captchaDisabled});

      const saveConfigButton = this.page.getByRole('button', { name: UIReference.configurationPage.saveConfigButtonLabel });
      await saveConfigButton.click();

      await expect(this.page.locator(UIReference.general.messageLocator).filter(
        {hasText: outcomeMarker.magentoAdmin.configurationSavedText}),
        `Notification "${outcomeMarker.magentoAdmin.configurationSavedText}" is visible`).toBeVisible();
    } else {
      await expect(selectedOption,`CAPTCHA is disabled`)
        .toEqual(expect.stringContaining(UIReference.adminPage.captchaDisabledLabel));
    }
  }

  /**
   * @feature Enable multiple admin logins in Magento
   * @scenario Admin enables the ability for multiple users to log in with the same admin account
   * @given the user is on the Magento admin dashboard
   * @when the user navigates to Stores > Configuration > Advanced > Admin > Security
   * @and the "Allow Multiple Admin Account Login" field is visible
   * @and the "Use system value" checkbox is unchecked
   * @and the select field value is "No"
   * @then the user selects "Yes" from the dropdown
   * @and clicks the Save Config button
   * @then the system displays a success message
   */
  async enableMultipleAdminLogins() {
    const mainMenuStoresButton = this.page.getByRole('link', { name: UIReference.adminPage.navigation.storesButtonLabel});
    // selecting first specifically because plugins can place another 'configuration' link in this menu.
    const storeSettingsConfigurationLink = this.page.getByRole('link', { name: UIReference.adminPage.subNavigation.configurationButtonLabel }).first();

    await expect(async () => {
      await mainMenuStoresButton.click();
      await expect(storeSettingsConfigurationLink, `Link to Store Configuration is visible`).toBeVisible();
    }).toPass();

    await storeSettingsConfigurationLink.click();

    const advancedConfigurationTab = this.page.getByRole('tab', { name: UIReference.configurationPage.advancedTabLabel });
    const advancedConfigAdminLabel = this.page.getByRole('link', { name: UIReference.configurationPage.advancedAdministrationTabLabel, exact: true });
    await advancedConfigurationTab.click();
    await advancedConfigAdminLabel.waitFor();
    await advancedConfigAdminLabel.click();

    const advancedConfigSecuritySection = this.page.getByRole('link', { name: UIReference.configurationPage.securitySectionLabel });
    const multipleLoginsSystemCheckbox = this.page.locator(UIReference.configurationPage.allowMultipleLoginsSystemCheckbox);

    await advancedConfigSecuritySection.waitFor();
    if (!await multipleLoginsSystemCheckbox.isVisible()) {
      await advancedConfigSecuritySection.click();
    }

    await expect(multipleLoginsSystemCheckbox, `Checkbox for multiple admin logins is visible`).toBeVisible();

    // make sure the 'use system value' option is not checked
    const adminAccountSharingSystemValueCheckbox = this.page.locator(UIReference.configurationPage.allowMultipleLoginsSystemCheckbox);
    if (await adminAccountSharingSystemValueCheckbox.isChecked()) {
      await adminAccountSharingSystemValueCheckbox.uncheck();
    }

    const allowMultipleLoginSelectField = this.page.locator(UIReference.configurationPage.allowMultipleLoginsSelectField);
    const selectedOption = await allowMultipleLoginSelectField.locator('option:checked').textContent();

    // We only have to perform these steps if the option is set to 'No'
    if(selectedOption == 'No') {
      await allowMultipleLoginSelectField.selectOption({label: inputValues.adminLogins.allowMultipleLogins});

      const saveConfigButton = this.page.getByRole('button', { name: UIReference.configurationPage.saveConfigButtonLabel });
      await saveConfigButton.click();

      await expect(this.page.locator(UIReference.general.messageLocator).filter(
        {hasText: outcomeMarker.magentoAdmin.configurationSavedText}),
        `Notification "${outcomeMarker.magentoAdmin.configurationSavedText}" is visible`).toBeVisible();
    }
  }

  /**
   * @feature Login to Magento admin dashboard
   * @scenario User logs in to admin dashboard
   * @given the admin slug environment variable is defined
   * @and the user navigates to the admin login page
   * @when the user enters a valid username and password
   * @and the user clicks the login button
   * @then the user should see the dashboard heading displayed
   */
  async login(username: string, password: string){
    await this.page.goto(requireEnv('MAGENTO_ADMIN_SLUG'));
    await this.page.waitForURL(`**/${requireEnv('MAGENTO_ADMIN_SLUG')}`);

    if(await this.page.getByRole('heading', {name: UIReference.adminPage.dashboardHeadingText}).isVisible()) {
      // already logged in
      return;
    }

    await this.adminLoginEmailField.fill(username);
    await this.adminLoginPasswordField.fill(password);
    await this.adminLoginButton.click();

    const captchaNotification = this.page.locator(UIReference.general.messageLocator).filter({hasText: UIReference.adminPage.captchaIncorrectText});

    if(await captchaNotification.isVisible()) {
      console.log('CAPTCHA field is visible, automated login not possible!');
      throw new Error("CAPTCHA field is visible, automated login not possible!");
    }

    const dashboardLabel = this.page.getByRole('heading',{level:1, name: UIReference.adminPage.dashboardHeadingText});

    // expect the H1 'Dashboard' to be visible
    await expect(async () => {
      await expect(dashboardLabel, `Title "Dashboard" is visible`).toBeVisible();
    }).toPass();
  }
}

export default AdminLogin;
