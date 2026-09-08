// @ts-check

import { expect, type Locator, type Page } from '@playwright/test';
import { UIReference, outcomeMarker, slugs} from '@config';

class RegisterPage {
  readonly page: Page;
  readonly accountCreationFirstNameField: Locator;
  readonly accountCreationLastNameField: Locator;
  readonly accountCreationEmailField: Locator;
  readonly accountCreationPasswordField: Locator;
  readonly accountCreationPasswordRepeatField: Locator;
  readonly accountCreationConfirmButton: Locator;

  constructor(page: Page){
    this.page = page;
    this.accountCreationFirstNameField = page.getByLabel(UIReference.personalInformation.firstNameLabel);
    this.accountCreationLastNameField = page.getByLabel(UIReference.personalInformation.lastNameLabel);
    this.accountCreationEmailField = page.getByRole('textbox', {name: UIReference.credentials.emailFieldLabel, exact: true});
    this.accountCreationPasswordField = page.getByRole('textbox', {name: UIReference.credentials.passwordFieldLabel, exact:true});
    this.accountCreationPasswordRepeatField = page.getByRole('textbox', {name: UIReference.credentials.passwordConfirmFieldLabel});
    this.accountCreationConfirmButton = page.getByRole('button', {name: UIReference.accountCreation.createAccountButtonLabel});
  }


  async createNewAccount(firstName: string, lastName: string, email: string, password: string, isSetup: boolean = false){
    let accountInformationField = this.page.locator(UIReference.accountDashboard.accountInformationFieldLocator).first();
    await this.page.goto(slugs.account.createAccountSlug);

    await expect(async () => {
      await expect(this.page.getByRole('heading',
          { name: UIReference.accountCreation.createAccountTitleText }),
        `Heading "${UIReference.accountCreation.createAccountTitleText}" is visible`).toBeVisible();
    }).toPass();

    await this.accountCreationFirstNameField.fill(firstName);
    await this.accountCreationLastNameField.fill(lastName);
    await this.accountCreationEmailField.fill(email);
    await this.accountCreationPasswordField.fill(password);
    await this.accountCreationPasswordRepeatField.fill(password);
    await this.accountCreationConfirmButton.click();

    if(!isSetup) {
      await this.page.waitForLoadState();
      // Assertions: Account created notification, navigated to account page, email visible on page
      await expect(this.page.getByText(outcomeMarker.account.accountCreatedNotificationText), 'Account creation notification should be visible').toBeVisible();

      await this.page.goto(slugs.account.accountOverviewSlug);
      await expect(this.page.getByRole('heading',
        {name: UIReference.accountDashboard.accountDashboardTitleLabel, level:2}),
        `Heading "${UIReference.accountDashboard.accountDashboardTitleLabel}" is visible`).toBeVisible();
      // await expect(this.page, 'Should be redirected to account overview page').toHaveURL(new RegExp('.+' + slugs.account.accountOverviewSlug));
      await expect(accountInformationField, `Account information should contain email: ${email}`).toContainText(email);
    }
  }
}

export default RegisterPage;
