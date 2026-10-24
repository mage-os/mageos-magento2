// @ts-check

import { expect, type Locator, type Page } from '@playwright/test';
import {UIReference, inputValues, outcomeMarker} from '@config';

class AdminMarketing {
  readonly page: Page;

  constructor(page: Page) {
	this.page = page;
  }

  /**
   * @feature Cart Price Rules Configuration
   * @scenario Add or activate a cart price rule with a specific coupon code
   * @given the admin is on the Magento dashboard
   * @when the coupon exists but is inactive
   * @then the admin activates the existing coupon and saves the rule
   * @but if the coupon does not exist
   * @then the admin creates a new cart price rule with the given coupon code
   * @and selects all websites and customer groups
   * @and sets the coupon type and discount amount
   * @and clicks the Save button
   * @then the system displays a success message confirming the rule was saved
   */
  async addCartPriceRule(magentoCouponCode: string){
	let resultMessage = "";

	// Force specific viewport size to deal with webkit issues
	await this.page.setViewportSize({
	  width: 1920,
	  height: 1080
	})

	const mainMenuMarketingButton = this.page.getByRole('link', {name: UIReference.adminPage.navigation.marketingButtonLabel});
	const cartPriceRulesLink = this.page.getByRole('link', {name: UIReference.adminPage.subNavigation.cartPriceRulesButtonLabel});
	await expect(mainMenuMarketingButton, `Button for Marketing is visible`).toBeVisible();

	await expect(async () => {
	  await mainMenuMarketingButton.click();
	  await expect(cartPriceRulesLink, `Button for Cart Price Rules is visible`).toBeVisible();
	}).toPass();

	await cartPriceRulesLink.click();

	const addCartPriceRuleButton = this.page.getByRole('button', {name: UIReference.cartPriceRulesPage.addCartPriceRuleButtonLabel});
	await addCartPriceRuleButton.waitFor();

	// Use search field to check for coupon codes.
	const couponSearchField = this.page.locator(UIReference.adminPage.couponSearchFieldLocator);
	await couponSearchField.fill(magentoCouponCode);
	await this.page.getByRole('button', {name: UIReference.general.searchButtonLabel}).click();

	await expect(this.page.getByText(UIReference.adminPage.searchResultsText), `Search results text visible`).toBeVisible();

	const couponCellField = this.page.getByRole('cell', { name: outcomeMarker.magentoAdmin.noResultsFoundText });

	if(await couponCellField.isHidden()){
	  const couponStatusField = this.page.locator('tr').filter({hasText:magentoCouponCode}).first();
	  const couponStatus = await couponStatusField.innerText();
	  if(couponStatus.includes(UIReference.cartPriceRulesPage.couponCodeActiveStatusText)){
		resultMessage = 'Coupon already exists and is active.';
	  } else {
		// coupon has been found, but is not active.
		await couponStatusField.click();
		const activeStatusSwitcher = this.page.locator(UIReference.cartPriceRulesPage.activeStatusSwitcherLocator).first();
		const activeStatusLabel = this.page.locator(UIReference.cartPriceRulesPage.activeStatusLabelLocator).first();

		await expect(activeStatusLabel, `Active/Disable toggle is visible`).toBeVisible();
		await activeStatusSwitcher.click();

		const saveCouponButton = this.page.getByRole('button', {name:UIReference.cartPriceRulesPage.saveRuleButtonLabel, exact:true});
		await saveCouponButton.click();

		await expect(this.page.locator(
		  UIReference.general.messageLocator).filter({hasText: outcomeMarker.magentoAdmin.couponRuleSavedText}
		), "Message 'you saved the rule' is visible").toBeVisible();
		resultMessage = `Coupon code ${magentoCouponCode} has been activated.`;
	  }
	} else {
	  // coupon is not set
	  await addCartPriceRuleButton.click();

	  const websiteSelector = this.page.getByLabel(UIReference.cartPriceRulesPage.websitesSelectLabel);
	  await websiteSelector.evaluate(select => {
		const s = select as HTMLSelectElement;
		for (const option of s.options) {
		  option.selected = true;
		}
		select.dispatchEvent(new Event('change'));
	  });

	  const customerGroupsSelector = this.page.getByLabel(UIReference.cartPriceRulesPage.customerGroupsSelectLabel, { exact: true });
	  await customerGroupsSelector.evaluate(select => {
		const s = select as HTMLSelectElement;
		for (const option of s.options) {
		  option.selected = true;
		}
		select.dispatchEvent(new Event('change'));
	  });

	  await this.page.getByRole('textbox', { name: UIReference.cartPriceRulesPage.ruleNameFieldLabel }).fill(magentoCouponCode);
	  await this.page.locator(UIReference.cartPriceRulesPage.couponTypeSelectField).selectOption({ label: inputValues.coupon.couponType });
	  await this.page.getByLabel(UIReference.cartPriceRulesPage.couponCodeFieldLabel).fill(magentoCouponCode);

	  await this.page.getByText(UIReference.cartPriceRulesPage.actionsSubtitleLabel, { exact: true }).click();
	  await this.page.getByLabel(UIReference.cartPriceRulesPage.discountAmountFieldLabel).fill('10');

	  const couponSaveButton = this.page.getByRole('button', { name: UIReference.cartPriceRulesPage.saveRuleButtonLabel, exact: true });
	  await couponSaveButton.scrollIntoViewIfNeeded();
	  await couponSaveButton.click({force:true});
	  await expect(this.page.locator(
		UIReference.general.messageLocator).filter({hasText: outcomeMarker.magentoAdmin.couponRuleSavedText}
	  ), "Message 'you saved the rule' is visible").toBeVisible();
	  resultMessage = `Coupon code ${magentoCouponCode} has been set and activated.`;
	}

	// Clear the search field
	await couponSearchField.waitFor();
	const clearSearchButton = this.page.getByRole('button', { name: UIReference.cartPriceRulesPage.clearSearchButtonLabel });
	await clearSearchButton.click();
	await expect(couponSearchField, `Coupon Code search field is empty`).toBeEmpty();

	return resultMessage;
  };
}

export default AdminMarketing;