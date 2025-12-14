# Installer Refactoring Implementation Plan

## Overview
Refactor the Mage-OS interactive installer to improve code quality, user experience, and maintainability.

## Goals
1. Add progress indicators and navigation (back/forward)
2. Enhance error messages with actionable guidance
3. Split god classes into manageable components (< 500 lines per class)
4. Use Symfony Process instead of exec()
5. Remove passwords from saved config (re-prompt on resume)
6. Use Value Objects instead of arrays
7. Consolidate duplicate validation logic

---

## Stage 1: Create Value Objects
**Goal**: Replace array-based configuration with type-safe Value Objects
**Success Criteria**: All configuration data uses typed objects with proper validation
**Status**: ✅ Complete

### Tasks:
- [ ] Create directory `setup/src/MageOS/Installer/Model/VO/`
- [ ] Create `VO/DatabaseConfiguration.php`
- [ ] Create `VO/AdminConfiguration.php`
- [ ] Create `VO/StoreConfiguration.php`
- [ ] Create `VO/BackendConfiguration.php`
- [ ] Create `VO/SearchEngineConfiguration.php`
- [ ] Create `VO/RedisConfiguration.php`
- [ ] Create `VO/RabbitMQConfiguration.php`
- [ ] Create `VO/LoggingConfiguration.php`
- [ ] Create `VO/SampleDataConfiguration.php`
- [ ] Create `VO/ThemeConfiguration.php`
- [ ] Create `VO/EnvironmentConfiguration.php`
- [ ] Create `VO/CronConfiguration.php`
- [ ] Create `VO/EmailConfiguration.php`

### Tests:
- Unit tests for each VO constructor validation
- Test serialization/deserialization (without passwords)

---

## Stage 2: Create Installation Context
**Goal**: Single object to hold all configuration state
**Success Criteria**: No more passing 10+ parameters between methods
**Status**: ✅ Complete

### Tasks:
- [ ] Create `Model/InstallationContext.php`
- [ ] Add typed getters/setters for each configuration VO
- [ ] Add serialization methods (excluding passwords)
- [ ] Add `getSensitiveFields()` method to identify fields to exclude
- [ ] Update ConfigFileManager to work with Context

### Tests:
- Test context state management
- Test serialization excludes sensitive data
- Test deserialization and re-prompting for passwords

---

## Stage 3: Extract Stage Pattern
**Goal**: Break InstallCommand into composable, testable stages
**Success Criteria**:
- Each installation step is an independent, testable class
- InstallCommand.php < 300 lines (achieved: 228 lines!)
- Stages support navigation (back/forward)
**Status**: ✅ Complete

### Tasks:
- [ ] Create `Model/Stage/InstallationStageInterface.php`
- [ ] Create `Model/Stage/AbstractStage.php` (base class)
- [ ] Create `Model/Stage/StageNavigator.php` for navigation
- [ ] Create `Model/Stage/StageResult.php` (success/back/retry)
- [ ] Create stages:
  - [ ] `WelcomeStage.php`
  - [ ] `EnvironmentConfigStage.php`
  - [ ] `DatabaseConfigStage.php`
  - [ ] `AdminConfigStage.php`
  - [ ] `StoreConfigStage.php`
  - [ ] `BackendConfigStage.php`
  - [ ] `DocumentRootInfoStage.php`
  - [ ] `SearchEngineConfigStage.php`
  - [ ] `RedisConfigStage.php`
  - [ ] `RabbitMQConfigStage.php`
  - [ ] `LoggingConfigStage.php`
  - [ ] `SampleDataConfigStage.php`
  - [ ] `ThemeConfigStage.php`
  - [ ] `SummaryStage.php`
  - [ ] `PermissionCheckStage.php`
  - [ ] `ThemeInstallationStage.php`
  - [ ] `MagentoInstallationStage.php`
  - [ ] `PostInstallConfigStage.php`
  - [ ] `CompletionStage.php`
- [ ] Update `InstallCommand::execute()` to orchestrate stages
- [ ] Refactor InstallCommand to < 300 lines

### Tests:
- Unit test each stage independently
- Test stage navigation (forward/back)
- Integration test full stage execution

---

## Stage 4: Add Progress Tracking
**Goal**: Show users where they are in the installation process
**Success Criteria**: Every stage shows "Step X of Y" and progress indication
**Status**: ✅ Complete

### Tasks:
- [ ] Create `Model/Progress/ProgressTracker.php`
- [ ] Add progress display to AbstractStage
- [ ] Update all stages to show progress
- [ ] Add progress bar helper
- [ ] Add "What's next" preview after each stage

### Tests:
- Test progress calculation
- Test display formatting

---

