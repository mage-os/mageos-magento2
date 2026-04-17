#!/usr/bin/env node

const fs = require('fs');
const path = require('path');
const readline = require('readline');
const { execSync } = require('child_process');

class Install {

  rl = '';
  currentUser = '';
  isCi = false;
  useDefaults = false;
  pathToMagentoRootGitignore = '../../../../../../../'; // default: when installed via npm
  envVars = {};

  rulesToAddToIgnore = [
    '# playwright',
    '/app/design/frontend/<vendor>/<theme>/web/playwright/*',
    '!/app/design/frontend/<vendor>/<theme>/web/playwright/tests/',
    '!/app/design/frontend/<vendor>/<theme>/web/playwright/package.json',
    '!/app/design/frontend/<vendor>/<theme>/web/playwright/package-lock.json'
  ]

  constructor() {
    this.useDefaults = true
    this.isCi = process.env.CI === 'true';
    this.currentUser = execSync('whoami').toString().trim();
    const isLocalDev = fs.existsSync(path.resolve(__dirname, '.git'));

    if (isLocalDev) {
      this.pathToMagentoRootGitignore = './'; // we're in the root of the dev repo
    }

    this.envVars = {
      'PLAYWRIGHT_BASE_URL': { default: 'https://hyva-demo.elgentos.io/' },
      'PLAYWRIGHT_PRODUCTION_URL': { default: 'https://hyva-demo.elgentos.io/' },
      'PLAYWRIGHT_REVIEW_URL': { default: 'https://hyva-demo.elgentos.io/' },
      'MAGENTO_ADMIN_SLUG': { default: 'admin' },
      'MAGENTO_ADMIN_USERNAME': { default: this.currentUser },
      'MAGENTO_ADMIN_PASSWORD': { default: 'Test1234!' },
      'MAGENTO_THEME_LOCALE': { default: 'nl_NL' },
      'MAGENTO_NEW_ACCOUNT_PASSWORD': { default: 'NewTest1234!' },
      'MAGENTO_EXISTING_ACCOUNT_EMAIL_CHROMIUM': { default: 'user-CHROMIUM@elgentos.nl' },
      'MAGENTO_EXISTING_ACCOUNT_EMAIL_FIREFOX': { default: 'user-FIREFOX@elgentos.nl' },
      'MAGENTO_EXISTING_ACCOUNT_EMAIL_WEBKIT': { default: 'user-WEBKIT@elgentos.nl' },
      'MAGENTO_EXISTING_ACCOUNT_PASSWORD': { default: 'Test1234!' },
      'MAGENTO_EXISTING_ACCOUNT_CHANGED_PASSWORD': { default: 'AanpassenKan@0212' },
      'MAGENTO_COUPON_CODE_CHROMIUM': { default: 'CHROMIUM321' },
      'MAGENTO_COUPON_CODE_FIREFOX': { default: 'FIREFOX321' },
      'MAGENTO_COUPON_CODE_WEBKIT': { default: 'WEBKIT321' }
    }

    this.rl = readline.createInterface({
      input: process.stdin,
      output: process.stdout
    });

    this.init();
  }

  async init() {
    await this.setEnvVariables();
    await this.appendToGitIgnore();

    console.log('\nInstallation completed successfully!');
    console.log('\nFor more information, please visit:');
    console.log('https://wiki.elgentos.nl/doc/stappenplan-testing-suite-implementeren-voor-klanten-hCGe4hVQvN');

    // Close rl when no questions are asked
    this.rl.close();
  }

  async askQuestion(query) {
    return new Promise((resolve) => this.rl.question(query, resolve))
  }

  async setEnvVariables() {
    // Check if user
    if (!this.isCi) {
      const initialAnswer = await this.askQuestion('Do you want to customize environment variables? (y/N): ');
      this.useDefaults = initialAnswer.trim().toLowerCase() !== 'y';
    }

    // Read and update .env file
    const envPath = path.join('.env');
    let envContent = '';

    for (const [key, value] of Object.entries(this.envVars)) {
      let userInput = '';
      if (!this.isCi && !this.useDefaults) {
        userInput = await this.askQuestion(`Enter ${ key } (default: ${ value.default }): `);
      }
      envContent += `${ key }=${ userInput || value.default }\n`;
    }

    fs.writeFileSync(envPath, envContent);
  }

  async appendToGitIgnore() {
    if (!this.isCi) {
      const initialAnswer = await this.askQuestion('Do you want to add lines to gitignore of your project? (y/N): ');
      if (initialAnswer.trim().toLowerCase() !== 'y') {
        return;
      }
    }

    console.log('Checking .gitignore and adding lines if necessary...');

    const gitignorePath = path.join(this.pathToMagentoRootGitignore, '.gitignore');

    // Read existing content if file exists
    let existingLines = [];
    if (fs.existsSync(gitignorePath)) {
      const content = fs.readFileSync(gitignorePath, 'utf-8');
      existingLines = content.split(/\r?\n/);
    }

    // Get vendor and theme
    const { vendor, theme } = await this.setVendorAndTheme(__dirname);

      // Append missing lines
    let updated = false;
    for (let line of this.rulesToAddToIgnore) {
        // Replace placeholders with actual values
        line = line.replace('<vendor>', vendor).replace('<theme>', theme);

        if (!existingLines.includes(line)) {
        existingLines.push(line);
        updated = true;
      }
    }

    // Write back if updated
    if (updated) {
      fs.writeFileSync(gitignorePath, existingLines.join('\n'), 'utf-8');
      console.log('.gitignore updated.');
    } else {
      console.log('.gitignore already contains all required lines.');
    }
  }

  async setVendorAndTheme() {
      // Ask user for input if path structure is invalid
      const vendor = await this.askQuestion('Enter the vendor name: ');
      const theme = await this.askQuestion('Enter the theme name: ');

      return { vendor, theme };
  }
}

new Install();