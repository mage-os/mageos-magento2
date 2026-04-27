# MageOS Interactive Installer

A modern, user-friendly interactive installer for Mage-OS/Magento using Laravel Prompts.

## Overview

The MageOS Interactive Installer (`bin/magento install`) provides a guided, step-by-step installation experience that:
- Auto-detects services (MySQL, Redis, OpenSearch, RabbitMQ)
- Validates configuration in real-time
- Saves progress for resume capability
- Applies post-install best practices automatically
- Protects against accidental production overwrites

## Features

### Interactive Installation Flow
- **21 Installation Stages** - Guided configuration from welcome to completion
- **Auto-Detection** - Automatically finds running services
- **Real-Time Validation** - Catches errors before installation begins
- **Resume Capability** - Saves configuration if installation fails
- **Back Navigation** - Review and modify configuration at summary stage

### Production Safety
- Asks for confirmation before backing up `env.php`
- Warns when running on production servers
- Database creation includes permission warnings
- Requires explicit user consent for destructive operations

### Post-Install Configuration
- **Theme Application** - Applies selected theme to store view
- **Indexer Optimization** - Sets indexers to schedule mode
- **2FA Handling** - Environment-aware two-factor authentication
- **Admin Session** - Extended session lifetime in development
- **Cache Management** - Automatic cache flushing

## Usage

```bash
# Start interactive installation
bin/magento install

# If installation fails, resume from saved config
bin/magento install
# (will prompt to resume)
```

## Architecture

### Core Components

```
setup/src/MageOS/Installer/
├── Console/Command/
│   └── InstallCommand.php          # Main entry point
│
├── Model/
│   ├── InstallationContext.php     # Central state container
│   │
│   ├── Stage/                      # Installation stages
│   │   ├── InstallationStageInterface.php
│   │   ├── AbstractStage.php
│   │   ├── StageNavigator.php
│   │   ├── StageResult.php
│   │   └── Stage/                  # 21 concrete stages
│   │
│   ├── VO/                         # Value Objects (immutable config)
│   │   ├── DatabaseConfiguration.php
│   │   ├── AdminConfiguration.php
│   │   └── ...                     # 13 total VOs
│   │
│   ├── Config/                     # Configuration collectors
│   │   ├── DatabaseConfig.php
│   │   ├── AdminConfig.php
│   │   └── ...                     # 13 total collectors
│   │
│   ├── Validator/                  # Input validation
│   │   ├── DatabaseValidator.php
│   │   ├── PasswordValidator.php
│   │   └── ...                     # 5 total validators
│   │
│   ├── Detector/                   # Service auto-detection
│   │   ├── DatabaseDetector.php
│   │   ├── RedisDetector.php
│   │   └── ...                     # 6 total detectors
│   │
│   ├── Command/                    # Process execution & configurers
│   │   ├── ProcessRunner.php
│   │   ├── ThemeConfigurer.php
│   │   ├── IndexerConfigurer.php
│   │   └── ...                     # 8 total executors
│   │
│   └── Writer/                     # File I/O
│       ├── ConfigFileManager.php   # Resume capability
│       └── EnvConfigWriter.php     # env.php modification
│
└── Module.php                      # Laminas module config
```

### Data Flow

```
1. InstallCommand
   ↓
2. Load saved config (if exists)
   ↓
3. StageNavigator executes stages sequentially:
   │
   ├── Config Collectors → VOs → InstallationContext
   ├── Summary & Confirmation
   ├── Permission Check
   ├── Theme Installation (Composer)
   ├── Magento Installation (setup:install)
   ├── Service Configuration (Redis, RabbitMQ via env.php)
   ├── Sample Data (optional)
   └── Post-Install Configuration
       ├── Theme Application
       ├── Indexer Mode
       ├── 2FA Handling
       └── Cron & Email
   ↓
4. Completion & Cleanup
```

## Extending the Installer

### Adding a New Configuration Stage

**1. Create a Configuration Collector**

```php
// setup/src/MageOS/Installer/Model/Config/MyFeatureConfig.php

namespace MageOS\Installer\Model\Config;

use function Laravel\Prompts\text;
use function Laravel\Prompts\confirm;

class MyFeatureConfig
{
    public function collect(): array
    {
        $enabled = confirm(
            label: 'Enable My Feature?',
            default: false
        );

        if (!$enabled) {
            return ['enabled' => false];
        }

        $apiKey = text(
            label: 'API Key',
            placeholder: 'Enter your API key',
            validate: fn($value) => empty($value) ? 'API key is required' : null
        );

        return [
            'enabled' => true,
            'api_key' => $apiKey
        ];
    }
}
```

