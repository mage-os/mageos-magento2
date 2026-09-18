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
     * @param value - the expected notification
     */
    async validate(value: string) {
		const messages = await this.page.locator(UIReference.general.messageLocator).all();
		let iteration = messages.length;

		for (const memo of messages) {
			let reportAnnotation = `Action was successful, but notification text could not be extracted.`;

			// wait for item to be visible
			await memo.waitFor({state: 'visible'});
			let msgContent = await memo.textContent();

			if(msgContent !== null) {
				reportAnnotation = msgContent.trim();

				if (msgContent.includes(value)) {
					// message equals expected message!
					// Push to report...
					this.testInfo.annotations.push({ type: `Validator Note`, description: msgContent });

					// ... then confirm
					expect(msgContent, `Message should be ${value}`).toEqual(expect.stringContaining(value));

				} else {
					if(!--iteration) {
						// the message did not equal our value, and we've reached the last item in list.
						// Push to report...
						this.testInfo.annotations.push({ type: `Validator Note`, description: msgContent });

						// ... then confirm
						expect(msgContent, `Message should be ${value}`).toEqual(expect.stringContaining(value));
					}
				}
			}
		}
    }
}

export default NotificationValidatorUtils;
