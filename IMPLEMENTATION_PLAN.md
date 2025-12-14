# Mage-OS Interactive Installer Implementation Plan

## Overview
Create a new `bin/magento install` command that provides a modern, user-friendly interactive installation experience for Mage-OS. This will wrap the existing `setup:install` command with better UX, smart defaults, auto-detection, and theme installation support.

## Why This Approach?
The existing `setup:install --interactive` flag exists but provides a bare-bones experience - it just loops through options without grouping, smart defaults, or helpful feedback. We're building something that actually improves DX significantly.

## Architecture Decision
- **Create NEW command**: `bin/magento install` (separate from existing `setup:install`)
- **Wrapper pattern**: Collect config interactively → Call `setup:install` programmatically + configure env.php directly
- **Module structure**: New module `MageOS_Installer` in `app/code/MageOS/Installer/`
- **Reuse existing logic**: Don't reimplement installation, just wrap it with better UX
- **Configuration profiles**: Support preset configurations (Development, Production, Docker)

## Complete Configuration Coverage

The installer will handle ALL critical Magento configuration:

### Core Setup
- ✅ Database (host, name, user, password, prefix)
- ✅ Admin account (name, email, username, password)
- ✅ Store configuration (base URL, admin URI, language, timezone, currency)
- ✅ Document root detection (pub/ or root)
- ✅ URL rewrites configuration

### Infrastructure Services
- ✅ Elasticsearch/OpenSearch (host, port, auth, prefix)
- ✅ Redis - Session storage
- ✅ Redis - Cache backend
- ✅ Redis - Full Page Cache (FPC)
- ✅ RabbitMQ (host, port, user, password, virtualhost)

### Development & Logging
- ✅ Debug mode (enabled/disabled)
- ✅ System logs (file, syslog, database)
- ✅ Log levels (debug, info, notice, warning, error)
- ✅ Sample data installation

### Optional Features
- ✅ Theme installation (Hyva, etc.)
- ✅ Performance optimizations
- ✅ Cron setup

---

## Stage 1: Core Setup + Basic Services (MVP)
**Goal**: Complete basic Magento installation with core configuration + essential services
**Success Criteria**:
- User can run `bin/magento install` and complete installation interactively
- All core parameters collected (DB, Admin, Store, Backend)
- Elasticsearch/OpenSearch configured
- Document root detected automatically
- Installation succeeds and Magento is functional
**Tests**:
- Unit tests for all config collectors
- Manual test: Fresh install on clean environment
- Verify all setup:install parameters are passed correctly
- Test with pub/ as document root and without

### Tasks
1. Create module structure:
   ```
   app/code/MageOS/Installer/
   ├── Console/Command/
   │   └── InstallCommand.php
   ├── Model/
   │   ├── Config/
   │   │   ├── DatabaseConfig.php
   │   │   ├── AdminConfig.php
   │   │   ├── StoreConfig.php
   │   │   ├── SearchEngineConfig.php
   │   │   └── BackendConfig.php
   │   ├── Detector/
   │   │   ├── DatabaseDetector.php
   │   │   ├── UrlDetector.php
   │   │   ├── SearchEngineDetector.php
   │   │   └── DocumentRootDetector.php
   │   ├── Validator/
   │   │   ├── DatabaseValidator.php
   │   │   ├── UrlValidator.php
   │   │   └── EmailValidator.php
   │   └── Installer.php
   ├── etc/
   │   ├── module.xml
   │   └── di.xml
   └── registration.php
   ```

2. Implement InstallCommand with grouped sections:
   - **Welcome & Profile Selection** (optional: Development/Production/Custom)
   - **Database Configuration**: host, name, user, password, prefix
   - **Admin Account**: first name, last name, email, username, password
   - **Store Configuration**: base URL, language, timezone, currency, use rewrites
   - **Backend Configuration**: backend frontname (admin path)
   - **Document Root**: Auto-detect pub/ or root, allow override
   - **Search Engine**: Elasticsearch/OpenSearch host, port, prefix, auth

3. Create config collectors with smart defaults:
   - `DatabaseConfig`: Collect DB params, validate connection
   - `AdminConfig`: Collect admin details, validate email/password strength
   - `StoreConfig`: Collect store settings with locale-based defaults
   - `SearchEngineConfig`: Detect and configure ES/OS
   - `BackendConfig`: Suggest custom backend frontname (not 'admin')

