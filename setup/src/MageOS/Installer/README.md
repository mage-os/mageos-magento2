# MageOS Interactive Installer

Interactive installation wizard for Mage-OS with theme support, service auto-detection, and smart configuration.

## Features

- ✅ Interactive guided installation
- ✅ Auto-detection of services (MySQL, Elasticsearch, Redis, RabbitMQ)
- ✅ Smart defaults based on environment
- ✅ Complete configuration (Database, Admin, Store, Services)
- ✅ Theme installation with Composer integration
- ✅ Retry logic for failed validations
- ✅ URL auto-correction
- ✅ Proper select lists for locale/timezone/currency

## Usage

```bash
bin/magento install
```

Follow the interactive prompts to configure your Mage-OS installation.

## Supported Themes

- **Hyva** (Recommended) - Modern, performance-focused theme
- **Luma** - Legacy Magento theme (already installed)

## Adding Custom Themes

To add support for your own theme:

### 1. Register Your Theme

Edit `Model/Theme/ThemeRegistry.php`:

```php
class ThemeRegistry
{
    public const THEME_HYVA = 'hyva';
    public const THEME_LUMA = 'luma';
    public const THEME_YOURTHEME = 'yourtheme';  // Add your constant

    public function getAvailableThemes(): array
    {
        return [
            self::THEME_HYVA => [...],
            self::THEME_LUMA => [...],

            // Add your theme
            self::THEME_YOURTHEME => [
                'name' => 'Your Theme',
                'description' => 'Amazing custom theme',
                'package' => 'vendor/your-theme-package',  // Composer package name
                'requires_auth' => false,  // true if requires private repo
                'is_already_installed' => false,
                'is_recommended' => false,  // true to make it default
                'sort_order' => 3  // Display order
            ]
        ];
    }
}
```

### 2. Implement Theme Installer (Optional)

For complex themes requiring custom setup:

Create `Model/Theme/YourThemeInstaller.php`:

```php
<?php
namespace MageOS\Installer\Model\Theme;

use Symfony\Component\Console\Output\OutputInterface;

class YourThemeInstaller
{
    public function install(
        string $baseDir,
        array $config,
        OutputInterface $output
    ): bool {
        // Your custom installation logic
        $output->writeln('<comment>Installing Your Theme...</comment>');

        // Run composer require
        exec('composer require vendor/your-theme-package', $output, $returnCode);

        // Run post-install commands
        exec('bin/magento setup:upgrade', $output);

        return $returnCode === 0;
    }
}
```

### 3. Wire Up in ThemeInstaller

Edit `Model/Theme/ThemeInstaller.php`:

```php
class ThemeInstaller
{
    public function __construct(
        private readonly ThemeRegistry $themeRegistry,
        private readonly HyvaInstaller $hyvaInstaller,
        private readonly YourThemeInstaller $yourThemeInstaller  // Add
    ) {}

    private function install(...) {
        // Add your theme case
        if ($themeId === ThemeRegistry::THEME_YOURTHEME) {
            return $this->yourThemeInstaller->install($baseDir, $themeConfig, $output);
        }
    }
}
```

### 4. Collect Credentials (If Needed)

If your theme requires authentication:

Edit `Model/Config/ThemeConfig.php`:

```php
private function collect(...) {
    // After Hyva credentials check, add yours
    if ($themeId === ThemeRegistry::THEME_YOURTHEME) {
        return $this->collectYourThemeCredentials(...);
    }
}

private function collectYourThemeCredentials(...) {
    // Collect API keys, license keys, etc.
    $apiKeyQuestion = new Question('? Your Theme API Key: ');
    $apiKey = $questionHelper->ask($input, $output, $apiKeyQuestion);

    return [
        'install' => true,
        'theme' => $themeId,
        'yourtheme_api_key' => $apiKey
    ];
}
```

### 5. Update ComposerAuthManager (If Needed)

If your theme uses private Composer repository:

Add methods to `Model/Theme/ComposerAuthManager.php`:

```php
public function addYourThemeAuth(string $baseDir, string $apiKey): void
{
    // Add credentials to auth.json
    // Similar to addHyvaAuth()
}

public function addYourThemeRepository(string $baseDir): void
{
    // Add repository to composer.json
    // Similar to addHyvaRepository()
}
```

## Example: Adding Breeze Theme

```php
// ThemeRegistry.php
public const THEME_BREEZE = 'breeze';

self::THEME_BREEZE => [
    'name' => 'Breeze',
    'description' => 'Lightweight frontend theme',
    'package' => 'swissup/breeze',
    'requires_auth' => false,
    'is_already_installed' => false,
    'is_recommended' => false,
    'sort_order' => 3
]

// ThemeInstaller.php
if ($themeId === ThemeRegistry::THEME_BREEZE) {
    return $this->installBreezeTheme($baseDir, $output);
}
```

## Architecture

```
MageOS/Installer/
├── Console/Command/
│   └── InstallCommand.php          # Main orchestrator
├── Model/
│   ├── Config/                     # Interactive collectors
│   │   ├── DatabaseConfig.php
│   │   ├── AdminConfig.php
│   │   ├── StoreConfig.php
│   │   ├── ThemeConfig.php         # Theme selection
│   │   └── ...
│   ├── Detector/                   # Service detection
│   ├── Validator/                  # Input validation
│   ├── Writer/                     # env.php updates
│   └── Theme/                      # Theme system
│       ├── ThemeRegistry.php       # Theme catalog
│       ├── ThemeInstaller.php      # Orchestrator
│       ├── HyvaInstaller.php       # Hyva-specific
│       ├── ComposerAuthManager.php # Auth handling
│       └── [YourInstaller.php]     # Your theme
```

## Contributing

To contribute a theme installer:
1. Fork the repository
2. Add your theme to ThemeRegistry
3. Implement installer if needed
4. Test the installation flow
5. Submit a pull request

Questions? Check IMPLEMENTATION_PLAN.md for full architecture details.
