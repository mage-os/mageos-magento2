// @ts-check

import { test, expect } from '@playwright/test';
import { faker } from '@faker-js/faker';
import { UIReference, outcomeMarker, slugs, inputValues} from '@config';
import { requireEnv } from '@utils/env.utils';

import AccountPage from '@poms/frontend/account.page';
import LoginPage from '@poms/frontend/login.page';
import MainMenuPage from '@poms/frontend/mainmenu.page';
import NewsletterSubscriptionPage from '@poms/frontend/newsletter.page';
import RegisterPage from '@poms/frontend/register.page';

// Before each test, log in
test.beforeEach(async ({ page, browserName }) => {
  const browserEngine = browserName?.toUpperCase() || "UNKNOWN";
  const emailInputValue = requireEnv(`MAGENTO_EXISTING_ACCOUNT_EMAIL_${browserEngine}`);
  const passwordInputValue = requireEnv('MAGENTO_EXISTING_ACCOUNT_PASSWORD');

  const loginPage = new LoginPage(page);
  await loginPage.login(emailInputValue, passwordInputValue);
});

test.describe('Account information actions', {annotation: {type: 'Account Dashboard', description: 'Test for Account Information'},}, () => {

  test.beforeEach(async ({page}) => {
    await page.goto(slugs.account.accountOverviewSlug);
    await page.waitForLoadState();

    await expect(
      page.locator('.block-dashboard-addresses .block-title', {
        hasText: UIReference.address.addressBookTitle,
      })
    ).toBeVisible();
  });
});

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
   * @feature Add an address
   * @given I am logged in
   * @and I am on the account dashboard page
   * @when I go to the page where I can add another address
   * @when I fill in the required information
   * @and I click the save button
   * @then I should see a notification my address has been updated.
   * @and The new address should be listed
   */
  test('Add_an_address',{ tag: ['@address-actions', '@hot'] }, async ({page}) => {
    await page.goto(slugs.account.addressNewSlug);
    const accountPage = new AccountPage(page);

    const address = `${faker.location.streetAddress()} ${Math.floor(Math.random() * 100 + 1)}`;
    const company = faker.company.name();

    await accountPage.addNewAddress({ company: company, street: address});

    await expect(page.getByText(address).first(), `Expect new address to be listed`).toBeVisible();
    //await expect(page.getByText(company).first(), `Expect new company name to be listed`).toBeVisible();
  });

  /**
   * @feature Magento 2 Update Address in Account
   * @scenario User updates an existing address to their account
   * @given I am logged in
   *  @and I am on the account dashboard page
   * @when I go to the page where I can see my address(es)
   * @when I click on the button to edit the address
   *   @and I fill in the required information correctly
   *   @then I click the save button
   * @then I should see a notification my address has been updated.
   *  @and The updated address should be visible in the addres book page.
   */
  test('Edit_existing_address',{ tag: ['@address-actions', '@hot'] }, async ({page}) => {
    const accountPage = new AccountPage(page);
    await page.goto(slugs.account.addressBookSlug);
    let editAddressButton = page.getByRole('link', {name: UIReference.accountDashboard.editAddressIconButton}).first();
    let isDefaultAddress = false;

    if(await editAddressButton.isHidden()){
      // The edit address button was not found, add another address first.
      if(await page.getByRole('link', { name: 'Change Shipping Address' }).isVisible()) {
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
   * @feature Magento 2 Delete Address from account
   * @scenario User removes an address from their account
   * @given I am logged in
   *  @and I am on the account dashboard page
   * @when I go to the page where I can see my address(es)
   * @when I click the trash button for the address I want to delete
   *   @and I click the confirmation button
   * @then I should see a notification my address has been deleted.
   *  @and The address should be removed from the overview.
   */
  test('Delete_an_address',{ tag: ['@address-actions', '@hot'] }, async ({page}) => {
    const accountPage = new AccountPage(page);
    await page.goto(slugs.account.addressBookSlug);

    let deleteAddressButton = page.getByRole('link', {name: UIReference.accountDashboard.addressDeleteIconButton}).first();

    if(await deleteAddressButton.isHidden()) {
      // The delete address button was not found, add another address first.
      await page.goto(slugs.account.addressNewSlug);
      await accountPage.addNewAddress();
    }
    await accountPage.deleteFirstAddressFromAddressBook();
  });
});