4. Implement document root detection:
   - Check if running from pub/ or root
   - Detect web server configuration
   - Warn about security implications
   - Configure base URL accordingly

5. Implement URL rewrites configuration:
   - Ask if web server supports rewrites
   - Configure use_rewrites in env.php
   - Provide instructions for Apache/Nginx

6. Call setup:install programmatically:
   ```php
   $command = $this->getApplication()->find('setup:install');
   $arguments = [
       '--db-host' => $dbHost,
       '--backend-frontname' => $backendFrontname,
       '--search-engine' => $searchEngine,
       '--elasticsearch-host' => $esHost,
       // ... all collected params
   ];
   $input = new ArrayInput($arguments);
   $returnCode = $command->run($input, $output);
   ```

7. Add progress feedback with sections:
   ```
   🔄 Validating database connection...
   ✓ Database connected successfully

   🔄 Installing database schema...
   ✓ Database schema created

   🔄 Creating admin user...
   ✓ Admin user created

   🔄 Configuring store...
   ✓ Store configured
   ```

**Status**: Complete ✅

---

## Stage 2: Redis, RabbitMQ & Advanced Services
**Goal**: Configure all infrastructure services (Redis, RabbitMQ) and advanced options
**Success Criteria**:
- Redis configured for Sessions, Cache, and FPC
- RabbitMQ configured for async operations
- Debug mode and logging configured
- Sample data option during install
- All services auto-detected with helpful defaults
**Tests**:
- Test Redis configuration for each backend
- Test RabbitMQ connection and queues
- Test with services running and not running
- Test debug mode and logging options
- Test sample data installation

### Tasks
1. Create additional config collectors:
   ```
   ├── Model/
   │   ├── Config/
   │   │   ├── RedisConfig.php         (Sessions, Cache, FPC)
   │   │   ├── RabbitMQConfig.php      (Message queue)
   │   │   ├── LoggingConfig.php       (Debug, logs, levels)
   │   │   └── SampleDataConfig.php
   │   ├── Detector/
   │   │   ├── RedisDetector.php       (Check ports 6379, 6380, 6381)
   │   │   └── RabbitMQDetector.php    (Check port 5672)
   ```

2. Implement Redis configuration:
   - **Session Storage**:
     * Ask: "Use Redis for sessions?"
     * Auto-detect Redis on localhost:6379
     * Configure session save handler in env.php
     * Support multiple Redis instances (db 0, 1, 2)

   - **Cache Backend**:
     * Ask: "Use Redis for cache?"
     * Configure default cache backend
     * Configure page_cache backend
     * Different Redis DB or instance than sessions

   - **Full Page Cache (FPC)**:
     * Ask: "Use Redis for FPC?"
     * Configure separate Redis instance if available
     * Or use different database number

   - Configuration format:
     ```php
     'session' => [
         'save' => 'redis',
         'redis' => [
             'host' => '127.0.0.1',
             'port' => '6379',
             'database' => '0',
         ]
     ],
     'cache' => [
         'frontend' => [
             'default' => [
                 'backend' => 'Cm_Cache_Backend_Redis',
                 'backend_options' => [
                     'server' => '127.0.0.1',
                     'port' => '6379',
                     'database' => '1',
                 ]
             ]
         ]
     ]
     ```

3. Implement RabbitMQ configuration:
   - Auto-detect RabbitMQ on localhost:5672
   - Ask for connection details:
     * Host [localhost]
     * Port [5672]
     * Username [guest]
     * Password [guest]
     * Virtual host [/]
   - Test connection before proceeding
   - Configure in env.php:
     ```php
     'queue' => [
         'amqp' => [
             'host' => 'localhost',
             'port' => '5672',
             'user' => 'guest',
             'password' => 'guest',
             'virtualhost' => '/'
         ]
     ]
     ```

4. Implement debug & logging configuration:
   - **Debug Mode**:
     * Ask: "Enable debug mode?" (Yes for dev, No for prod)
     * Set MAGE_MODE in env.php

   - **System Logs**:
     * Ask: "Log handler?" (file, syslog, database)
     * File: Configure log file path
     * Syslog: Configure syslog settings
     * Database: Use db_log table

   - **Log Level**:
     * Ask: "Log level?" (debug, info, notice, warning, error, critical)
     * Default: debug for dev, error for prod

