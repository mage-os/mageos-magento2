// @ts-check

import { expect, type Locator, type Page } from '@playwright/test';
import { UIReference, slugs } from '@config';
import MainmenuPage from '@poms/frontend/mainmenu.page';

class LoginPage {
  readonly page: Page;
  readonly loginEmailField: Locator;
  readonly loginPasswordField: Locator;
  readonly loginButton: Locator;

  constructor(page: Page) {
    this.page = page;
    //this.loginEmailField = page.getByRole('textbox', {name: UIReference.credentials.emailFieldLabel, exact: true});
    //this.loginPasswordField = page.getByRole('textbox', {name: UIReference.credentials.passwordFieldLabel});
    this.loginEmailField = page.locator('input[name="login[username]"]');
    this.loginPasswordField = page.locator('input[name="login[password]"]');
    this.loginButton = page.getByRole('button', { name: UIReference.credentials.loginButtonLabel });
  }

  async login(email: string, password: string){
    const mainmenu = new MainmenuPage(this.page);

    await this.page.goto(slugs.account.loginSlug);
    await this.loginEmailField.fill(email);
    await this.loginPasswordField.fill(password);
    // usage of .press("Enter") to prevent webkit issues with button.click();
    await this.loginButton.press("Enter");
    // await this.loginButton.click({force: true});

    await this.page.waitForURL(slugs.account.accountOverviewSlug);

    // wait for page to be done loading
    await this.page.waitForURL('/customer/account/');

    // Open the menu, then check the 'Sign Out' button is visible
    await mainmenu.mainMenuAccountButton.waitFor();
    await mainmenu.mainMenuAccountButton.click();
    await expect(mainmenu.mainMenuLogoutItem, 'Sign Out button is visible, user is logged in').toBeVisible();
  }

  async loginExpectError(email: string, password: string, errorMessage: string) {
    await this.page.goto(slugs.account.loginSlug);
    await this.loginEmailField.fill(email);
    await this.loginPasswordField.fill(password);
    await this.loginButton.press('Enter');
    await this.page.waitForLoadState('networkidle');

    await expect(this.page, 'Should stay on login page').toHaveURL(new RegExp(slugs.account.loginSlug));
  }
}

export default LoginPage;
