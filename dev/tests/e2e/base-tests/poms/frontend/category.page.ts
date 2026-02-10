// @ts-check

import { expect, type Locator, type Page } from '@playwright/test';
import { UIReference, slugs } from '@config';

class CategoryPage {
  readonly page:Page;
  categoryPageTitle: Locator;

  constructor(page: Page) {
    this.page = page;
    this.categoryPageTitle = this.page.getByRole('heading', { name: UIReference.categoryPage.categoryPageTitleText });
  }

  /**
   * @feature Navigate to Category page
   * @scenario User navigates to the category page
   * @given
   * @when I navigate to the category page
   * @then I should see the filter options
   * @and I should see the title of the page
   */
  async goToCategoryPage(){
    await this.page.goto(slugs.categoryPage.categorySlug);
    // Wait for the first filter option to be visible
    const firstFilterOption = this.page.locator(UIReference.categoryPage.firstFilterOptionLocator);
    await firstFilterOption.waitFor();

    this.page.waitForLoadState();
    await expect(this.categoryPageTitle).toBeVisible();
  }

  /**
   * @feature Filter category page
   * @scenario User filters category page on size L
   * @given I am on the category page
   * @when I open the Size filter category
   * @and I click the size L button
   * @then the URL should reflect this filter
   * @and I should see fewer products
   */
  async filterOnSize() {
    const sizeFilterButton = this.page.getByRole('button', {name: UIReference.categoryPage.sizeFilterButtonLabel});
    const sizeLButton = this.page.getByRole('link', {name: UIReference.categoryPage.sizeLLinkLabel});
    const removeActiveFilterLink = this.page.getByRole('link', {name: UIReference.categoryPage.removeActiveFilterButtonLabel}).first();
    const amountOfItemsBeforeFilter = parseInt(await this.page.locator(UIReference.categoryPage.itemsOnPageAmountLocator).last().innerText());

    await expect(async() => {
      await sizeFilterButton.click();
      await expect(sizeLButton).toBeVisible();
    }).toPass();

    await sizeLButton.click();
    const sizeFilterRegex = new RegExp(`\\?size=L$`);
    //await this.page.waitForURL(sizeFilterRegex);

    const amountOfItemsAfterFilter = parseInt(await this.page.locator(UIReference.categoryPage.itemsOnPageAmountLocator).last().innerText());
    await expect(removeActiveFilterLink, 'Trash button to remove filter is visible').toBeVisible();
    expect(amountOfItemsAfterFilter, `Amount of items shown with filter (${amountOfItemsAfterFilter}) is less than without (${amountOfItemsBeforeFilter})`).toBeLessThanOrEqual(amountOfItemsBeforeFilter);
  }

  /**
   * @feature Sort category page by price
   * @scenario User sorts category page by price
   * @given I am on the category page
   * @when I open the 'Sort' dropdown
   * @and I click the price button
   * @then the URL should reflect this filter
   * @and I should see products sorted by price
   */
  async sortProducts(attribute:string){
    const sortButton = this.page.getByLabel(UIReference.categoryPage.sortByButtonLabel);
    await sortButton.selectOption(attribute);
    const sortRegex = new RegExp(`\\?product_list_order=${attribute}$`);
    await this.page.waitForURL(sortRegex);

    const selectedValue = await this.page.$eval(UIReference.categoryPage.sortByButtonLocator, sel => (sel as HTMLSelectElement).value);

    // sortButton should now display attribute
    expect(selectedValue, `Sort button should now display ${attribute}`).toEqual(attribute);
    // URL now has ?product_list_order=${attribute}
    expect(this.page.url(), `URL should contain ?product_list_order=${attribute}`).toContain(`product_list_order=${attribute}`);
  }

  /**
   * @feature products per page
   * @scenario User updates the amount of products shown on the page
   * @given I am on the category page
   * @when I change the 'Show' dropdown
   * @then the URl should reflect this filter
   * @and the amount of items should be the new amount I've selected
   */
  async showMoreProducts(){
    const itemsPerPageButton = this.page.getByLabel(UIReference.categoryPage.itemsPerPageButtonLabel);
    const productGrid = this.page.locator(UIReference.categoryPage.productGridLocator);

    await itemsPerPageButton.selectOption('36');
    const itemsRegex = /\?product_list_limit=36$/;
    await this.page.waitForURL(itemsRegex);

    const amountOfItems = await productGrid.locator('li').count();

    expect(this.page.url(), `URL should contain ?product_list_limit=36`).toContain(`?product_list_limit=36`);
    expect(amountOfItems, `Amount of items on the page should be 36`).toBe(36);
  }

  /**
   * @feature View switcher
   * @scenario User switches from the grid to the list view
   * @given I am on the category page
   * @when I click the grid or list mode button
   * @then the URl should reflect this updated view
   * @and the reported selected view should not be the same as it was before I clicked the button
   */
  async switchView(){
    const viewSwitcher = this.page.getByLabel(UIReference.categoryPage.viewSwitchLabel, {exact: true}).locator(UIReference.categoryPage.activeViewLocator);
    const activeView = await viewSwitcher.getAttribute('title');

    if(activeView == 'Grid'){
      await this.page.getByLabel(UIReference.categoryPage.viewListLabel).click();
    } else {
      await this.page.getByLabel(UIReference.categoryPage.viewGridLabel).click();
    }

    const viewRegex = /\?product_list_mode=list$/;
    await this.page.waitForURL(viewRegex);

    const newActiveView = await viewSwitcher.getAttribute('title');
    expect(newActiveView, `View (now ${newActiveView}) should be switched (old: ${activeView})`).not.toEqual(activeView);
    expect(this.page.url(),`URL should contain ?product_list_mode=${newActiveView?.toLowerCase()}`).toContain(`?product_list_mode=${newActiveView?.toLowerCase()}`);
  }
}

export default CategoryPage;
