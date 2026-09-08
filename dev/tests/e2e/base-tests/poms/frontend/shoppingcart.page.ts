// @ts-check

import { expect, type Locator, type Page } from '@playwright/test';
import { UIReference, outcomeMarker } from '@config';

class CartPage {
  readonly page: Page;
  readonly showDiscountButton: Locator;
  productQuantityInCheckout: string | undefined;
  productPriceInCheckout: string | undefined;

  constructor(page: Page) {
    this.page = page;
    // this.showDiscountButton = this.page.getByRole('button', { name: UIReference.cart.showDiscountFormButtonLabel });
    this.showDiscountButton = this.page.locator('summary').filter({hasText: UIReference.cart.showDiscountFormButtonLabel});
  }

  async changeProductQuantity(amount: string){
    const productRow = this.page.getByRole('listitem').filter({hasText: UIReference.productPage.simpleProductTitle});
    let currentQuantity = await productRow.getByRole('spinbutton', {name: UIReference.cart.cartQuantityLabel}).inputValue();

    if(currentQuantity == amount){
      amount = '3';
    }

    let subTotalBeforeUpdate = await productRow.getByText(UIReference.general.genericPriceSymbol).last().innerText();
    await productRow.getByLabel(UIReference.cart.cartQuantityLabel).fill(amount);
    await this.page.getByRole('button', { name: UIReference.cart.updateShoppingCartButtonLabel }).click();

    await expect(async () => {
      let subTotalAfterUpdate = await productRow.getByText(UIReference.general.genericPriceSymbol).last().innerText();
      await expect(subTotalBeforeUpdate, `Subtotal should change`).not.toEqual(subTotalAfterUpdate);
    }).toPass();

    let updatedQuantity = await productRow.getByLabel(UIReference.cart.cartQuantityLabel).inputValue();
    expect(updatedQuantity, `updated quantity (${updatedQuantity}) should equal amount we've requested (${amount})`).toEqual(amount);
  }

  // ==============================================
  // Product-related methods
  // ==============================================

  async removeProduct(productTitle: string){
    let removeButton = this.page.getByLabel(`${UIReference.general.removeLabel} ${productTitle}`);
    await removeButton.click();
    await this.page.waitForLoadState();
    await expect(removeButton,`Button to remove specified product is not visible in the cart`).toBeHidden();

    // Expect product to no longer be visible in the cart
    await expect (this.page.getByRole('cell', { name: productTitle }), `Product is not visible in cart`).toBeHidden();
  }

  // ==============================================
  // Discount-related methods
  // ==============================================
  async applyDiscountCode(code: string){
    if(await this.page.getByPlaceholder(UIReference.cart.discountInputFieldLabel).isHidden()){
      // discount field is not open.
      await this.showDiscountButton.click();
    }

    let applyDiscoundButton = this.page.getByRole('button', {name: UIReference.cart.applyDiscountButtonLabel, exact:true});
    let discountField = this.page.getByPlaceholder(UIReference.cart.discountInputFieldLabel);
    await discountField.fill(code);
    await applyDiscoundButton.click();
    await this.page.waitForLoadState();

    const notificationBanner = this.page.locator(UIReference.general.successMessageLocator)
      .filter({hasText: outcomeMarker.cart.discountAppliedNotification});
    await notificationBanner.waitFor();

    await expect.soft(this.page.getByText(`${outcomeMarker.cart.discountAppliedNotification} "${code}"`),`Notification that discount code ${code} has been applied`).toBeVisible();
    // WORKAROUND
    // hardcoded '-' symbol because the space between - and $ is not always present.
    await expect(this.page.getByText(`- ${outcomeMarker.cart.priceReducedSymbols}`),`'- $' should be visible on the page`).toBeVisible();
    //Close message to prevent difficulties with other tests.
    await this.page.getByLabel(UIReference.general.closeMessageLabel).click();
  }

