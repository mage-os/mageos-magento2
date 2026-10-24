// @ts-check

import { expect, type Page } from '@playwright/test';
import { slugs } from '@config';

class OrderHistoryPage {
  readonly page: Page;

  constructor(page: Page) {
    this.page = page;
  }

  async open() {
    await this.page.goto(slugs.account.orderHistorySlug);
    await this.page.waitForLoadState();
  }

  async verifyOrderPresent(orderNumber: string) {
    await expect(this.page.getByText(orderNumber)).toBeVisible();
  }
}

export default OrderHistoryPage;
