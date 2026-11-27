# AGENTS.md

Guidance for AI coding agents working on Mage-OS. Every agent-specific file in this repository (`CLAUDE.md`, `GEMINI.md`, `.aider.conf.yml`) only loads this one.

Scope: this file is about the `mage-os/mageos-magento2` repository. The `mage-os/magento2-base` Composer package also ships a copy, so you may find it under `vendor/` in a store project. It does not apply there: store projects never edit core code.

## What this repository is

- Mage-OS is an independent distribution of Magento Open Source. It must stay compatible with existing Magento themes and extensions.
- This repository is the core itself. Fixes go directly into `app/code/Magento/<Module>`, `lib/internal/Magento/Framework` and `setup/`. Store-project advice such as "never modify core, write a plugin" does not apply.
- It is a monorepo. The release tooling (`mage-os/generate-mirror-repo-js`) publishes every component as its own `mage-os/*` package. In this repository the code keeps the `Magento\` namespace and the `magento/*` package names; only the interactive installer uses `MageOS\`.
- Features that can live outside the core belong in separate modules (see Mage-OS Lab), not here.
- Branches:
  - `main` is development and the target for PRs.
  - `release/N.x` are the release lines; maintainers backport to them.
  - `2.4-develop` is an untouched mirror of upstream Magento, updated daily by CI. Never commit to it.
- Maintainers regularly merge upstream Magento into `main`. Keep diffs to upstream files small and local, because reformatting, reordering or renaming code you don't need to touch causes merge conflicts. `git log origin/2.4-develop..main -- <path>` lists the Mage-OS-only changes to a file.

## Picking up work

- Work comes from GitHub issues and from porting fixes from `magento/magento2`. Check that no open PR already covers it, and discuss larger changes in an issue first.
- Security problems are never reported or fixed in public. Mage-OS-specific ones go to security@mage-os.org, and ones that also affect Magento go to Adobe (see `SECURITY.md`).
- Reproduce a bug before fixing it, and keep the steps: the PR template asks for them.
- Branch names: `fix/<issue>-<slug>`, `feature/<issue>-<slug>`, `chore/<slug>`, `backport/<upstream-pr>-<slug>`.
- PR titles: `Fix #<issue>: <summary>`, or `Backport magento/magento2#<pr>: <summary>`. For a release branch, prefix the title, e.g. `[4.x] <summary>`.

## Setup

- PHP `~8.3 || ~8.4 || ~8.5` with the extensions in `composer.json`. `supported-services.json` lists the PHP and service versions CI tests against.
- Unit tests, static checks and `setup:di:compile` need nothing beyond `composer install`. Integration tests need MySQL/MariaDB and OpenSearch, and they install their own sandbox.
- For a working store, use `bin/magento setup:install` with options. The Mage-OS installer, `bin/magento install`, is interactive.
- No local environment is prescribed; CI uses Warden. Run the commands below from the repository root, inside whatever provides PHP.

## Commands

### Unit tests

```bash
vendor/bin/phpunit -c dev/tests/unit/phpunit.xml.dist --no-extensions app/code/Magento/Sales/Test/Unit/Helper/GuestTest.php
vendor/bin/phpunit -c dev/tests/unit/phpunit.xml.dist --no-extensions --filter testLoadValidOrderNotEmptyPost app/code/Magento/Sales/Test/Unit/Helper/GuestTest.php
vendor/bin/phpunit -c dev/tests/unit/phpunit.xml.dist --no-extensions app/code/Magento/Sales/Test/Unit   # a whole module, as CI runs it
vendor/bin/phpunit -c dev/tests/unit/phpunit.xml.dist --no-extensions lib/internal/Magento/Framework/Module/Test/Unit
vendor/bin/phpunit -c setup/phpunit.xml --do-not-cache-result                                            # Mage-OS installer
```

- Always pass `--no-extensions`. Without it, the Allure extension in the `.dist` config fails to load from the repository root and PHPUnit exits 1 even when every test passes.
- Run the suites of the modules you touched, not the whole unit suite; CI does the same. PHPUnit notices, e.g. about mocks without expectations, don't fail a run.
- CI doesn't run the installer suite, and it already has failures on `main`. Before assuming your change broke something, compare with `main`.

### Integration tests

```bash
cp dev/tests/integration/etc/install-config-mysql.php.dist dev/tests/integration/etc/install-config-mysql.php   # then set hosts and credentials
cd dev/tests/integration
../../../vendor/bin/phpunit -c phpunit.xml.dist testsuite/Magento/Sales/Controller/Adminhtml/Order/Creditmemo/GridWithDeletedWebsiteTest.php
../../../vendor/bin/phpunit -c phpunit.xml.dist --testsuite 'Magento Integration Tests Real Suite' --filter='/Magento/Sales/|Magento\\Sales'   # one module, as CI runs it
```

- Run them from `dev/tests/integration` and keep the extensions: the test framework hooks into PHPUnit as one.
- CI uses Warden with `WARDEN_TEST_DB=1`. In that setup the values are `db-host` `tmp-mysql`, `db-user` `root`, `db-password` `magento`, `opensearch-host` `opensearch` and `amqp-host` `rabbitmq`. If no RabbitMQ is running, drop the `amqp-*` keys.
- `TESTS_CLEANUP` is `enabled` in `phpunit.xml.dist`, so every run reinstalls Magento. While iterating, copy the file to `phpunit.xml` (gitignored), set it to `disabled`, and use `-c phpunit.xml`.
- The bootstrap copies the `_files/Magento/TestModule*` modules into `app/code/Magento/`. The copies are gitignored; leave them alone.

### Coding standard and static checks

```bash
vendor/bin/phpcs --standard=Magento2 --severity=5 --warning-severity=8 --error-severity=8 <files>   # the CI gate
```

- CI runs this on the files a PR adds or modifies. Violations that already exist on the base branch are ignored. Warnings fail too; the usual ones are a method without a docblock and a missing `@param`.
- CI installs the latest `magento/magento-coding-standard`, which can be newer than the locked one.

Magento's static suite adds phpmd, PHPStan and more. CI doesn't run it:

```bash
git diff --name-only origin/main...HEAD > dev/tests/static/testsuite/Magento/Test/_files/changed_files_local.txt
vendor/bin/phpunit -c dev/tests/static/phpunit.xml.dist --no-extensions --filter 'testCodeStyle|testCodeMess|testPhpStan' dev/tests/static/testsuite/Magento/Test/Php/LiveCodeTest.php
rm dev/tests/static/testsuite/Magento/Test/_files/changed_files_local.txt
```

Without the list file, `LiveCodeTest` only checks uncommitted and staged changes.

### Other checks

```bash
bin/magento module:enable --all && bin/magento setup:di:compile   # what CI runs on every push; writes app/etc/config.php, env.php, generated/
varnishtest dev/tests/varnish/*.vtc                                 # VCL in app/code/Magento/PageCache/etc
cp package.json.sample package.json && cp Gruntfile.js.sample Gruntfile.js && npm install   # JS tooling; gitignored copies, needs vendor/
npx grunt static --file=<path/to/file.js>                           # ESLint
npx grunt spec:luma --file=<path/to/test.js>                        # Jasmine (backend, blank, luma); needs deployed static content
```

`dev/tests/api-functional`, `dev/tests/setup-integration` and MFTF (`<Module>/Test/Mftf`) also exist. No workflow in this repository runs them.

## CI on pull requests

- **Coding standard:** the phpcs gate above.
- **Unit and integration tests:** for the modules Nx marks as affected by the diff, on the PHP and service versions in `supported-services.json`. The full integration suite only runs on manual dispatch.
- **`setup:di:compile`:** with all modules enabled. A broken DI configuration or constructor anywhere fails it.
- **Sansec eComscan:** scans the whole tree.

## Architecture

- **Layout:**
  - `app/code/Magento/<Module>`: about 220 core modules.
  - `app/design` and `app/i18n`: themes and language packs.
  - `lib/internal/Magento/Framework`: the framework. Its `Amqp`, `Bulk`, `MessageQueue` and `Stomp` directories are separate packages.
- **`setup/`** is a separate Laminas application with the `setup:*` commands (`setup/src/Magento/Setup`) and the installer (`setup/src/MageOS/Installer`). It is wired through `setup/config/*.config.php`, not `di.xml`.
- **Component registration:** nothing is installed into `vendor/`. `app/etc/NonComposerComponentRegistration.php` registers every component by globbing the paths in `app/etc/registration_globlist.php`.
- **Composer bookkeeping**, checked by `Magento\Test\Integrity\ComposerTest`. That test currently also fails on `main`, so compare with `main`.
  - A new module, theme or language pack goes into `replace` in the root `composer.json`.
  - A component's `composer.json` has no `version`, requires `magento/framework`, and repeats the root PHP constraint.
  - Every package a component requires is also in the root `require`, `require-dev` or `replace`.
  - A module that uses another module declares that dependency.
  - After editing the root `composer.json`, run `composer update --lock`.

### Mage-OS-specific mechanisms

- **Lazy-ghost DI (PHP 8.4+, compiled DI only).**
  - **How it works:** `setup:di:compile` records which types may be built lazily (`Magento\Setup\Module\Di\Compiler\Config\Chain\LazyTypes`, `ConstructorSideEffectScanner`). For those types, `Magento\Framework\ObjectManager\Factory\Compiled::create()` returns a `ReflectionClass::newLazyGhost()` when there are no call-time arguments. The constructor then runs only when the object is first used.
  - **Which constructors qualify:** those declared in `Magento\` or `MageOS\` are trusted. Third-party ones qualify only if the scanner proves them free of side effects.
  - **The trap:** without compiled metadata, and always in integration tests, objects are built eagerly. A constructor with side effects can therefore pass every test and still misbehave in production. Mark such classes `#[\Magento\Framework\ObjectManager\Attribute\NonLazy]`; the attribute is inherited through parents, interfaces and traits.
  - **Kill switch:** `'lazy_object_loading_disabled' => true` in `app/etc/env.php`.
- **Installer** (`bin/magento install`, Laravel Prompts).
  - Stages collect input into immutable value objects held by `InstallationContext`. The context is saved, so a failed run can resume.
  - Register new stages in `InstallCommand::createStageNavigator()`, and their collaborators in `setup/config/di.config.php`.
  - `setup/README.md` describes the flow. Its test conventions (`final` classes, snake_case names) are outdated; follow the existing tests.
- **Distribution metadata:** `ProductMetadata` also implements `DistributionMetadataInterface` (the name `Mage-OS` and the distribution version). `getVersion()` keeps returning the Magento version the release is based on.

## Code rules

- **Stay backward compatible.**
  - Don't change or remove public or protected methods, signatures or `@api` interfaces.
  - A new constructor dependency goes last, nullable and optional, with a fallback: `?Foo $foo = null` and `$this->foo = $foo ?? ObjectManager::getInstance()->get(Foo::class);`. Don't use the ObjectManager anywhere else.
- **PHP 8.3 compatibility.** Code must still run on PHP 8.3. Guard newer APIs with `PHP_VERSION_ID`. Don't use 8.4+ syntax such as property hooks, asymmetric visibility, or `new Foo()->bar()` without wrapping parentheses.
- **New Composer dependencies** need agreement in an issue first.
- **New PHP files** declare `strict_types=1` and start with a Mage-OS header, usually `Copyright © Mage-OS. All rights reserved.` followed by `See COPYING.txt for license details.` Keep the `Copyright <year> Adobe` headers of upstream files.
- **`db_schema.xml` changes** need the matching `db_schema_whitelist.json`. Generate it with `bin/magento setup:db-declaration:generate-whitelist --module-name=Magento_<Module>`.
- **`.editorconfig`:** 4 spaces and LF. YAML and JSON use 2 spaces, except `composer.json`, `auth.json` and `db_schema_whitelist.json`.

## Tests

- **PHPUnit 12 uses attributes,** such as `#[DataProvider('provider')]` with a `public static` provider.
  - It ignores docblock annotations like `@dataProvider`, and such tests then fail with `ArgumentCountError`.
  - `addMethods()`, `withConsecutive()` and `getMockForAbstractClass()` no longer exist.
  - Magento's own `@magento…` annotations in integration tests still work.
- **`Magento\Framework\TestFramework\Unit\Helper\MockCreationTrait`** fills the gaps:
  - `createPartialMockWithReflection($class, $methods)` mocks methods a class doesn't declare, such as magic getters and setters.
  - `createInvocationMatcher('once')` turns a string from a static data provider into a matcher.
- **Unit test location:** `<Module>/Test/Unit/…` with the namespace `Magento\<Module>\Test\Unit\…`. Framework unit tests live in `lib/internal/Magento/Framework/<Component>/Test/Unit/…`.
- **Integration tests** go in `dev/tests/integration/testsuite/Magento/<Module>/…` and use the namespace of the class under test.
  - New tests use the attributes in `Magento\TestFramework\Fixture\`: `#[DataFixture]`, `#[AppArea]`, `#[DbIsolation]`, `#[AppIsolation]` and `#[Config]`. Fixture classes go in `<Module>/Test/Fixture`.
  - Legacy `@magentoDataFixture` scripts in `_files/` still work.
- **Abstract base classes:** name them `…TestCase`, not `…Test`. PHPUnit 12 warns about abstract `*Test.php` classes, and the warning fails CI.

## Commits and pull requests

- **Scope:** one focused change per PR, opened against `main` from a fork.
- **Description:** complete every required section of `.github/PULL_REQUEST_TEMPLATE.md`: the description, manual testing steps, and `Fixes mage-os/mageos-magento2#<issue>`.
- **Tests:** bug fixes and new logic come with unit or integration tests.
- **Commit messages:** the subject is a plain imperative sentence (`Fix #<issue>: …` when there is an issue). The body explains the cause and the fix.

## About these instructions

- **Single source:** `AGENTS.md` is the only place for shared agent instructions.
  - Copilot, Codex, Cursor, Junie, Windsurf, Zed and most other agents read it directly.
  - `CLAUDE.md` and `GEMINI.md` only import it, and `.aider.conf.yml` loads it.
- **No tool-specific copies,** such as `.cursorrules`, `.github/copilot-instructions.md`, `WARP.md` or `.junie/AGENTS.md`. Some tools read those instead of `AGENTS.md`.
- **No nested agent files** below `app/`, `lib/`, `setup/` or `dev/`. Those paths ship in Composer packages.
- **Personal instructions** belong in your tool's user-level settings or a gitignored `CLAUDE.local.md` or `AGENTS.local.md`.
