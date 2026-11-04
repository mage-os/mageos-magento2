// @ts-check

import { request, expect, APIRequestContext, APIResponse } from '@playwright/test';
import { requireEnv } from '@utils/env.utils';

class ApiClient {
  private context!: APIRequestContext;
  private token: string | undefined;
  private tokenExpiry: number | undefined;

  constructor() {}

  /**
   * Initializes the ApiClient by ensuring a valid token and setting up the request context.
   * @returns {Promise<ApiClient>} A Promise that resolves to an instance of ApiClient.
   */
  async create(): Promise<ApiClient> {
    await this.ensureToken();

    this.context = await request.newContext({
      baseURL: requireEnv('PLAYWRIGHT_BASE_URL'),
      extraHTTPHeaders: {
        'Content-Type': 'application/json',
        Authorization: `Bearer ${this.token}`,
      },
    });

    return this;
  }

  /**
   * Ensures the API token is valid, refreshing it if expired or absent.
   * @private
   * @returns {Promise<void>}
   */
  private async ensureToken(): Promise<void> {
    if (!this.token || this.isTokenExpired()) {
      this.token = await this.refreshIntegrationToken();
    }
  }

  /**
   * Fetches a new API token from the server and sets the expiry time.
   * @private
   * @returns {Promise<string>} A Promise that resolves to the token string.
   * @throws {Error} If token retrieval fails.
   */
  private async refreshIntegrationToken(): Promise<string> {
    const tempContext = await request.newContext({
      baseURL: requireEnv('PLAYWRIGHT_BASE_URL'),
      extraHTTPHeaders: {
        'Content-Type': 'application/json',
      },
    });

    const response = await tempContext.post('/rest/V1/integration/admin/token', {
      data: {
        username: requireEnv('MAGENTO_API_USERNAME'),
        password: requireEnv('MAGENTO_API_PASSWORD'),
      },
    });

    if (!response.ok()) {
      const errorBody = await response.text();
      await tempContext.dispose();
      throw new Error(`Failed to obtain integration token: ${response.status()} ${errorBody}`);
    }

    const token = await response.json();
    const expiresHeader = response.headers()['expires'];
    if (expiresHeader) {
        this.tokenExpiry = new Date(expiresHeader).getTime();
    } else {
        this.tokenExpiry = Date.now() + (3600 * 1000);
    }

    await tempContext.dispose();
    return token;
  }

  /**
   * Determines if the current token is expired.
   * @private
   * @returns {boolean} True if the token is expired, otherwise false.
   */
  private isTokenExpired(): boolean {
    return !this.tokenExpiry || Date.now() >= this.tokenExpiry;
  }

  /**
   * Performs a GET request to the specified URL.
   * @param {string} url The endpoint URL to send the request to.
   * @returns {Promise<any>} A Promise that resolves to the response JSON.
   */
  async get(url: string): Promise<any> {
    const response = await this.context.get(url);
    return this.handleResponse(response);
  }

  /**
   * Performs a POST request with the given payload to the specified URL.
   * @param {string} url The endpoint URL to send the request to.
   * @param {Record<string, unknown>} payload The data payload to send with the request.
   * @returns {Promise<any>} A Promise that resolves to the response JSON.
   * @throws {Error} If the response indicates failure.
   */
  async post(url: string, payload: Record<string, unknown>): Promise<any> {
    const response = await this.context.post(url, { data: payload });
    return this.handleResponse(response);
  }

  /**
   * Performs a PUT request with the given payload to the specified URL.
   * @param {string} url The endpoint URL to send the request to.
   * @param {Record<string, unknown>} payload The data payload to send with the request.
   * @returns {Promise<any>} A Promise that resolves to the response JSON.
   * @throws {Error} If the response indicates failure.
   */
  async put(url: string, payload: Record<string, unknown>): Promise<any> {
    const response = await this.context.put(url, { data: payload });
    return this.handleResponse(response);
  }

  /**
   * Performs a DELETE request to the specified URL.
   * @param {string} url The endpoint URL to send the request to.
   * @returns {Promise<void>} A Promise indicating successful deletion.
   */
  async delete(url: string): Promise<void> {
    const response = await this.context.delete(url);
    return this.handleResponse(response);
  }

  /**
   * Handles an API response, checking for success and parsing the JSON body.
   * @param {APIResponse} response The response object to handle.
   * @returns {Promise<any>} A Promise that resolves to the response JSON.
   * @throws {Error} If the response is not successful.
   */
  async handleResponse(response: APIResponse): Promise<any> {
    if (!response.ok()) {
      const body = await response.text();
      throw new Error(`API call failed [${response.status()}]: ${body}`);
    }
    return await response.json();
  }

  /**
   * Disposes of the current request context.
   * @returns {Promise<void>}
   */
  async dispose(): Promise<void> {
    await this.context.dispose();
  }
}

export default ApiClient;