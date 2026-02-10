// @ts-check

import { expect, type Locator, type Page } from '@playwright/test';
import { UIReference, outcomeMarker, slugs } from '@config';

class ProductPage {
  readonly page: Page;
  simpleProductTitle: Locator | undefined;
  configurableProductTitle: Locator | undefined;
  addToCartButton: Locator;
  addToCompareButton: Locator;
  addToWishlistButton: Locator;

  constructor(page: Page) {
    this.page = page;
    this.addToCartButton = page.getByRole('button', { name: UIReference.productPage.addToCartButtonLocator, exact:true });
    this.addToCompareButton = page.getByLabel(UIReference.productPage.addToCompareButtonLabel, { exact: true });
    this.addToWishlistButton = page.getByLabel(UIReference.productPage.addToWishlistButtonLabel, { exact: true });
  }

  // ==============================================
  // Productpage-related methods
  // ==============================================

  async addProductToCompare(product:string, url: string){
    let productAddedNotification = `${outcomeMarker.productPage.simpleProductAddedNotification} product`;
    const successMessage = this.page.locator(UIReference.general.successMessageLocator);

    await this.page.goto(url);

    await this.addToCompareButton.click();
    await successMessage.waitFor();
    await expect(this.page.getByText(productAddedNotification)).toBeVisible();

    await this.page.goto(slugs.productPage.productComparisonSlug);

    // Assertion: a cell with the product name inside a cell with the product name should be visible
    await expect(this.page.getByRole('cell', {name: product}).getByText(product, {exact: true})).toBeVisible();
  }

  async addProductToWishlist(product:string, url: string){
    /**
     * Note that the test Add_product_to_wishlist is currently set to fixme
     */
    let addedToWishlistNotification = `${product} ${outcomeMarker.wishListPage.wishListAddedNotification}`;
    await this.page.goto(url);
    await this.addToWishlistButton.waitFor();
    this.addToWishlistButton.click();

    await expect(async () => {
      await this.page.waitForSelector(UIReference.general.messageLocator, { state: 'visible' });
    }).toPass();

    await expect(this.page.getByText(addedToWishlistNotification), "Notification that product has been added is visible").toBeVisible();

    await expect(async () => {
      await expect(this.page.getByText(addedToWishlistNotification)).toBeVisible();
    }).toPass();

    let productNameInWishlist = this.page.locator(UIReference.wishListPage.wishListItemGridLabel).getByText(UIReference.productPage.simpleProductTitle, {exact: true});

    await expect(this.page).toHaveURL(new RegExp(slugs.wishList.wishListRegex));
    await expect(this.page.getByText(addedToWishlistNotification)).toBeVisible();
    await expect(productNameInWishlist).toContainText(product);
  }

  async leaveProductReview(product:string, url: string){

    await this.page.goto(url);

    //TODO: Uncomment this and fix test once website is fixed
    /*
      await page.locator('#Rating_5_label path').click();
      await page.getByPlaceholder('Nickname*').click();
      await page.getByPlaceholder('Nickname*').fill('John');
      await page.getByPlaceholder('Nickname*').press('Tab');
      await page.getByPlaceholder('Summary*').click();
      await page.getByPlaceholder('Summary*').fill('A short paragraph');
      await page.getByPlaceholder('Review*').click();
      await page.getByPlaceholder('Review*').fill('Review message!');
      await page.getByRole('button', { name: 'Submit Review' }).click();
      await page.getByRole('img', { name: 'loader' }).click();
    */
  }

  async openLightboxAndScrollThrough(url: string){

    await this.page.goto(url);
    let fullScreenOpener = this.page.getByLabel(UIReference.productPage.fullScreenOpenLabel);
    let fullScreenCloser = this.page.getByLabel(UIReference.productPage.fullScreenCloseLabel);
    let thumbnails = this.page.getByRole('button', {name: UIReference.productPage.thumbnailImageLabel});

    await fullScreenOpener.click();
    await expect(fullScreenCloser).toBeVisible();

    for (const img of await thumbnails.all()) {
      await img.click();
      // wait for transition animation
      await this.page.waitForTimeout(500);
      await expect(img, `CSS class 'border-primary' appended to button`).toHaveClass(new RegExp(outcomeMarker.productPage.borderClassRegex));
    }

    await fullScreenCloser.click();
    await expect(fullScreenCloser).toBeHidden();

  }