5. Implement sample data configuration:
   - Ask during installation: "Install sample data?"
   - If yes, run after base install:
     ```
     bin/magento sampledata:deploy
     bin/magento setup:upgrade
     ```
   - Show progress during sample data install

6. Update env.php directly:
   - After setup:install completes, update env.php with:
     * Redis configuration (session, cache, FPC)
     * RabbitMQ configuration
     * Debug settings
     * Logging configuration
   - Use Magento's DeploymentConfig writer

7. Service connection testing:
   - Ping Redis before configuring
   - Test RabbitMQ connection
   - Show clear errors if services unavailable
   - Offer to skip optional services

**Status**: Complete ✅

---

## Stage 3: Theme Installation Support
**Goal**: Allow users to install themes (especially Hyva) during setup
**Success Criteria**:
- User can choose to install Hyva theme during setup
- Theme is installed via Composer
- Theme is activated automatically
- Static content is deployed
**Tests**:
- Test Hyva installation on fresh Magento
- Verify theme is active after install
- Test with other themes (Luma, Blank)

### Tasks
1. Create theme registry system:
   - `ThemeRegistry`: Registry of installable themes
   - Support for Hyva, Luma, Blank initially
   - Extensible interface for theme vendors

2. Implement theme installer:
   - `ThemeInstaller`: Handle composer require
   - Run post-install commands (setup:upgrade, di:compile)
   - Set theme as active in config

3. Add theme selection to installer:
   - "Install a theme?" prompt after base install
   - List available themes with descriptions
   - Option to skip

4. Hyva-specific implementation:
   - Composer require hyva-themes/magento2-default-theme
   - Check compatibility with Magento version
   - Run Hyva-specific setup if needed

5. Post-theme installation:
   - Deploy static content
   - Clear cache
   - Show success message with next steps

**Status**: Complete ✅

---

## Stage 4: Configuration Profiles & Presets
**Goal**: Add installation profiles and configuration management for repeatable installs
**Success Criteria**:
- User can select preset profiles (Development, Production, Docker)
- Config can be saved to/loaded from install.json
- Silent install mode for CI/CD
- Environment variable support
**Tests**:
- Test each profile (dev, prod, docker)
- Test saving/loading config from file
- Test silent install with config file
- Test env var substitution

### Tasks
1. Implement configuration profiles:
   - **Development Profile**:
     * Debug mode: ON
     * Redis: Optional (files if not available)
     * RabbitMQ: Optional
     * Sample data: Yes (default)
     * Log level: debug
     * Backend frontname: admin (convenient)
     * Base URL: http://magento.test

   - **Production Profile**:
     * Debug mode: OFF
     * Redis: Required (sessions, cache, FPC)
     * RabbitMQ: Recommended
     * Sample data: No
     * Log level: error
     * Backend frontname: Custom (security)
     * Base URL: https://... (requires HTTPS)
     * URL rewrites: ON

   - **Docker Profile**:
     * Debug mode: ON
     * Database host: mysql (container name)
     * Redis host: redis (container name)
     * Elasticsearch host: elasticsearch (container name)
     * RabbitMQ host: rabbitmq (container name)
     * Base URL: http://localhost or http://magento.local

   - Ask at start: "Select installation profile?" (Development/Production/Docker/Custom)
   - Pre-populate answers based on profile
   - Allow overriding any value

2. Config file support:
   - Save answers to `install.json` after interactive session
   - Exclude sensitive data by default (passwords)
   - Option to include secrets (encrypted or plain)
   - Add `install.json` to .gitignore automatically
   - JSON format:
     ```json
     {
       "profile": "development",
       "database": {
         "host": "localhost",
         "name": "magento",
         "user": "root",
         "password": "${DB_PASSWORD}"
       },
       "admin": {
         "email": "admin@example.com",
         "username": "admin",
         "password": "${ADMIN_PASSWORD}"
       },
       "redis": {
         "enabled": true,
         "session_host": "127.0.0.1"
       }
     }
     ```

3. Load config from file:
   - `--config=path/to/install.json` flag
   - Merge with interactive inputs
   - Override with CLI flags (highest priority)
   - Validate config file structure

4. Environment variable support:
   - Read from .env file if present
   - Support ${VAR_NAME} syntax in config files
   - Common env vars:
     * DATABASE_URL (parse into components)
     * ELASTICSEARCH_HOST
     * REDIS_HOST
     * RABBITMQ_HOST
     * ADMIN_EMAIL
     * ADMIN_PASSWORD

