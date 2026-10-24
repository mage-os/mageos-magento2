// @ts-check

import { test, expect } from '@playwright/test';
import { outcomeMarker } from '@config';

import MainMenuPage from '@poms/frontend/mainmenu.page';
import HomePage from '@poms/frontend/home.page';

test('Add_product_on_homepage_to_cart',{ tag: ['@homepage', '@cold']}, async ({page}) => {
  const homepage = new HomePage(page);
  const mainmenu = new MainMenuPage(page);

  await page.goto('');
  await homepage.addHomepageProductToCart();
  await mainmenu.openMiniCart();
  await expect(page.getByText('x ' + outcomeMarker.homePage.firstProductName), 'product should be visible in cart').toBeVisible();
});