## Stage 5: Replace exec() with Process
**Goal**: Use Symfony Process for all command execution
**Success Criteria**: No direct exec() calls, all commands use Process
**Status**: ✅ Complete

### Tasks:
- [ ] Create `Model/Command/ProcessRunner.php`
- [ ] Create `Model/Command/CronConfigurer.php`
- [ ] Create `Model/Command/EmailConfigurer.php`
- [ ] Create `Model/Command/ModeConfigurer.php`
- [ ] Create `Model/Command/SetupInstallCommandBuilder.php`
- [ ] Update all exec() calls to use Process
- [ ] Add proper timeout and error handling

### Tests:
- Mock Process execution
- Test timeout handling
- Test error handling and recovery

---

## Stage 6: Enhance Error Messages
**Goal**: Provide actionable guidance when errors occur
**Success Criteria**: Every error includes "What to do next" section
**Status**: Not Started

### Tasks:
- [ ] Create `Model/Error/ErrorMessageFormatter.php`
- [ ] Create `Model/Error/ErrorRecoveryGuide.php`
- [ ] Create `Model/Error/PreFlightChecker.php`
- [ ] Add comprehensive pre-flight checks:
  - [ ] PHP extensions
  - [ ] PHP memory limit
  - [ ] Disk space
  - [ ] Database permissions
  - [ ] Service connectivity
- [ ] Update all error handling to use formatter
- [ ] Add rollback/cleanup helpers

### Tests:
- Test error message formatting
- Test recovery suggestions accuracy
- Test pre-flight checks

---

