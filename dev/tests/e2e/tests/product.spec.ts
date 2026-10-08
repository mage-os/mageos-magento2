// @ts-check

import { test } from '@playwright/test';
import { UIReference ,slugs } from '@config';

import ProductPage from '@poms/frontend/product.page';
import LoginPage from '@poms/frontend/login.page';
import { requireEnv } from '@utils/env.utils';

test.describe('Product page tests',{ tag: '@product',}, () => {
  test('Add_product_to_compare',{ tag: '@cold'}, async ({page}) => {
    const productPage = new ProductPage(page);
    await productPage.addProductToCompare(UIReference.productPage.simpleProductTitle, slugs.productPage.simpleProductSlug);
  });

  test.fixme('Add_product_to_wishlist',{ tag: '@cold'}, async ({page, browserName}) => {
    /**
     * This test is currently (October 2025) set to be fixed, since it causes regular timeouts.
     * Various fixes have been tried, unsuccessfully.
     */
    await test.step('Log in with account', async () =>{
      const browserEngine = browserName?.toUpperCase() || "UNKNOWN";
      const emailInputValue = requireEnv(`MAGENTO_EXISTING_ACCOUNT_EMAIL_${browserEngine}`);
      const passwordInputValue = requireEnv('MAGENTO_EXISTING_ACCOUNT_PASSWORD');

      const loginPage = new LoginPage(page);
      await loginPage.login(emailInputValue, passwordInputValue);
    });

    await test.step('Add product to wishlist', async () =>{
      const productPage = new ProductPage(page);
      await productPage.addProductToWishlist(UIReference.productPage.simpleProductTitle, slugs.productPage.simpleProductSlug);
    });
  });


  test.fixme('Leave a product review (Test currently fails due to error on website)',{ tag: '@cold'}, async ({}) => {
    // const productPage = new ProductPage(page);
    // await productPage.leaveProductReview(UIReference.productPage.simpleProductTitle, slugs.productPage.simpleProductSlug);
  });

  test('Open_pictures_in_lightbox_and_scroll', async ({page}) => {
    const productPage = new ProductPage(page);
    await productPage.openLightboxAndScrollThrough(slugs.productPage.configurableProductSlug);
  });

  test('Change_number_of_reviews_shown_on_product_page', async ({page}) => {
    const productPage = new ProductPage(page);
    await productPage.changeReviewCountAndVerify(slugs.productPage.simpleProductSlug);
  });
});