**2. Create a Value Object**

```php
// setup/src/MageOS/Installer/Model/VO/MyFeatureConfiguration.php

namespace MageOS\Installer\Model\VO;

use MageOS\Installer\Model\VO\Attribute\Sensitive;

final readonly class MyFeatureConfiguration
{
    public function __construct(
        public bool $enabled,
        #[Sensitive]
        public string $apiKey = ''
    ) {
    }

    public function toArray(bool $includeSensitive = false): array
    {
        $data = ['enabled' => $this->enabled];

        if ($includeSensitive) {
            $data['api_key'] = $this->apiKey;
        }

        return $data;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['enabled'] ?? false,
            $data['api_key'] ?? ''
        );
    }
}
```

**3. Create a Configuration Stage**

```php
// setup/src/MageOS/Installer/Model/Stage/MyFeatureConfigStage.php

namespace MageOS\Installer\Model\Stage;

use MageOS\Installer\Model\Config\MyFeatureConfig;
use MageOS\Installer\Model\InstallationContext;
use MageOS\Installer\Model\VO\MyFeatureConfiguration;
use Symfony\Component\Console\Output\OutputInterface;

class MyFeatureConfigStage extends AbstractStage
{
    public function __construct(
        private readonly MyFeatureConfig $myFeatureConfig
    ) {
    }

    public function getName(): string
    {
        return 'My Feature Configuration';
    }

    public function getDescription(): string
    {
        return 'Configure My Feature integration';
    }

    public function getProgressWeight(): int
    {
        return 1; // Weight for progress calculation
    }

    public function execute(InstallationContext $context, OutputInterface $output): StageResult
    {
        // Collect configuration
        $configArray = $this->myFeatureConfig->collect();

        // Convert to VO and store in context
        $config = MyFeatureConfiguration::fromArray($configArray);
        $context->setMyFeature($config);

        return StageResult::continue();
    }
}
```

**4. Add to InstallationContext**

```php
// In Model/InstallationContext.php

private ?MyFeatureConfiguration $myFeature = null;

public function setMyFeature(MyFeatureConfiguration $config): void
{
    $this->myFeature = $config;
}

public function getMyFeature(): ?MyFeatureConfiguration
{
    return $this->myFeature;
}

// In toArray()
if ($this->myFeature) {
    $data['myFeature'] = $this->myFeature->toArray(false);
}

// In fromArray()
if (isset($data['myFeature'])) {
    $context->setMyFeature(MyFeatureConfiguration::fromArray($data['myFeature']));
}

// In getSensitiveFields()
return [
    'database.password',
    'admin.password',
    'rabbitMQ.password',
    'email.password',
    'myFeature.apiKey', // Add sensitive fields
];

// In getMissingPasswords()
if ($this->myFeature && $this->myFeature->enabled && empty($this->myFeature->apiKey)) {
    $missing[] = 'myFeature.apiKey';
}
```

**5. Register Stage in InstallCommand**

```php
// In Console/Command/InstallCommand.php

private function createStageNavigator(): Model\Stage\StageNavigator
{
    $stages = [
        new Model\Stage\WelcomeStage(),
        // ... existing stages ...
        new Model\Stage\MyFeatureConfigStage($this->myFeatureConfig), // Add here
        new Model\Stage\SummaryStage(),
        // ... rest of stages ...
    ];

    return new Model\Stage\StageNavigator($stages);
}
```

**6. Configure Dependency Injection**

```php
// In setup/config/di.config.php

use MageOS\Installer\Model\Config\MyFeatureConfig;

return [
    'dependencies' => [
        'auto' => [
            'types' => [
                // Add your collector for auto-resolution
                MyFeatureConfig::class => [],
            ],
        ],
    ],
];
```

### Adding a Post-Install Configurer

**1. Create the Configurer**

```php
// setup/src/MageOS/Installer/Model/Command/MyFeatureConfigurer.php

namespace MageOS\Installer\Model\Command;

use MageOS\Installer\Model\VO\MyFeatureConfiguration;
use Symfony\Component\Console\Output\OutputInterface;

class MyFeatureConfigurer
{
    public function __construct(
        private readonly ProcessRunner $processRunner
    ) {
    }

    public function configure(
        MyFeatureConfiguration $config,
        string $baseDir,
        OutputInterface $output
    ): bool {
        if (!$config->enabled) {
            return true;
        }

        $output->writeln('');
        $output->write('<comment>🔧 Configuring My Feature...</comment>');

        // Run Magento command
        $result = $this->processRunner->runMagentoCommand(
            "config:set my/feature/api_key {$config->apiKey}",
            $baseDir,
            timeout: 30
        );

        if ($result->isSuccess()) {
            $output->writeln(' <info>✓</info>');
            $output->writeln('<info>✓ My Feature configured!</info>');
            return true;
        }

        $output->writeln(' <comment>⚠️</comment>');
        $output->writeln('<comment>⚠️  Configuration failed</comment>');
        return false;
    }
}
```

