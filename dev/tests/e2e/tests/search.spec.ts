// @ts-check

import { test, expect } from '@playwright/test';
import { UIReference, outcomeMarker, inputValues, slugs } from '@config';

import SearchPage from '@poms/frontend/search.page';

test.describe('Search functionality', () => {
  test('User_can_find_a_specific_product_and_navigate_to_its_page', async ({ page }) => {
    await page.goto('');
    const searchPage = new SearchPage(page);
    await searchPage.search(inputValues.search.querySpecificProduct);
    await expect(page).toHaveURL(slugs.productPage.searchProductSlug);
  });

  test('No_results_message_is_shown_for_unknown_query', async ({ page }) => {
    await page.goto('');
    const searchPage = new SearchPage(page);
    await searchPage.search(inputValues.search.queryNoResults);
    await expect(page.getByText(outcomeMarker.search.noResultsMessage)).toBeVisible();
  });
});
