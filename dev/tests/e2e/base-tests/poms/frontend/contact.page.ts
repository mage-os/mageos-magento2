// @ts-check

import { expect, type Locator, type Page } from '@playwright/test';
import { faker } from '@faker-js/faker';
import { UIReference, outcomeMarker, slugs } from '@config';

class ContactPage {
  readonly page: Page;
  readonly nameField: Locator;
  readonly emailField: Locator;
  readonly messageField: Locator;
  readonly sendFormButton: Locator;

  constructor(page: Page){
    this.page = page;
    this.nameField = this.page.getByLabel(UIReference.credentials.nameFieldLabel);
    this.emailField = this.page.getByPlaceholder(UIReference.credentials.emailFieldLabel, { exact: true });
    this.messageField = this.page.locator(UIReference.contactPage.messageFieldSelector);
    this.sendFormButton = this.page.getByRole('button', { name: UIReference.general.genericSubmitButtonLabel });
  }

  async fillOutForm(){
    await this.page.goto(slugs.contact.contactSlug);
    let messageSentConfirmationText = outcomeMarker.contactPage.messageSentConfirmationText;

    // Add a wait for the form to be visible
    await this.nameField.waitFor({state: 'visible', timeout: 10000});

    await this.nameField.fill(faker.person.firstName());
    await this.emailField.fill(faker.internet.email());
    await this.messageField.fill(faker.lorem.paragraph());

    await this.sendFormButton.click();

    await expect(this.page.getByText(messageSentConfirmationText)).toBeVisible();
    await expect(this.nameField, 'name should be empty now').toBeEmpty();
    await expect(this.emailField, 'email should be empty now').toBeEmpty();
    await expect(this.messageField, 'message should be empty now').toBeEmpty();
  }
}

export default ContactPage;
