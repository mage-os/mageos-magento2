// @ts-check

import { expect, type Locator, type Page } from '@playwright/test';
import { UIReference, outcomeMarker, slugs} from '@config';
import MainMenuPage from "@poms/frontend/mainmenu.page";

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
    const form = page.locator('#form-validate');
    this.accountCreationFirstNameField = page.getByLabel(UIReference.personalInformation.firstNameLabel);
    //this.accountCreationFirstNameField = form.locator('input[name="firstname"]');
    this.accountCreationLastNameField = page.getByLabel(UIReference.personalInformation.lastNameLabel);
    //this.accountCreationLastNameField = form.locator('input[name="lastname"]');
    this.accountCreationEmailField = page.getByRole('textbox', {name: UIReference.credentials.emailFieldLabel, exact: true});
    //this.accountCreationEmailField = form.locator('input[name="email"]');
    this.accountCreationPasswordField = page.getByRole('textbox', {name: UIReference.credentials.passwordFieldLabel, exact:true});
    //this.accountCreationPasswordField = form.locator('input[name="password"]');
    this.accountCreationPasswordRepeatField = page.getByRole('textbox', {name: UIReference.credentials.passwordConfirmFieldLabel});
    //this.accountCreationPasswordRepeatField = form.locator('input[name="password_confirmation"]');
    this.accountCreationConfirmButton = page.getByRole('button', {name: UIReference.accountCreation.createAccountButtonLabel});
    //this.accountCreationConfirmButton = form.locator('button[type="submit"]');
  }


  async createNewAccount(firstName: string, lastName: string, email: string, password: string, isSetup: boolean = false){
    //let accountInformationField = this.page.locator(UIReference.accountDashboard.accountInformationFieldLocator).first();
    await this.page.goto(slugs.account.createAccountSlug);

    await expect(async () => {
      await expect(this.page.getByRole('heading',
          { name: UIReference.accountCreation.createAccountTitleText }),
        `Heading "${UIReference.accountCreation.createAccountTitleText}" is visible`).toBeVisible();
    }).toPass();
    // await expect(async () => {
    //   await expect(
    //     this.page.getByRole('heading', { level: 1 })
    //   ).toHaveText('My Account');
    // }).toPass();

    //const mainMenu = new MainMenuPage(this.page);
    //await mainMenu.logout()
    await this.page.goto(slugs.account.createAccountSlug);
    await expect(async () => {
      await expect(
        this.page.getByRole('heading', { level: 1 })
      ).toHaveText('Create New Customer Account');
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
      const accountInfoBlock = this.page.locator('.column.main');
      const contactInfoBox = accountInfoBlock.locator('h3:has-text("Contact Information")').locator('..');
      const contactInfoContent = contactInfoBox.locator('p');

      await expect(contactInfoContent, `Account information should contain email: ${email}`)
        .toContainText(email);
    }
  }
}

export default RegisterPage;
