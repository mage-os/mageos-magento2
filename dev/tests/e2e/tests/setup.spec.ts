// @ts-check

import { test } from '@playwright/test';
import { faker } from '@faker-js/faker';
import { inputValues } from '@config';
import { requireEnv } from '@utils/env.utils';
import { createLogger } from '@utils/logger';

import AdminLogin from '@poms/adminhtml/login.page';
import AdminMarketing from '@poms/adminhtml/marketing.page';
import AdminCustomers from '@poms/adminhtml/customers.page';

import RegisterPage from '@poms/frontend/register.page';

const logger = createLogger('Setup');

const magentoAdminUsername = requireEnv('MAGENTO_ADMIN_USERNAME');
const magentoAdminPassword = requireEnv('MAGENTO_ADMIN_PASSWORD');

test.beforeEach(async ({ page }, testInfo) => {
  const adminLoginPage = new AdminLogin(page);
  await adminLoginPage.login(magentoAdminUsername, magentoAdminPassword);
});

test.describe('Setting up the testing environment', () => {
  // Set tests to serial mode to ensure the order is followed.
  test.describe.configure({mode:'serial'});

  /**
   * @feature Magento Admin Configuration (disable login CAPTCHA)
   * @scenario Disable login CAPTCHA in admin settings via Chromium browser
   * @given the test is running in a Chromium-based browser
   * @when the admin logs in to the Magento dashboard
   * @and the admin navigates to the security configuration section
   * @and the "Enable CAPTCHA on Admin Login" setting is updated to "No"
   * @then the configuration is saved successfully
   * @but if the browser is not Chromium
   * @then the test is skipped with an appropriate message
   */
  test('Disable_login_captcha', { tag: '@setup' }, async ({ page, browserName }, testInfo) => {
    test.skip(browserName !== 'chromium', `Disabling login captcha through Chromium. This is ${browserName}, therefore test is skipped.`);

    const adminLoginPage = new AdminLogin(page);
    await adminLoginPage.disableLoginCaptcha();
  });

  /**
   * @feature Magento Admin Configuration (Enable multiple admin logins)
   * @scenario Enable multiple admin logins only in Chromium browser
   * @given the
   * @scenario Enable multiple admin logins only in Chromium browser
   * @given the test is running in a Chromium-based browser
   * @when the admin logs in to the Magento dashboard
   * @and the admin navigates to the configuration page
   * @and the "Allow Multiple Admin Account Login" setting is updated to "Yes"
   * @then the configuration is saved successfully
   * @but if the browser is not Chromium
   * @then the test is skipped with an appropriate message
   */
  test('Enable_multiple_admin_logins', { tag: '@setup' }, async ({ page, browserName }, testInfo) => {
    test.skip(browserName !== 'chromium', `Disabling login captcha through Chromium. This is ${browserName}, therefore test is skipped.`);

    const adminLoginPage = new AdminLogin(page);
    await adminLoginPage.enableMultipleAdminLogins();
  });

  /**
   * @feature Cart Price Rules Configuration
   * @scenario Set up a coupon code for the current browser environment
   * @given a valid coupon code environment variable exists for the current browser engine
   * @when the admin navigates to the Cart Price Rules section
   * @and the admin creates a new cart price rule with the specified coupon code
   * @then the coupon code is successfully saved and available for use
   */
  test('Set_up_coupon_codes', { tag: '@setup'}, async ({page, browserName}, testInfo) => {
    const adminMarketingPage = new AdminMarketing(page);
    const browserEngine = browserName?.toUpperCase() || "UNKNOWN";
    const couponCode = requireEnv(`MAGENTO_COUPON_CODE_${browserEngine}`);

    const addCouponCodeResult = await adminMarketingPage.addCartPriceRule(couponCode);
    testInfo.annotations.push({type: 'notice', description: addCouponCodeResult});
  });

  /**
   * @feature Customer Account Setup
   * @scenario Create a test customer account for the current browser environment
   * @given valid environment variables for email and password exist for the current browser engine
   * @when the user navigates to the registration page
   * @and submits the registration form with first name, last name, email, and password
   * @then a new customer account is successfully created for testing purposes
   */
  test('Create_test_accounts', { tag: '@setup'}, async ({page, browserName}, testInfo) => {
    const adminCustomersPage = new AdminCustomers(page);
    const registerPage = new RegisterPage(page);
    const browserEngine = browserName?.toUpperCase() || "UNKNOWN";
    const accountEmail = requireEnv(`MAGENTO_EXISTING_ACCOUNT_EMAIL_${browserEngine}`);
    const accountPassword = requireEnv('MAGENTO_EXISTING_ACCOUNT_PASSWORD');

    await test.step(`Check if ${accountEmail} is already registered`, async () => {
      const customerLookUp = await adminCustomersPage.checkIfCustomerExists(accountEmail);
      if(customerLookUp){
        testInfo.skip(true, `${accountEmail} was found in user table, this step is skipped. If you think this is incorrect, consider removing user from the table and try running the setup again.`);
      }
    });

    await test.step('Create new customer', async () => {
      await registerPage.createNewAccount(
        inputValues.accountCreation.firstNameValue,
        inputValues.accountCreation.lastNameValue,
        accountEmail,
        accountPassword,
        true
      );
    });
  });
});
