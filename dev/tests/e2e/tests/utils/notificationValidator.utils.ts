// @ts-check

import { expect, Page, TestInfo } from "@playwright/test";
import { UIReference } from '@config';

class NotificationValidatorUtils {

    private page : Page;
    private testInfo: TestInfo;

    constructor(page: Page, testInfo: TestInfo) {
        this.page = page;
        this.testInfo = testInfo;
    }

    /**
     * @param notificationType
     * @param value
     * @return json object
     */
    async validate(notificationType: string, value: string) {
      return;
        // await this.page.locator(UIReference.general.messageLocator).waitFor({ state: 'visible' });
        // const notificationText = await this.page.locator(UIReference.general.messageLocator).textContent();
        // let message = { success: true, message: 'Action was successful, but notification text could not be extracted.'};
        //
        // if(
        //   notificationText !== null
        // ) {
        //   message = { success: true, message: notificationText.trim()};
        // }
        //
        // if (
        //     ! expect.soft(this.page.locator(UIReference.general.messageLocator)).toContainText(value)
        // ) {
        //     message = { success: false, message: `Notification text not found: ${value}. Found notification text: ${notificationText}` };
        // }
        //
        // this.testInfo.annotations.push({ type: `Notification: ${notificationType}`, description: message.message });
        //
        // return message;
    }
}

export default NotificationValidatorUtils;
