// @ts-check

import {expect, type Locator, type Page, test, TestInfo} from '@playwright/test';
import { faker } from '@faker-js/faker';
import { UIReference, outcomeMarker, inputValues, slugs } from '@config';

import LoginPage from '@poms/frontend/login.page';

class AccountPage {
  readonly page: Page;
  readonly accountDashboardTitle: Locator;
  readonly firstNameField: Locator;
  readonly lastNameField: Locator;
  readonly companyNameField: Locator;
  readonly phoneNumberField: Locator;
  readonly loginPage: LoginPage;
  readonly streetAddressField: Locator;
  readonly zipCodeField: Locator;
  readonly cityField: Locator;
  readonly countrySelectorField: Locator;
  readonly stateSelectorField: Locator;
  readonly stateInputField: Locator;
  readonly saveAddressButton: Locator;
  readonly addNewAddressButton: Locator;
  readonly deleteAddressButton: Locator;
  readonly editAddressButton: Locator;
  readonly changePasswordSwitch: Locator;
  readonly changeEmailCheck: Locator;
  readonly currentPasswordField: Locator;
  readonly newPasswordField: Locator;
  readonly confirmNewPasswordField: Locator;
  readonly genericSaveButton: Locator;
  readonly accountCreationFirstNameField: Locator;
  readonly accountCreationLastNameField: Locator;
  readonly accountCreationEmailField: Locator;
  readonly accountCreationPasswordField: Locator;
  readonly accountCreationPasswordRepeatField: Locator;
  readonly accountCreationConfirmButton: Locator;
  readonly accountInformationField: Locator;

  constructor(page: Page) {
    this.page = page;
    this.loginPage = new LoginPage(page);

    this.accountDashboardTitle = page.getByRole('heading', { name: UIReference.accountDashboard.accountDashboardTitleLabel });
    this.firstNameField = page.getByLabel(UIReference.personalInformation.firstNameLabel);
    this.lastNameField = page.getByLabel(UIReference.personalInformation.lastNameLabel);
    // this.companyNameField = page.getByLabel(UIReference.newAddress.companyNameLabel);
    this.companyNameField = page.getByRole('textbox', {name: UIReference.newAddress.companyNameLabel});
    this.phoneNumberField = page.getByLabel(UIReference.newAddress.phoneNumberLabel);
    this.streetAddressField = page.getByLabel(UIReference.newAddress.streetAddressLabel, { exact: true });
    this.zipCodeField = page.getByLabel(UIReference.newAddress.zipCodeLabel);
    this.cityField = page.getByLabel(UIReference.newAddress.cityNameLabel);
    this.countrySelectorField = page.getByLabel(UIReference.newAddress.countryLabel);

    this.stateInputField = page.getByLabel(UIReference.newAddress.provinceSelectLabel);
    this.stateSelectorField = this.stateInputField.filter({ hasText: UIReference.newAddress.provinceSelectFilterLabel });

    this.saveAddressButton = page.getByRole('button', { name: UIReference.newAddress.saveAdressButton });

    // Account Information elements
    this.changePasswordSwitch = page.getByRole('switch', { name: UIReference.personalInformation.changePasswordSwitchLabel });
    this.changeEmailCheck = page.getByRole('switch', { name: UIReference.personalInformation.changeEmailCheckLabel });
    this.currentPasswordField = page.getByLabel(UIReference.credentials.currentPasswordFieldLabel);
    this.newPasswordField = page.getByLabel(UIReference.credentials.newPasswordFieldLabel, { exact: true });
    this.confirmNewPasswordField = page.getByLabel(UIReference.credentials.newPasswordConfirmFieldLabel);
    this.genericSaveButton = page.getByRole('button', { name: UIReference.general.genericSaveButtonLabel });

    // Account Creation elements
    this.accountCreationFirstNameField = page.getByLabel(UIReference.personalInformation.firstNameLabel);
    this.accountCreationLastNameField = page.getByLabel(UIReference.personalInformation.lastNameLabel);
    this.accountCreationEmailField = page.getByLabel(UIReference.credentials.emailFieldLabel, { exact: true });
    this.accountCreationPasswordField = page.getByLabel(UIReference.credentials.passwordFieldLabel, { exact: true });
    this.accountCreationPasswordRepeatField = page.getByLabel(UIReference.credentials.passwordConfirmFieldLabel);
    this.accountCreationConfirmButton = page.getByRole('button', { name: UIReference.accountCreation.createAccountButtonLabel });

    this.accountInformationField = page.locator(UIReference.accountDashboard.accountInformationFieldLocator).first();

    // Address Book elements
    this.addNewAddressButton = page.getByRole('button', { name: UIReference.accountDashboard.addAddressButtonLabel });
    this.deleteAddressButton = page.getByRole('link', { name: UIReference.accountDashboard.addressDeleteIconButton }).first();
    this.editAddressButton = page.getByRole('link', { name: UIReference.accountDashboard.editAddressIconButton }).first();
  }