5. Silent install mode:
   - `--silent` or `--non-interactive` flag
   - Requires config file or all CLI params
   - No prompts, just run and show progress
   - Error if required config missing

6. Dry-run mode:
   - `--dry-run` flag
   - Validate all config without installing
   - Test all service connections
   - Show exactly what would be executed
   - Output full install command

**Status**: Not Started

---

## Stage 5: Post-Install Wizard & Optimizations
**Goal**: Post-install wizard for Varnish, performance optimizations, and final setup
**Success Criteria**:
- Varnish configuration and VCL generation
- Cron setup and validation
- Performance optimization suggestions
- Comprehensive installation summary
**Tests**:
- Test Varnish detection and VCL generation
- Test cron configuration
- Test each optimization recommendation
- Verify summary report shows all configured services

### Tasks
1. Post-install wizard:
   - Run automatically after successful installation
   - Can also be run standalone: `bin/magento install:post-setup`
   - Ask "Would you like to configure..."
   - Each section optional and skippable

2. Varnish configuration:
   - Detect if Varnish is running (port 6081, 6082)
   - Ask: "Configure Varnish for FPC?"
   - Generate VCL file for Varnish 6.x/7.x
   - Save VCL to var/varnish.vcl
   - Show instructions:
     ```
     ✓ Varnish VCL generated: var/varnish.vcl

     To use Varnish:
     1. Copy VCL: sudo cp var/varnish.vcl /etc/varnish/default.vcl
     2. Restart Varnish: sudo systemctl restart varnish
     3. Configure Magento: bin/magento config:set system/full_page_cache/caching_application 2
     4. Clear cache: bin/magento cache:clean
     ```

3. Cron setup:
   - Check if Magento cron is configured
   - Provide crontab example:
     ```
     * * * * * /usr/bin/php /path/to/magento/bin/magento cron:run 2>&1 | grep -v "Ran jobs by schedule"
     * * * * * /usr/bin/php /path/to/magento/update/cron.php 2>&1
     * * * * * /usr/bin/php /path/to/magento/bin/magento setup:cron:run 2>&1
     ```
   - Offer to install cron automatically (if permissions allow)
   - Test cron execution

4. Performance optimizations:
   - **Production Mode**:
     * Recommend if not in debug mode
     * Show command: `bin/magento deploy:mode:set production`

   - **Flat Catalog**:
     * Ask: "Enable flat catalog?" (improves query performance)
     * Enable for products and categories

   - **Merge/Minify**:
     * Ask: "Enable CSS/JS merge and minification?"
     * Configure in stores > configuration

   - **Image Optimization**:
     * Suggest WebP image format
     * Suggest image optimization tools

   - **Database Optimization**:
     * Suggest indexing strategy
     * Show command: `bin/magento indexer:set-mode schedule`

5. Security recommendations:
   - **Two-Factor Authentication**:
     * Remind to configure 2FA for admin
     * Show configuration path

   - **Security.txt**:
     * Offer to create .well-known/security.txt
     * Help with responsible disclosure

   - **Admin URL**:
     * Remind if using default 'admin'
     * Suggest using custom admin URL

6. Comprehensive installation summary:
   ```
   🎉 Mage-OS Installation Complete!

   === Installation Summary ===

   ✓ Database: mysql@localhost/magento
   ✓ Elasticsearch: localhost:9200
   ✓ Redis (Sessions): localhost:6379 (db 0)
   ✓ Redis (Cache): localhost:6379 (db 1)
   ✓ Redis (FPC): localhost:6379 (db 2)
   ✓ RabbitMQ: localhost:5672
   ✓ Theme: Hyva Default
   ✓ Sample Data: Installed

   === Access Information ===

   🌐 Storefront: http://magento.test
   🔐 Admin Panel: http://magento.test/admin_custom
   👤 Admin Username: admin
   📧 Admin Email: admin@example.com

   === Next Steps ===

   1. Configure cron (required):
      bin/magento cron:install

   2. Deploy static content (if not in developer mode):
      bin/magento setup:static-content:deploy -f

   3. Clear cache:
      bin/magento cache:clean

   4. Set proper file permissions:
      find var generated vendor pub/static pub/media app/etc -type f -exec chmod g+w {} +
      find var generated vendor pub/static pub/media app/etc -type d -exec chmod g+ws {} +

   5. Review security settings:
      - Enable Two-Factor Authentication
      - Review admin permissions
      - Set up SSL/TLS certificate

   === Configuration Saved ===

   Your configuration has been saved to: install.json
   To reinstall with same settings: bin/magento install --config=install.json

   === Support ===

   Documentation: https://mage-os.org/docs
   Community: https://discord.gg/mage-os
   Issues: https://github.com/mage-os/mageos/issues

   Happy coding! 🚀
   ```

