// @ts-check

import { expect, Locator, type Page } from '@playwright/test';
import { UIReference } from '@config';

class Footer {
    readonly page: Page
    readonly footerElement: Locator


    constructor(page: Page) {
        this.page = page
        this.footerElement = this.page.locator(UIReference.footerPage.footerLocator);
    }

    async goToFooterElement () {
        await this.page.getByText('Newsletter').scrollIntoViewIfNeeded();
        await expect(
          this.footerElement,
          'Footer is visible'
        ).toBeVisible();
    }
}

export default Footer;
