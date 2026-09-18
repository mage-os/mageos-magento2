const fs = require('fs');
const path = require('path');

class Build {

  pathToBaseDir = '../../../'; // default: when installed via npm
  tempDirTests = 'base-tests';
  exampleFileName = '.example';

  constructor() {
    const isLocalDev = fs.existsSync(path.resolve(__dirname, '.git'));

    if (isLocalDev) {
      this.pathToBaseDir = './'; // we're in the root of the dev repo
    }

    this.copyExampleFiles();
    this.copyTestsToTempFolder();
    this.createNewTestsFolderForCustomTests();
  }

  /**
   * @feature Copy config example files
   * @scenario Copy all `.example` files from the current directory to the root directory.
   * @given I have `.example` files in this directory
   * @when I run the Build script
   * @then The `.example` files should be copied to the root directory without the `.example` extension
   *  @and Existing destination files should NOT be overwritten, but skipped
   */
  copyExampleFiles() {
    // const exampleFiles = new Set<string>();
    const exampleFiles = new Set(fs.readdirSync(__dirname).filter(file => file.includes(this.exampleFileName)));

    for (const file of exampleFiles) {
      // destination will be created or overwritten by default.
      const sourceFile = './' + file;
      const destFile = this.pathToBaseDir + file.replace(this.exampleFileName, '');

      try {
        fs.copyFileSync(sourceFile, destFile, fs.constants.COPYFILE_EXCL);
        console.log(`${path.basename(destFile)} was copied to destination`);
      } catch (err) {
        if (err.code === 'EEXIST') {
          console.log(`${path.basename(destFile)} already exists, skipping copy.`);
        } else {
          throw err;
        }
      }
    }
  }

  /**
   * @feature Copy base test files
   * @scenario Prepare test suite by copying `tests/` to the root-level `base-tests/` folder.
   * @given There is a `tests/` folder in the package directory
   * @when I run the Build script
   *  @and A `base-tests/` folder already exists in the root
   * @then The existing `base-tests/` folder should be removed
   *  @and A fresh copy of `tests/` should be placed in `../../../base-tests`
   */
  copyTestsToTempFolder() {

    const sourceDir = path.resolve(__dirname, 'tests');
    const targetDir = path.resolve(__dirname, this.pathToBaseDir, this.tempDirTests);

    try {
      if (fs.existsSync(targetDir)) {
        fs.rmSync(targetDir, {recursive: true, force: true});
      }

      fs.cpSync(sourceDir, targetDir, {recursive: true});
      if (process.env.CI === 'true') {
        fs.rmSync(sourceDir, {recursive: true, force: true});
      }
      console.log(`Copied tests from ${sourceDir} to ${targetDir}`);
    } catch (err) {
      console.error('Error copying test directory:', err);
    }
  }

  /**
   * @feature Create tests directory
   * @scenario Ensure the `tests/` directory exists at the project root level.
   * @given There is no `tests/` directory at the project root
   * @when I run the `createNewTestsFolderForCustomTests` function
   * @then A new `tests/` directory should be created at `../../../tests`
   *  @and A log message "Created tests directory: <path>" should be output
   * @given The `tests/` directory already exists at the project root
   * @when I run the `createNewTestsFolderForCustomTests` function
   * @then No new directory should be created
   *  @and A log message "Tests directory already exists: <path>" should be output
   */
  createNewTestsFolderForCustomTests() {
    const testsDir = path.resolve(__dirname, this.pathToBaseDir, 'tests');
    if (!fs.existsSync(testsDir)) {
      fs.mkdirSync(testsDir);
      console.log(`Created tests directory: ${testsDir}`);
    } else {
      console.log(`Tests directory already exists: ${testsDir}`);
    }
  }
}

new Build();