7. Configuration backup:
   - Save full configuration to install.json
   - Save sanitized version (no passwords) to install.example.json
   - Add both to .gitignore
   - Show locations and usage

**Status**: Not Started

---

## Complete User Experience Flow

Here's the full interactive installation experience with all configuration options:

```bash
$ bin/magento install

🚀 Welcome to Mage-OS Interactive Installer!

Let's set up your store step by step.

=== Installation Profile ===
? Select installation profile:
  1) Development (Debug ON, Redis optional, Sample data)
  2) Production (Debug OFF, Redis required, No sample data)
  3) Docker (Pre-configured for containers)
  4) Custom (Configure everything manually)

  Your choice [1]: 1

✓ Using Development profile

=== Database Configuration ===
? Database host [localhost]: ✓
? Database name [magento]: my_shop
? Database user [root]: magento_user
? Database password: ••••••••
? Table prefix (optional):

🔄 Testing database connection...
✓ Database connection successful!

=== Admin Account ===
? Admin first name: John
? Admin last name: Doe
? Admin email: admin@example.com
? Admin username [admin]: ✓
? Admin password: ••••••••
✓ Strong password detected!

=== Store Configuration ===
? Store URL [http://my_shop.test]: ✓
? Default language [en_US]: ✓
? Default timezone [America/Chicago]: America/New_York
? Default currency [USD]: ✓
? Enable URL rewrites? [Y/n]: y

=== Backend Configuration ===
? Backend admin path [admin]: admin_custom
⚠️  Recommendation: Use a custom admin path for better security

=== Document Root ===
ℹ️  Detected: Document root is /pub
✓ Using secure document root configuration

=== Search Engine ===
🔄 Detecting Elasticsearch/OpenSearch...
✓ Found Elasticsearch on localhost:9200

? Search engine (elasticsearch8, opensearch): elasticsearch8
? Elasticsearch host [localhost:9200]: ✓
? Index prefix (optional):
? Enable authentication? [y/N]: n

=== Redis Configuration ===
🔄 Detecting Redis instances...
✓ Found Redis on localhost:6379

? Use Redis for session storage? [Y/n]: y
? Redis session host [127.0.0.1]: ✓
? Redis session port [6379]: ✓
? Redis session database [0]: ✓

? Use Redis for cache backend? [Y/n]: y
? Redis cache host [127.0.0.1]: ✓
? Redis cache port [6379]: ✓
? Redis cache database [1]: ✓

? Use Redis for Full Page Cache? [Y/n]: y
? Redis FPC host [127.0.0.1]: ✓
? Redis FPC port [6379]: ✓
? Redis FPC database [2]: ✓

=== RabbitMQ Configuration ===
🔄 Detecting RabbitMQ...
⚠️  RabbitMQ not detected on localhost:5672

? Configure RabbitMQ? [y/N]: n
ℹ️  Skipping RabbitMQ configuration

=== Debug & Logging ===
? Enable debug mode? [Y/n]: y
? Log handler (file/syslog/database) [file]: file
? Log level (debug/info/warning/error) [debug]: debug

=== Optional Features ===
? Install sample data? [Y/n]: y
? Install a theme? [y/N]: y
  Available themes:
  1) Luma (default)
  2) Hyva (open source)
  3) Blank

? Select theme [1]: 2

🎯 Configuration complete! Here's what will be installed:

  Database: mysql@localhost/my_shop
  Admin: admin@example.com
  Store: http://my_shop.test
  Elasticsearch: localhost:9200
  Redis: Sessions, Cache, FPC
  Debug Mode: ON
  Sample Data: Yes
  Theme: Hyva

? Proceed with installation? [Y/n]: y

🚀 Starting installation...

🔄 Installing Magento core...
  ✓ Database schema created
  ✓ Default data inserted
  ✓ Admin user created
  ✓ Store configuration applied

🔄 Configuring services...
  ✓ Redis (Session storage) configured
  ✓ Redis (Cache backend) configured
  ✓ Redis (Full Page Cache) configured
  ✓ Elasticsearch configured

🔄 Installing sample data...
  ✓ Sample data modules deployed
  ✓ Sample data installed

🔄 Installing Hyva theme...
  ✓ Hyva theme installed via Composer
  ✓ Theme activated
  ✓ Static content deployed

⏱️  Installation completed in 2m 34s

=== Post-Install Configuration ===

? Configure Varnish? [y/N]: n
? Enable flat catalog? [y/N]: n
? Enable CSS/JS merge and minification? [y/N]: n

🎉 Mage-OS Installation Complete!

=== Installation Summary ===

✓ Database: mysql@localhost/my_shop
✓ Elasticsearch: localhost:9200
✓ Redis (Sessions): localhost:6379 (db 0)
✓ Redis (Cache): localhost:6379 (db 1)
✓ Redis (FPC): localhost:6379 (db 2)
✓ Theme: Hyva Default
✓ Sample Data: Installed
✓ Debug Mode: Enabled

=== Access Information ===

🌐 Storefront: http://my_shop.test
🔐 Admin Panel: http://my_shop.test/admin_custom
👤 Admin Username: admin
📧 Admin Email: admin@example.com

=== Next Steps ===

1. Configure cron (required):
   bin/magento cron:install

2. Clear cache:
   bin/magento cache:clean

3. Open your store:
   http://my_shop.test

=== Configuration Saved ===

Your configuration has been saved to: install.json
To reinstall: bin/magento install --config=install.json

Happy coding! 🚀
```

