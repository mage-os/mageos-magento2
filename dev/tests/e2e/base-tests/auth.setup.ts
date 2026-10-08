// @ts-check

import { test as setup, expect } from '@playwright/test';
import path from 'path';
import { UIReference, slugs } from '@config';
import { requireEnv } from '@utils/env.utils';

const authFile = path.join(__dirname, '../playwright/.auth/user.json');

setup('authenticate', async ({ page, browserName }) => {
  const browserEngine = browserName?.toUpperCase() || "UNKNOWN";
  const emailInputValue = requireEnv(`MAGENTO_EXISTING_ACCOUNT_EMAIL_${browserEngine}`);
  const passwordInputValue = requireEnv('MAGENTO_EXISTING_ACCOUNT_PASSWORD');

  // Perform authentication steps. Replace these actions with your own.
  await page.goto(slugs.account.loginSlug);
  await page.getByLabel(UIReference.credentials.emailFieldLabel, {exact: true}).fill(emailInputValue);
  await page.getByLabel(UIReference.credentials.passwordFieldLabel, {exact: true}).fill(passwordInputValue);
  await page.getByRole('button', { name: UIReference.credentials.loginButtonLabel }).click();
  // Wait until the page receives the cookies.
  //
  // Sometimes login flow sets cookies in the process of several redirects.
  // Wait for the final URL to ensure that the cookies are actually set.
  // await page.waitForURL('');
  // Alternatively, you can wait until the page reaches a state where all cookies are set.
  await expect(page.getByRole('link', { name: UIReference.mainMenu.myAccountLogoutItem })).toBeVisible();

  // End of authentication steps.

  await page.context().storageState({ path: authFile });
});