  async removeDiscountCode(){
    if(await this.page.getByPlaceholder(UIReference.cart.discountInputFieldLabel).isHidden()){
      // discount field is not open.
      await this.showDiscountButton.click();
    }

    let cancelCouponButton = this.page.getByRole('button', {name: UIReference.cart.cancelCouponButtonLabel});
    await cancelCouponButton.click();
    await this.page.waitForLoadState();

    await expect.soft(this.page.getByText(outcomeMarker.cart.discountRemovedNotification),`Notification should be visible`).toBeVisible();
    await expect(this.page.getByText(`-${outcomeMarker.cart.priceReducedSymbols}`),`'- $' should not be on the page`).toBeHidden();
  }

  async enterWrongCouponCode(code: string){
    if(await this.page.getByPlaceholder(UIReference.cart.discountInputFieldLabel).isHidden()){
      // discount field is not open.
      await this.showDiscountButton.click();
    }

    let applyDiscoundButton = this.page.getByRole('button', {name: UIReference.cart.applyDiscountButtonLabel, exact:true});
    let discountField = this.page.getByPlaceholder(UIReference.cart.discountInputFieldLabel);
    await discountField.fill(code);
    await applyDiscoundButton.click();
    await this.page.waitForLoadState();

    let incorrectNotification = `${outcomeMarker.cart.incorrectCouponCodeNotificationOne} "${code}" ${outcomeMarker.cart.incorrectCouponCodeNotificationTwo}`;

    //Assertions: notification that code was incorrect & discount code field is still editable
    await expect.soft(this.page.getByText(incorrectNotification), `Code should not work`).toBeVisible();
    await expect(discountField).toBeEditable();
  }


  // ==============================================
  // Additional methods
  // ==============================================

  async getCheckoutValues(productName:string, pricePDP:string, amountPDP:string){
    const checkoutCartDetails = this.page.locator(UIReference.checkout.checkoutCartDetailsLocator);
    const openCartDetailsButton = this.page.locator(UIReference.checkout.openCartDetailsButtonLocator);

    if(await checkoutCartDetails.isHidden()) {
      await openCartDetailsButton.click();
    }

    // // Open minicart based on amount of products in cart
    // let cartItemAmount = await this.page.locator(UIReference.miniCart.minicartAmountBubbleLocator).count();
    // if(cartItemAmount == 1) {
    //   await this.page.getByLabel(`${UIReference.checkout.openCartButtonLabel} ${cartItemAmount} ${UIReference.checkout.openCartButtonLabelCont}`).click();
    // } else {
    //   await this.page.getByLabel(`${UIReference.checkout.openCartButtonLabel} ${cartItemAmount} ${UIReference.checkout.openCartButtonLabelContMultiple}`).click();
    // }

    // Get values from checkout page
    let productInCheckout = this.page.locator(UIReference.checkout.cartDetailsLocator).filter({ hasText: productName }).nth(1);
    this.productPriceInCheckout = await productInCheckout.getByText(UIReference.general.genericPriceSymbol).last().innerText();
    this.productPriceInCheckout = this.productPriceInCheckout.trim();
    // let productImage = this.page.locator(UIReference.checkout.cartDetailsLocator)
    // .filter({ has: this.page.getByRole('img', { name: productName })});
    // this.productQuantityInCheckout = await productImage.locator('> span').innerText();
    this.productQuantityInCheckout = await productInCheckout.locator('.product-price').getByText('x').innerText();
    this.productQuantityInCheckout = this.productQuantityInCheckout.substring(0,1);
    return [this.productPriceInCheckout, this.productQuantityInCheckout];
  }

  async calculateProductPricesAndCompare(pricePDP: string, amountPDP:string, priceCheckout:string, amountCheckout:string){
    // perform magic to calculate price * amount and mold it into the correct form again
    pricePDP = pricePDP.replace(UIReference.general.genericPriceSymbol,'');
    let pricePDPInt = Number(pricePDP);
    let quantityPDPInt = parseInt(amountPDP);
    let calculatedPricePDP = `${UIReference.general.genericPriceSymbol}` + (pricePDPInt * quantityPDPInt).toFixed(2);

    expect(amountPDP,`Amount on PDP (${amountPDP}) equals amount in checkout (${amountCheckout})`).toEqual(amountCheckout);
    expect(calculatedPricePDP, `Price * qty on PDP (${calculatedPricePDP}) equals price * qty in checkout (${priceCheckout})`).toEqual(priceCheckout);
  }
}

export default CartPage;