  async changeReviewCountAndVerify(url: string) {

    await this.page.goto(url);

    // Get the default review count from URL or UI
    const initialUrl = this.page.url();

    // Find and click the review count selector
    const reviewCountSelector = this.page.getByLabel(UIReference.productPage.reviewCountLabel);
    await expect(reviewCountSelector).toBeVisible();

    // Select 20 reviews per page
    await reviewCountSelector.selectOption('20');
    await this.page.waitForURL(/.*limit=20.*/);

    // Verify URL contains the new limit
    const urlAfterFirstChange = this.page.url();
    expect(urlAfterFirstChange, 'URL should contain limit=20 parameter').toContain('limit=20');
    expect(urlAfterFirstChange, 'URL should have changed after selecting 20 items per page').not.toEqual(initialUrl);

    // Select 50 reviews per page
    await reviewCountSelector.selectOption('50');
    await this.page.waitForURL(/.*limit=50.*/);

    // Verify URL contains the new limit
    const urlAfterSecondChange = this.page.url();
    expect(urlAfterSecondChange, 'URL should contain limit=50 parameter').toContain('limit=50');
    expect(urlAfterSecondChange, 'URL should have changed after selecting 50 items per page').not.toEqual(urlAfterFirstChange);
  }

  // ==============================================
  // Cart-related methods
  // ==============================================

  async addSimpleProductToCart(product: string, url: string, quantity?: string) {

    await this.page.goto(url);

    this.simpleProductTitle = this.page.getByLabel('Product Info').getByText(product, {exact:true});
    expect(await this.simpleProductTitle.innerText(), `Product title "${product}" is visible`).toEqual(product);

    if(quantity){
      // set quantity
      await this.page.getByRole('spinbutton', {name: UIReference.productPage.quantityFieldLabel}).fill('2');
    }

    // assert visibility to ensure we can click the add to cart button.
    await expect(this.addToCartButton).toBeVisible();
    await this.addToCartButton.click();
    // await expect(this.page.locator(UIReference.general.messageLocator).filter(
    //   {hasText: `${outcomeMarker.productPage.simpleProductAddedNotification} ${product}`}),
    //   `Product has been added to cart`
    // ).toBeVisible();
    await expect(this.page.locator(UIReference.general.successMessageLocator).filter(
        {hasText: `${outcomeMarker.productPage.simpleProductAddedNotification} ${product}`}),
      `Product has been added to cart`
    ).toBeVisible();
  }

  async addConfigurableProductToCart(product: string, url:string, quantity?:string) {

    await this.page.goto(url);

    this.configurableProductTitle = this.page.getByLabel('Product Info').getByText(product, {exact:true});
    let productAddedNotification = `${outcomeMarker.productPage.simpleProductAddedNotification} ${product}`;
    const productOptions = this.page.locator(UIReference.productPage.configurableProductOptionForm);

    // wait for the color and size selectors are actually visible
    await productOptions.getByRole('radiogroup').first().waitFor();
    await productOptions.getByRole('radiogroup').last().waitFor();

    // loop through each radiogroup (product option) within the form
    for (const option of await productOptions.getByRole('radiogroup').all()) {
      await option.locator(UIReference.productPage.configurableProductOptionValue).first().check();
    }

    if(quantity){
      // set quantity
      await this.page.getByLabel(UIReference.productPage.quantityFieldLabel).fill('2');
    }

    await this.addToCartButton.click();
    let successMessage = this.page.locator(UIReference.general.successMessageLocator);
    await successMessage.waitFor();
    await expect(this.page.getByText(productAddedNotification)).toBeVisible();
  }
}

export default ProductPage;