  /**
   * Add an address to test account
   * @param values - Optional values to fill the form with
   */
  async addNewAddress(values?: {
    company?: string;
    phone?: string;
    street?: string;
    zip?: string;
    city?: string;
    state?: string;
    country?: string;
  }) {
    let addressAddedNotification = outcomeMarker.address.newAddressAddedNotifcation;

    await expect(this.firstNameField, `first name should be pre-filled`).not.toBeEmpty();
    await expect(this.lastNameField, `last name should be pre-filled`).not.toBeEmpty();

    const phone = values?.phone || faker.phone.number({style: 'national'}); // Use 'national' style to prevent input errors
    const streetName = values?.street || faker.location.streetAddress();
    const zipCode = values?.zip || faker.location.zipCode();
    const cityName = values?.city || faker.location.city();
    const stateName = values?.state || faker.location.state();
    const country = values?.country || faker.helpers.arrayElement(inputValues.addressCountries);
    if (values?.company) {
      await this.companyNameField.fill(values.company);
    }

    await this.phoneNumberField.fill(phone);
    await this.streetAddressField.fill(streetName);
    await this.zipCodeField.fill(zipCode);
    await this.cityField.fill(cityName);

    // If default selected country == country we want to use for the test,
    // don't re-select it.
    const defaultSelectedCountry = await this.countrySelectorField.evaluate(
      (select: HTMLSelectElement) => select.options[select.selectedIndex]?.text
    );

    if(country !== defaultSelectedCountry) {
      await this.countrySelectorField.selectOption({label: country});
    }
    const regionDropdown = this.page.locator(UIReference.newAddress.regionDropdownLocator);
    const regionInputField = this.page.getByRole('textbox', {name: UIReference.newAddress.provinceSelectLabel});

    if(country !== 'United States') {
      await expect(regionDropdown, `Dropdown should not be visible`).toBeHidden();
      await expect(regionInputField, `Region input field should be visible`).toBeVisible();

      await regionInputField.fill(stateName);
    } else {
      await expect(regionInputField, `Dropdown should not be visible`).toBeHidden();
      await expect(regionDropdown, `State input field should be editable`).toBeEditable();
      // await regionDropdown.selectOption(stateName);
      await this.stateSelectorField.selectOption(stateName);
      // Timeout because Alpine uses an @input.debounce to delay the activation of the event
      // Standard debounce is 250ms.
      await this.page.waitForTimeout(1000);
    }

    await this.saveAddressButton.scrollIntoViewIfNeeded();
    await this.saveAddressButton.click();
    await this.page.waitForLoadState();

    await expect.soft(this.page.getByText(addressAddedNotification), `message that confirms actions should be visible`).toBeVisible();
  }



