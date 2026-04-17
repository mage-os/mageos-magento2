// @ts-check

import { expect, type Locator, type Page } from '@playwright/test';
import { UIReference } from '@config';

class SearchPage {
  readonly page: Page;
  readonly searchToggle: Locator;
  readonly searchInput: Locator;
  readonly suggestionBox: Locator;

  constructor(page: Page) {
    this.page = page;
    this.searchToggle = page.locator(UIReference.search.searchToggleLocator);
    this.searchInput = page.locator(UIReference.search.searchInputLocator);
    this.suggestionBox = page.locator(UIReference.search.suggestionBoxLocator);
  }

  async openSearch() {
    await this.searchToggle.waitFor({ state: 'visible' });
    await this.searchToggle.click();
    await expect(this.searchInput).toBeVisible();
  }

  async search(query: string) {
    await this.openSearch();
    await this.searchInput.fill(query);
    await this.searchInput.press('Enter');
    await this.page.waitForLoadState('networkidle');
  }
}

export default SearchPage;
