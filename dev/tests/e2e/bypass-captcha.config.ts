// @ts-check

/**
 * This file is used to set up the CAPTCHA bypass for your tests.
 * It will set the global cookie to bypass CAPTCHA for Magento 2.
 * See: https://github.com/elgentos/magento2-bypass-captcha-cookie
 * 
 */

import { FullConfig } from '@playwright/test';
import * as playwright from 'playwright';
import dotenv from 'dotenv';

dotenv.config();

async function globalSetup(config: FullConfig) {
  const bypassCaptcha = process.env.CAPTCHA_BYPASS === 'true';

  for (const project of config.projects) {
    const { storageState, browserName = 'chromium' } = project.use || {};
    if (storageState) {
      const browserType = playwright[browserName];
      const browser = await browserType.launch();
      const context = await browser.newContext();

      if (bypassCaptcha) {
        // Set the global cookie to bypass CAPTCHA
        await context.addCookies([{
          name: 'disable_captcha', // this cookie will be read by 'magento2-bypass-captcha-cookie' module.
          value: '', // Fill with generated token.
          domain: 'hyva-demo.elgentos.io', // Replace with your domain
          path: '/',
          httpOnly: true,
          secure: true,
          sameSite: 'Lax',
        }]);
        console.log(`CAPTCHA bypass enabled for browser: ${project.name}`);
      } else {
        // Do nothing.
      }

      await context.storageState({ path: `./auth-storage/${project.name}-storage-state.json` });
      await browser.close();
    }
  }
}

export default globalSetup;