  async editExistingAddress(values?: {
    firstName?: string;
    lastName?: string;
    company?: string;
    phone?: string;
    street?: string;
    zip?: string;
    city?: string;
    state?: string;
    country?: string;
  }, defaultAddress: boolean = false) {
    let addressModifiedNotification = outcomeMarker.address.newAddressAddedNotifcation;

    const firstName = values?.firstName || faker.person.firstName();
    const lastName = values?.lastName || faker.person.lastName();
    const companyName = values?.company || faker.company.name();
    const phone = values?.phone || faker.phone.number({style: 'national'}); // Use 'national' style to prevent input errors
    const streetName = values?.street || faker.location.streetAddress();
    const zipCode = values?.zip || faker.location.zipCode();
    const cityName = values?.city || faker.location.city();
    const stateName = values?.state || faker.location.state();
    const country = values?.country || faker.helpers.arrayElement(inputValues.addressCountries);

    // click the correct button based on if there's more than one address (defaultAddress boolean)
    defaultAddress ? await this.page.getByRole('link', { name: 'Change Shipping Address arrow' }).click() : await this.editAddressButton.click();

    let oldAddress = await this.streetAddressField.inputValue();

    await expect(this.firstNameField,`first name field should be filled in automatically`).not.toBeEmpty();
    await expect(this.lastNameField, `first name field should be filled in automatically`).not.toBeEmpty();

    // contact information section
    await this.firstNameField.fill(firstName);
    await this.lastNameField.fill(lastName);
    await this.companyNameField.fill(companyName);
    await this.phoneNumberField.fill(phone);

    // Address information section
    await this.streetAddressField.fill(streetName);
    await this.zipCodeField.fill(zipCode);
    await this.cityField.fill(cityName);

    // If default selected country == country we want to use for the test,
    // don't re-select it.
    const defaultSelectedCountry = await this.countrySelectorField.evaluate( (select: HTMLSelectElement) => select.options[select.selectedIndex]?.text);
    if(country !== defaultSelectedCountry) {
      await this.countrySelectorField.selectOption({label: country});
    }

    const regionDropdown = this.page.locator(UIReference.newAddress.regionDropdownLocator);
    const regionInputField = this.page.getByRole('textbox', {name: UIReference.newAddress.provinceSelectLabel});

    if(country !== 'United States') {
      await expect(regionDropdown, `Dropdown should not be visible`).toBeHidden();
      await expect(regionInputField, `Region input field should be visible`).toBeVisible();

      await regionInputField.fill(stateName);
    } else {
      // await regionDropdown.selectOption(stateName);
      await this.stateSelectorField.selectOption(stateName);
      // Timeout because Alpine uses an @input.debounce to delay the activation of the event
      // Standard debounce is 250ms.
      await this.page.waitForTimeout(1000);
    }

    await this.saveAddressButton.scrollIntoViewIfNeeded();
    await this.saveAddressButton.click();
    await this.page.waitForLoadState();

    await expect.soft(this.page.getByText(addressModifiedNotification)).toBeVisible();
    // await expect(this.page.getByText(streetName).last()).toBeVisible();
    if (oldAddress != null) await expect(this.page.getByText(oldAddress)).not.toBeVisible();
  }

  async deleteFirstAddressFromAddressBook() {
    let addressDeletedNotification = outcomeMarker.address.addressDeletedNotification;
    let addressBookSection = this.page.locator(UIReference.accountDashboard.addressBookArea);

    this.page.on('dialog', async (dialog) => {
      if (dialog.type() === 'confirm') {
        await dialog.accept();
      }
    });

    // Retrieve all text in the 'address book' section
    let addressBookArray = await addressBookSection.allInnerTexts();
    // split by each new line
    let arraySplit = addressBookArray[0].split('\n');
    // Retrieve index 8, because:
    // index 0 to 5 are the table headers (i.e. Company, Name etc.)
    // index 6 is company, index 7 is name, and index 8 is the first address value.
    // if this table changes, the index number should change.
    let addressToBeDeleted = arraySplit[8];

    // Annotate the report so the user knows what address should be deleted
    test.info().annotations.push({type: `Address to be deleted`, description: addressToBeDeleted});

    await this.deleteAddressButton.click();
    await this.page.waitForLoadState();

    await expect(this.page.getByText(addressDeletedNotification)).toBeVisible();
    await expect(addressBookSection, `${addressToBeDeleted} should not be visible`).not.toContainText(addressToBeDeleted);
  }

  async updatePassword(currentPassword: string, newPassword: string) {
    let passwordUpdatedNotification = outcomeMarker.account.changedPasswordNotificationText;
    await this.changePasswordSwitch.check();
    await this.currentPasswordField.fill(currentPassword);
    await this.newPasswordField.fill(newPassword);
    await this.confirmNewPasswordField.fill(newPassword);
    await this.genericSaveButton.click();

    await this.page.waitForURL(slugs.account.loginSlug);
    await expect(this.page.getByText(passwordUpdatedNotification)).toBeVisible();
  }

  async updateEmail(currentPassword: string, newEmail: string) {
    let accountUpdatedNotification = outcomeMarker.account.changedPasswordNotificationText;
    await this.changeEmailCheck.check();
    await this.accountCreationEmailField.fill(newEmail);
    await this.currentPasswordField.fill(currentPassword);
    await this.genericSaveButton.click();
    await this.page.waitForLoadState();
    await this.loginPage.login(newEmail, currentPassword);
    await expect(this.accountInformationField, `Account information should contain email: ${newEmail}`).toContainText(newEmail);
  }

  async deleteAllAddresses() {
    let addressDeletedNotification = outcomeMarker.address.addressDeletedNotification;

    this.page.on('dialog', async (dialog) => {
      if (dialog.type() === 'confirm') {
        await dialog.accept();
      }
    });

    while (await this.deleteAddressButton.isVisible()) {
      await this.deleteAddressButton.click();
      await this.page.waitForLoadState();
      await expect.soft(this.page.getByText(addressDeletedNotification)).toBeVisible();
    }
  }
}

export default AccountPage;
