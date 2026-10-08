// @ts-check

import { expect, type Page } from '@playwright/test';
import { UIReference, outcomeMarker } from '@config';
import { requireEnv } from '@utils/env.utils';

class AdminOrders {
  readonly page: Page;

  constructor(page: Page) {
	this.page = page;
  }

  /**
   * @feature Navigate to Admin orders page
   * @scenario User navigates to the admin orders page
   * @given
   * @when I navigate to the orders page
   * @then I should see the orders list
   * @and I should see the saved order number id
   */
  async checkIfOrderExists(orderNumber: string){
	const mainMenuSalesButton = this.page.getByRole('link', { name: UIReference.adminPage.navigation.salesButtonLabel });
	const ordersButtonLink = this.page.getByRole('link', { name: UIReference.adminPage.subNavigation.ordersButtonLabel }).first();

	await expect(async () => {
	  await mainMenuSalesButton.click();
	  await expect(ordersButtonLink).toBeVisible();
	}).toPass();

	await ordersButtonLink.click();

	const ordersSearchField = this.page.getByRole('textbox', {name: UIReference.adminGeneral.tableSearchFieldLabel});

	// Wait for URL. If loading symbol is visible, wait for it to go away
	await this.page.waitForURL(`**/${requireEnv('MAGENTO_ADMIN_SLUG')}/sales/order/index/**`);
	if (await this.page.locator(UIReference.adminGeneral.loadingSpinnerLocator).isVisible()) {
	  await this.page.locator(UIReference.adminGeneral.loadingSpinnerLocator).waitFor({state: 'hidden'});
	}

	await ordersSearchField.waitFor();
	await ordersSearchField.fill(orderNumber);
	await this.page.getByRole('button', {name: UIReference.adminGeneral.searchButtonLabel}).click();

	if (await this.page.locator(UIReference.adminGeneral.loadingSpinnerLocator).isVisible()) {
	  await this.page.locator(UIReference.adminGeneral.loadingSpinnerLocator).waitFor({state: 'hidden'});
	}

	// Loop to ensure the 'results found' text is visible
	await expect(async() =>{
	  await this.page.getByText(outcomeMarker.adminGeneral.searchResultsFoundText).first();
	}).toPass();

	await expect(this.page.getByRole('cell', {name:orderNumber}).locator('div')).toBeVisible();
  }
}

export default AdminOrders;