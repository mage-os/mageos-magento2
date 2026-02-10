// @ts-check

import {test} from '@playwright/test';
import {faker} from '@faker-js/faker';
import { inputValues } from '@config';

import RegisterPage from '@poms/frontend/register.page';
import { requireEnv } from '@utils/env.utils';

// Reset storageState to ensure we're not logged in before running these tests.
test.use({ storageState: { cookies: [], origins: [] } });

/**
 * @feature Magento 2 Account Creation
 * @scenario The user creates an account on the website
 *  @given I am on any Magento 2 page
 *    @when I go to the account creation page
 *    @and I fill in the required information correctly
 *  @then I click the 'Create account' button
 *  @then I should see a messsage confirming my account was created
 */
test('User_registers_an_account', { tag: ['@account-creation', '@hot'] }, async ({page, browserName}, testInfo) => {
  const registerPage = new RegisterPage(page);

  // Retrieve desired password from .env file
  const existingAccountPassword = requireEnv('MAGENTO_EXISTING_ACCOUNT_PASSWORD');
  let firstName = faker.person.firstName();
  let lastName = faker.person.lastName();

  const browserEngine = browserName?.toUpperCase() || "UNKNOWN";
  let randomNumber = Math.floor(Math.random() * 100);
  let emailHandle = inputValues.accountCreation.emailHandleValue;
  let emailHost = inputValues.accountCreation.emailHostValue;
  const accountEmail = `${emailHandle}${randomNumber}-${browserEngine}@${emailHost}`;
  //const accountEmail = process.env[`MAGENTO_EXISTING_ACCOUNT_EMAIL_${browserEngine}`];

  if (!accountEmail) {
    throw new Error(`Generated account email is invalid.`);
  }
  // end of browserNameEmailSection

  await registerPage.createNewAccount(firstName, lastName, accountEmail, existingAccountPassword);
  testInfo.annotations.push({ type: 'Notification: account created!', description: `Credentials used: ${accountEmail}, password: ${existingAccountPassword}` });
});