## Stage 7: Remove Passwords from Saved Config
**Goal**: Never persist passwords to disk
**Success Criteria**: Config file contains no sensitive data
**Status**: ✅ Complete (handled by InstallationContext and #[Sensitive] attribute)

### Tasks:
- [ ] Update `ConfigFileManager::save()` to exclude passwords
- [ ] Add `InstallationContext::getSensitiveFields()` method
- [ ] Create `Model/Config/SensitiveFieldCollector.php` for resume
- [ ] Add re-prompt logic in stages for:
  - Database password
  - Admin password
  - RabbitMQ password (if configured)
  - Email password (if configured)
- [ ] Update resume flow to re-collect passwords
- [ ] Add encryption option (future enhancement)

### Tests:
- Test saved config contains no passwords
- Test resume flow re-prompts correctly
- Test validation on re-prompted passwords

---

## Stage 8: Consolidate Validation Logic
**Goal**: Remove duplicate validation code
**Success Criteria**: Each validation rule exists in exactly one place
**Status**: ✅ Complete

### Tasks:
- [ ] Extract password validation to `Model/Validator/PasswordValidator.php`
- [ ] Remove duplicate password validation from AdminConfig and InstallCommand
- [ ] Remove duplicate search validation from SearchEngineConfig and InstallCommand
- [ ] Update collectors to use shared validators
- [ ] Remove validation from InstallCommand

### Tests:
- Test each validator independently
- Test validators are reused correctly

---

## Stage 9: Update Config Collectors
**Goal**: Make collectors return Value Objects instead of arrays
**Success Criteria**: All collectors return typed configuration objects
**Status**: Not Started

### Tasks:
- [ ] Update `DatabaseConfig::collect()` to return `DatabaseConfiguration`
- [ ] Update `AdminConfig::collect()` to return `AdminConfiguration`
- [ ] Update `StoreConfig::collect()` to return `StoreConfiguration`
- [ ] Update `BackendConfig::collect()` to return `BackendConfiguration`
- [ ] Update `SearchEngineConfig::collect()` to return `SearchEngineConfiguration`
- [ ] Update `RedisConfig::collect()` to return `RedisConfiguration`
- [ ] Update `RabbitMQConfig::collect()` to return `RabbitMQConfiguration`
- [ ] Update `LoggingConfig::collect()` to return `LoggingConfiguration`
- [ ] Update `SampleDataConfig::collect()` to return `SampleDataConfiguration`
- [ ] Update `ThemeConfig::collect()` to return `ThemeConfiguration`
- [ ] Update `EnvironmentConfig::collect()` to return `EnvironmentConfiguration`
- [ ] Update `CronConfig::collect()` to return `CronConfiguration`
- [ ] Update `EmailConfig::collect()` to return `EmailConfiguration`

### Tests:
- Test each collector returns correct VO type
- Test validation still works with VOs

---

## Stage 10: Integration & Testing
**Goal**: Ensure everything works end-to-end
**Success Criteria**: Full installation completes successfully with all features
**Status**: Not Started

### Tasks:
- [ ] Run full installation test
- [ ] Test resume capability (with password re-prompting)
- [ ] Test back navigation
- [ ] Test error recovery
- [ ] Test all optional features (Redis, RabbitMQ, etc.)
- [ ] Test theme installation
- [ ] Test progress indicators
- [ ] Update README with refactoring notes
- [ ] Clean up old code and comments
- [ ] Remove IMPLEMENTATION_PLAN.md

### Tests:
- Full integration test suite
- Test matrix for different configurations
- Performance testing (should not be slower)

---

## Key Architecture Decisions

### Stage Pattern
```php
interface InstallationStageInterface {
    public function getName(): string;
    public function execute(InstallationContext $context, OutputInterface $output): StageResult;
    public function canGoBack(): bool;
    public function getProgressWeight(): int; // For progress calculation
}

class StageResult {
    const CONTINUE = 'continue';
    const GO_BACK = 'back';
    const RETRY = 'retry';
    const ABORT = 'abort';

    public function __construct(
        public readonly string $status,
        public readonly ?string $message = null
    ) {}
}
```

### Value Objects
```php
final readonly class DatabaseConfiguration {
    public function __construct(
        public string $host,
        public string $name,
        public string $user,
        #[Sensitive] // Custom attribute to mark sensitive fields
        public string $password,
        public string $prefix = ''
    ) {}

    public function toArray(bool $includeSensitive = false): array {
        $data = [
            'host' => $this->host,
            'name' => $this->name,
            'user' => $this->user,
            'prefix' => $this->prefix
        ];

        if ($includeSensitive) {
            $data['password'] = $this->password;
        }

        return $data;
    }
}
```

### Navigation Support
- Each stage returns StageResult indicating next action
- StageNavigator maintains stage history for back navigation
- "Go back" option in Laravel Prompts (when applicable)
- Context preserves all non-sensitive data between stages

### Process Execution
```php
class ProcessRunner {
    public function run(array $command, string $cwd, int $timeout = 300): ProcessResult {
        $process = new Process($command, $cwd, null, null, $timeout);

        try {
            $process->mustRun();
            return new ProcessResult(true, $process->getOutput());
        } catch (ProcessFailedException $e) {
            return new ProcessResult(
                false,
                $process->getOutput(),
                $e->getMessage()
            );
        }
    }
}
```

---

## Breaking Changes

**None for end users** - This is purely internal refactoring. The command behavior remains the same:
- `bin/magento install` works identically
- Config file format stays the same (but passwords excluded)
- Resume capability improved (not broken)

**Internal APIs changed:**
- Config collectors return VOs instead of arrays
- InstallCommand is split into stages
- Private methods moved to dedicated classes

---

## Success Metrics

After completion, we should have:
- [ ] InstallCommand.php < 300 lines (currently 1048)
- [ ] No class > 500 lines
- [ ] All exec() calls replaced with Process
- [ ] No passwords in saved config
- [ ] 100% type coverage on configuration (no arrays)
- [ ] Error messages include actionable steps
- [ ] Users can navigate back through stages
- [ ] Progress indicator on every step
- [ ] All validation logic deduplicated
- [ ] Test coverage > 80%

---

## Migration Strategy

1. **Keep both implementations temporarily**
   - Old array-based methods marked @deprecated
   - New VO-based methods added alongside

2. **Gradual migration**
   - Stage 1-2: Create VOs and Context (no breaking changes)
   - Stage 3-5: Extract stages and commands (internal only)
   - Stage 6-8: Improve UX (user-facing improvements)
   - Stage 9: Switch collectors to return VOs
   - Stage 10: Remove deprecated code

3. **Testing at each stage**
   - Unit tests for new components
   - Integration tests ensure nothing breaks
   - Manual testing of full flow

---

## Timeline Estimate

**Note**: Following the philosophy of "planning without timelines" - these are complexity estimates, not time commitments.

- Stage 1 (Value Objects): Medium - Straightforward but many files
- Stage 2 (Context): Small - Single class
- Stage 3 (Stage Pattern): Large - Major refactoring
- Stage 4 (Progress): Small - UI enhancement
- Stage 5 (Process): Medium - Several command classes
- Stage 6 (Error Messages): Medium - Requires thought on UX
- Stage 7 (Remove Passwords): Small - File I/O changes
- Stage 8 (Validation): Small - Extract existing code
- Stage 9 (Update Collectors): Medium - Touch many files
- Stage 10 (Testing): Large - Comprehensive testing

**Total complexity**: Large project, systematic approach needed.

---

## Next Actions

1. Review this plan
2. Create feature branch: `feature/installer-refactoring`
3. Start with Stage 1 (Value Objects) - foundational
4. Commit after each stage completes
5. Test thoroughly between stages