---

## Testing Strategy

### Unit Tests (PHPUnit)
- Test each config collector independently
- Test each detector with mocked services
- Test validators with various inputs
- Test theme registry
- Located in: `app/code/MageOS/Installer/Test/Unit/`

### Integration Tests
- Test InstallCommand with mocked QuestionHelper
- Test full flow with test database
- Test theme installation flow
- Located in: `app/code/MageOS/Installer/Test/Integration/`

### Manual Testing Checklist
- [ ] Fresh install on clean environment
- [ ] Install with existing database
- [ ] Install with invalid credentials
- [ ] Install with unreachable services
- [ ] Interrupt installation mid-process
- [ ] Install with Hyva theme
- [ ] Install with config file
- [ ] Silent install
- [ ] Dry-run mode

---

## Security Considerations

1. **Password Handling**:
   - Never log passwords in plain text
   - Use hidden input for password prompts
   - Enforce minimum password strength
   - Option to generate secure passwords

2. **Configuration Files**:
   - Warn about secrets in config files
   - Add install.json to .gitignore
   - Support .env-style variable substitution

3. **Database Permissions**:
   - Test DB user has sufficient privileges
   - Warn if using root user
   - Suggest dedicated DB user

4. **Admin Path**:
   - Warn if using default 'admin' path
   - Suggest custom admin path
   - Validate path doesn't conflict

---

## UX Enhancements

### Visual Feedback
- ✓ Success indicators (green)
- ❌ Error messages (red)
- ⚠️ Warnings (yellow)
- ℹ️ Info/help text (cyan)
- 🔄 Progress spinners

### User Experience
- Group related questions
- Show default values in brackets: `[localhost]`
- Provide contextual help for each option
- Allow skipping optional sections
- Show command summary before execution
- Provide rollback on failure

---

## Technical Decisions

### Why a New Command?
- Don't break existing `setup:install` workflow
- Give users choice between old and new
- Can deprecate old interactive mode later
- Allows complete UX redesign

### Why Wrapper Pattern?
- Don't reimplement battle-tested installation logic
- Reduce maintenance burden
- Ensure compatibility
- Focus on UX improvements

### Module Location
- `app/code/MageOS/Installer/` (not in core Magento code)
- Can be distributed separately
- Easier to maintain and update
- Clear ownership

### Extensibility
- Theme vendors can register via DI
- Events dispatched during installation
- Plugin system for custom steps
- Compatible with existing Magento extension patterns

---

## Completion Criteria

All stages are complete when:
- ✓ All tests passing
- ✓ Code follows Magento conventions
- ✓ Documentation is complete
- ✓ Manual testing checklist passed
- ✓ No linter/formatter warnings
- ✓ This IMPLEMENTATION_PLAN.md is removed

---

## Next Steps

1. Get approval on this plan
2. Set up feature branch: `feature/interactive-installer`
3. Begin Stage 1 implementation
4. Iterate based on feedback
5. Release as beta for community testing
