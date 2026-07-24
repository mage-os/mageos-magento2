// @ts-check

import { test } from '@playwright/test';

import CategoryPage from '@poms/frontend/category.page';

/**
 * @feature Filter category page
 * @scenario User filters category page on size L
 * @given I navigate to the category page
 * @when I open the Size filter category
 * @and I click the size L button
 * @then the URL should reflect this filter
 * @and I should see fewer products
 */
test('Filter_category_on_size',{ tag: ['@category', '@cold']}, async ({page}) => {
  const categoryPage = new CategoryPage(page);
  await categoryPage.goToCategoryPage();

  await categoryPage.filterOnSize();
});

/**
 * @feature Sort category page by price
 * @scenario User sorts category page by price
 * @given I navigate to the category page
 * @when I open the 'Sort' dropdown
 * @and I click the price button
 * @then the URL should reflect this filter
 * @and I should see products sorted by price
 */
test('Sort_category_by_price',{ tag: ['@category', '@cold']}, async ({page}) => {
  const categoryPage = new CategoryPage(page);
  await categoryPage.goToCategoryPage();

  await categoryPage.sortProducts('price');
});

/**
 * @feature products per page
 * @scenario User updates the amount of products shown on the page
 * @given I navigate to the category page
 * @when I change the 'Show' dropdown
 * @then the URl should reflect this filter
 * @and the amount of items should be the new amount I've selected
 */
test('Change_amount_of_products_shown',{ tag: ['@category', '@cold'],}, async ({page}) => {
  const categoryPage = new CategoryPage(page);
  await categoryPage.goToCategoryPage();

  await categoryPage.showMoreProducts();
});

/**
 * @feature View switcher
 * @scenario User switches from the grid to the list view
 * @given I navigate to the category page
 * @when I click the grid or list mode button
 * @then the URl should reflect this updated view
 * @and the reported selected view should not be the same as it was before I clicked the button
 */
test('Switch_from_grid_to_list_view',{ tag: ['@category', '@cold'],}, async ({page}) => {
  const categoryPage = new CategoryPage(page);
  await categoryPage.goToCategoryPage();
  await categoryPage.switchView();
});