**2. Add to PostInstallConfigStage**

```php
// In Model/Stage/PostInstallConfigStage.php

public function __construct(
    // ... existing dependencies ...
    private readonly MyFeatureConfigurer $myFeatureConfigurer
) {
}

public function execute(InstallationContext $context, OutputInterface $output): StageResult
{
    // ... existing logic ...

    // Configure your feature
    $myFeature = $context->getMyFeature();
    if ($myFeature) {
        $this->myFeatureConfigurer->configure($myFeature, BP, $output);
    }

    return StageResult::continue();
}
```

**3. Configure DI**

```php
// In setup/config/di.config.php

MyFeatureConfigurer::class => [
    'parameters' => [
        'processRunner' => ProcessRunner::class
    ]
],
```

### Adding a Validator

```php
// setup/src/MageOS/Installer/Model/Validator/ApiKeyValidator.php

namespace MageOS\Installer\Model\Validator;

class ApiKeyValidator
{
    /**
     * Validate API key format
     *
     * @param string $apiKey
     * @return array{valid: bool, error: string|null}
     */
    public function validate(string $apiKey): array
    {
        if (empty($apiKey)) {
            return [
                'valid' => false,
                'error' => 'API key cannot be empty'
            ];
        }

        if (strlen($apiKey) < 32) {
            return [
                'valid' => false,
                'error' => 'API key must be at least 32 characters'
            ];
        }

        return [
            'valid' => true,
            'error' => null
        ];
    }
}
```

**Use in collector:**

```php
use function Laravel\Prompts\text;

$apiKey = text(
    label: 'API Key',
    validate: function (string $value) {
        $result = $this->apiKeyValidator->validate($value);
        return $result['valid'] ? null : $result['error'];
    }
);
```

### Adding a Detector

```php
// setup/src/MageOS/Installer/Model/Detector/MyServiceDetector.php

namespace MageOS\Installer\Model\Detector;

class MyServiceDetector
{
    /**
     * Detect if My Service is running
     *
     * @return array{host: string, port: int}|null
     */
    public function detect(): ?array
    {
        $commonHosts = [
            ['host' => 'localhost', 'port' => 8080],
            ['host' => 'myservice', 'port' => 8080],
        ];

        foreach ($commonHosts as $config) {
            if ($this->isPortOpen($config['host'], $config['port'])) {
                return $config;
            }
        }

        return null;
    }

    private function isPortOpen(string $host, int $port, int $timeout = 2): bool
    {
        $connection = @fsockopen($host, $port, $errno, $errstr, $timeout);

        if ($connection) {
            fclose($connection);
            return true;
        }

        return false;
    }
}
```

**Use in collector:**

```php
use function Laravel\Prompts\spin;
use function Laravel\Prompts\info;

$detected = spin(
    message: 'Detecting My Service...',
    callback: fn () => $this->myServiceDetector->detect()
);

if ($detected) {
    info(sprintf('✓ Detected My Service on %s:%d', $detected['host'], $detected['port']));
}
```

## Writing Tests

### Testing Value Objects

All VOs should extend `AbstractVOTest`:

```php
// setup/tests/unit/MageOS/Installer/Model/VO/MyFeatureConfigurationTest.php

namespace MageOS\Installer\Test\Unit\MageOS\Installer\Model\VO;

use MageOS\Installer\Model\VO\MyFeatureConfiguration;
use MageOS\Installer\Test\TestCase\AbstractVOTest;

final class MyFeatureConfigurationTest extends AbstractVOTest
{
    protected function createValidInstance(): MyFeatureConfiguration
    {
        return new MyFeatureConfiguration(
            enabled: true,
            apiKey: 'test-api-key-12345678901234567890'
        );
    }

    protected function getSensitiveFields(): array
    {
        return ['apiKey'];
    }

    // AbstractVOTest provides 6 common tests automatically
    // Add any VO-specific tests here
}
```

### Testing Configurers

