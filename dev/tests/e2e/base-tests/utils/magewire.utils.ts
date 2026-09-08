// @ts-check

import {expect, Page} from '@playwright/test';

class MagewireUtils {

  protected page: Page;
  private activeRequests: Set<string> = new Set();

  constructor(page: Page) {
    this.page = page;
  }

  /**
   * Sets up request/response monitoring for Magewire traffic.
   * Must be called before Magewire activity starts (e.g. in beforeEach).
   */
  startMonitoring(): void {
    const handleMagewireTraffic = (type: 'add' | 'delete') => (event: { url(): string }) => {
      const url = event.url();
      if (this.isMagewireRequest(url)) {
        this.activeRequests[type](url);
      }
    };

    this.page.on('request', handleMagewireTraffic('add'));
    this.page.on('response', handleMagewireTraffic('delete'));
    this.page.on('requestfailed', handleMagewireTraffic('delete'));
  }

  /**
   * Waits until all Magewire network requests are completed.
   */
  async waitForMagewireRequests(): Promise<void> {
    const settlingTime = 100; // ms to wait after last request seen
    const maxWaitTime = 10000; // total timeout
    const checkInterval = 50; // interval to check active requests

    const start = Date.now();

    while (Date.now() - start <= maxWaitTime) {
      if (this.activeRequests.size === 0) {
        // Wait a little to ensure no new requests are triggered
        await this.page.waitForTimeout(settlingTime);
        if (this.activeRequests.size === 0) {
          await this.waitForMagewireDomIdle();
          return;
        }
      }

      await this.page.waitForTimeout(checkInterval);
    }

    throw new Error('[Magewire] Timeout: Still pending requests after wait');
  }

  // private async waitForMagewireDomIdle(): Promise<void> {
  //   // look for the magewire pop-up
  //   // const element = this.page.locator('.magewire.messenger');
  //   const element = this.page.locator('#magewire-loader-notifications > div');
  //
  //   // LocatorHandler will keep looking for pop-up
  //   await this.page.addLocatorHandler(element, async() => {
  //     // Keep retrying, waiting for element to be hidden.
  //     await expect(async () => {
	// 	  // await expect(element).toBeHidden();
  //       await expect(element).toHaveCount(0);
  //     }).toPass();
  //   }, {noWaitAfter: true})
  // }

  private async waitForMagewireDomIdle(): Promise<void> {
    // 1. Check of de messenger height 0px is
    // await this.page.waitForFunction(() => {
    //   // const element = document.querySelector('#magewire-loader-notifications > div');
      
    //   // magewire element "Saving Shipping Method"
    //   //#magewire-loader-notifications > div > div > div

	  // const element = document.querySelector('.magewire\\.messenger');
    //   return element && getComputedStyle(element).height === '0px';
    // }, { timeout: 30000 });

    // 2. Check if there is no processing ongoing
    await this.page.waitForFunction(() => {
      return !(window.magewire && (window.magewire as any).processing);
    }, { timeout: 30000 });

    await this.page.waitForTimeout(500);
  }

  private isMagewireRequest(url: string): boolean {
    return url.includes('/magewire/message');
  }
}

export default MagewireUtils;