```php
// setup/tests/unit/MageOS/Installer/Model/Command/MyFeatureConfigurerTest.php

namespace MageOS\Installer\Test\Unit\MageOS\Installer\Model\Command;

use MageOS\Installer\Model\Command\MyFeatureConfigurer;
use MageOS\Installer\Model\Command\ProcessResult;
use MageOS\Installer\Model\Command\ProcessRunner;
use MageOS\Installer\Model\VO\MyFeatureConfiguration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;

final class MyFeatureConfigurerTest extends TestCase
{
    private ProcessRunner $processRunnerMock;
    private MyFeatureConfigurer $configurer;
    private BufferedOutput $output;

    protected function setUp(): void
    {
        parent::setUp();
        $this->processRunnerMock = $this->createMock(ProcessRunner::class);
        $this->configurer = new MyFeatureConfigurer($this->processRunnerMock);
        $this->output = new BufferedOutput();
    }

    public function test_configure_returns_true_when_disabled(): void
    {
        $config = new MyFeatureConfiguration(enabled: false);

        $result = $this->configurer->configure($config, '/var/www', $this->output);

        $this->assertTrue($result);
    }

    public function test_configure_calls_correct_command(): void
    {
        $config = new MyFeatureConfiguration(enabled: true, apiKey: 'test-key');
        $successResult = new ProcessResult(true, '');

        $this->processRunnerMock->expects($this->once())
            ->method('runMagentoCommand')
            ->with(
                $this->stringContains('config:set'),
                $this->anything(),
                $this->anything()
            )
            ->willReturn($successResult);

        $this->configurer->configure($config, '/var/www', $this->output);
    }
}
```

### Testing File I/O

Use `FileSystemTestCase` for tests involving files:

```php
// setup/tests/unit/MageOS/Installer/Model/Writer/MyWriterTest.php

namespace MageOS\Installer\Test\Unit\MageOS\Installer\Model\Writer;

use MageOS\Installer\Test\TestCase\FileSystemTestCase;

final class MyWriterTest extends FileSystemTestCase
{
    public function test_it_writes_file(): void
    {
        $writer = new MyWriter();
        $path = $this->getVirtualFilePath('test.json');

        $writer->write($path, ['key' => 'value']);

        $this->assertVirtualFileExists('test.json');
        $content = $this->getVirtualFileContent('test.json');
        $this->assertStringContainsString('key', $content);
    }
}
```

### Running Tests

```bash
# Run all tests
cd setup
../vendor/bin/phpunit

# Run specific test file
../vendor/bin/phpunit tests/unit/MageOS/Installer/Model/VO/MyFeatureConfigurationTest.php

# Run with coverage report
../vendor/bin/phpunit --coverage-html coverage/
open coverage/index.html

# Run specific test suite
../vendor/bin/phpunit tests/unit/MageOS/Installer/Model/Command/
```

## Stage Types and When to Use Them

### Configuration Stages
**When:** Collecting user input for a feature
**Characteristics:**
- Uses Laravel Prompts for interactive input
- Converts to Value Object
- Stores in InstallationContext
- Can go back (set `canGoBack() = true`)
- Low progress weight (1 point)

**Examples:** DatabaseConfigStage, AdminConfigStage

### Installation Stages
**When:** Performing irreversible operations
**Characteristics:**
- Cannot go back (`canGoBack() = false`)
- High progress weight (5-10 points)
- Shows warnings before executing
- May run external commands

**Examples:** MagentoInstallationStage, ThemeInstallationStage

### Information Stages
**When:** Displaying information without user input
**Characteristics:**
- Zero progress weight
- Quick execution
- Can go back

**Examples:** WelcomeStage, DocumentRootInfoStage

### Post-Install Stages
**When:** Configuring Magento after installation
**Characteristics:**
- Cannot go back (Magento already installed)
- May collect additional config
- Executes configurers
- Low-medium progress weight

**Examples:** PostInstallConfigStage, ServiceConfigurationStage

## Best Practices

### Value Objects
- ✅ Use `readonly` properties
- ✅ Implement `toArray(bool $includeSensitive)` for serialization
- ✅ Implement `static fromArray(array $data)` for deserialization
- ✅ Mark sensitive fields with `#[Sensitive]` attribute
- ✅ Provide sensible defaults in `fromArray()`

### Configurers
- ✅ Extend `ProcessRunner` for safe command execution
- ✅ Return `bool` for success/failure
- ✅ Show user-friendly messages
- ✅ Provide manual fallback instructions on failure
- ✅ Use appropriate timeouts (30s for config, 120s+ for compilation)

### Validators
- ✅ Return structured arrays: `['valid' => bool, 'error' => ?string]`
- ✅ Provide helpful error messages
- ✅ Sanitize input to prevent injection
- ✅ Be permissive where reasonable (accept various formats)

### Stages
- ✅ Extend `AbstractStage`
- ✅ Set appropriate progress weight
- ✅ Return `StageResult` (continue/back/retry/abort)
- ✅ Handle edge cases gracefully
- ✅ Set `canGoBack()` based on reversibility

### Testing
- ✅ Write tests for all new classes
- ✅ Use `AbstractVOTest` for Value Objects
- ✅ Use `FileSystemTestCase` for file I/O
- ✅ Mock external dependencies (`ProcessRunner`, database connections)
- ✅ Use data providers for comprehensive coverage
- ✅ Keep tests fast (<5s total suite)
- ✅ Test classes must be `final`
- ✅ Test methods use `snake_case`

## Dependencies

### Production
- **laravel/prompts** ^0.3.8 - Interactive CLI prompts
- **symfony/process** ^6.4 - Safe process execution (already in Magento)
- **laminas/laminas-servicemanager** ^3.16 - DI container (already in Magento)

### Development
- **phpunit/phpunit** ^10.5 - Testing framework (already in Magento)

**No additional external dependencies required!**

## File Structure

```
setup/
├── config/
│   ├── application.config.php    # Laminas application config
│   ├── di.config.php             # Dependency injection
│   └── modules.config.php        # Module registration
│
├── src/
│   ├── Magento/Setup/            # Existing Magento setup
│   └── MageOS/Installer/         # Interactive installer
│       ├── Console/Command/
│       ├── Model/
│       │   ├── Checker/
│       │   ├── Command/
│       │   ├── Config/
│       │   ├── Detector/
│       │   ├── Stage/
│       │   ├── Validator/
│       │   ├── VO/
│       │   ├── Writer/
│       │   └── InstallationContext.php
│       └── Module.php
│
├── tests/
│   ├── bootstrap.php             # PHPUnit bootstrap
│   ├── TestCase/                 # Abstract base tests
│   │   ├── AbstractVOTest.php
│   │   └── FileSystemTestCase.php
│   ├── Util/                     # Test utilities
│   │   └── TestDataBuilder.php
│   └── unit/                     # Unit tests (mirrors src/)
│       └── MageOS/Installer/
│
├── phpunit.xml                   # PHPUnit configuration
└── README.md                     # This file
```

## Configuration Flow

1. **User Input** → Config Collector (`collect()`)
2. **Array Data** → Value Object (`fromArray()`)
3. **Value Object** → InstallationContext (`setXxx()`)
4. **InstallationContext** → File (`ConfigFileManager.saveContext()`)
5. **File** → InstallationContext (`ConfigFileManager.loadContext()`)
6. **InstallationContext** → Magento (`setup:install` arguments)

## State Management

### Installation States
- **Not Started** - Fresh installation
- **In Progress** - User configuring
- **Saved** - Config saved, ready to resume
- **Installing** - Running setup:install (point of no return)
- **Post-Install** - Configuring services
- **Complete** - Installation finished

### Stage Results
- **CONTINUE** - Proceed to next stage
- **GO_BACK** - Return to previous stage
- **RETRY** - Re-run current stage
- **ABORT** - Cancel installation

## Troubleshooting

### Tests Failing After Changes

```bash
# Regenerate autoloader
composer dump-autoload

# Run tests with verbose output
cd setup
../vendor/bin/phpunit --testdox
```

### Adding New Stage Not Showing

1. Check DI configuration in `setup/config/di.config.php`
2. Verify stage is added to `InstallCommand::createStageNavigator()`
3. Check constructor has all dependencies
4. Run `composer dump-autoload`

### Configuration Not Persisting

1. Verify VO has `toArray()` and `fromArray()` methods
2. Check InstallationContext includes your VO in serialization
3. Verify sensitive fields are marked with `#[Sensitive]`
4. Test with `ConfigFileManagerTest` pattern

## Contributing

When adding new features:

1. **Follow existing patterns** - Look at similar classes for reference
2. **Write tests first** - TDD approach ensures quality
3. **Use type hints** - All parameters and return types must be declared
4. **Document code** - Add docblocks for all public methods
5. **Keep it simple** - One responsibility per class
6. **Test thoroughly** - Aim for 85%+ coverage on new code

## License

Copyright © Mage-OS. All rights reserved.
Licensed under Open Software License (OSL 3.0) and Academic Free License (AFL 3.0)

## Support

For issues and questions:
- [Mage-OS GitHub](https://github.com/mage-os/mageos-magento2)
- [Mage-OS Community](https://mage-os.org/)
