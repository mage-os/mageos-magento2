# MageOS Installer - Unit Test Coverage Implementation Plan

## Executive Summary

**Current State:** 77 PHP files, 0 unit tests (💀)
**Target State:** ~345 unit tests, 85%+ coverage
**Timeline:** 4-5 weeks focused work (or 2-3 weeks with 2 people)
**Priority:** Data integrity (VOs, serialization, file I/O)

## Architecture Overview

### Component Breakdown
- **21 Installation Stages** - Orchestrate installation flow
- **13 Value Objects (VOs)** - Immutable configuration data
- **13 Config Collectors** - Interactive user input (Laravel Prompts)
- **6 Detectors** - Auto-detect system configuration
- **5 Validators** - Input validation and sanitization
- **3 Command Executors** - Safe process execution
- **3 Theme Handlers** - Theme installation logic
- **2 Writers** - File I/O (env.php, config files)
- **1 Context Container** - Central state management
- **1 Stage Navigator** - Flow orchestration

### Critical Dependencies
- Laravel Prompts (interactive CLI)
- Symfony Process (shell execution)
- PDO (database connections)
- Magento CLI (setup:install, etc.)
- File system (env.php, config persistence)

---

## Testing Philosophy

### Test Pyramid Distribution
- **Unit Tests (70%)** - Fast, isolated, focused on logic
- **Integration Tests (20%)** - Multi-component interactions
- **E2E Tests (10%)** - Full installation flow (Magento's existing test suite)

### Coverage Targets
- **Tier 1 (Critical Path):** 95%+ coverage
- **Tier 2 (High Value):** 85%+ coverage
- **Tier 3 (Supporting):** 70%+ coverage
- **Overall Project:** 85%+ coverage

### Quality Gates
- ✅ All tests passing (no skips/incomplete)
- ✅ Coverage thresholds met
- ✅ Fast execution (<5s for all unit tests)
- ✅ Deterministic (no flaky tests)
- ✅ CI blocks merges below coverage threshold

---

## Phase 1: Foundation & Critical Path (Week 1)

**Goal:** Establish test infrastructure and cover core data structures
**Test Count:** ~145 tests
**Coverage:** VOs + InstallationContext (Tier 1)

### Stage 1.1: Test Infrastructure Setup
**Status:** Not Started
**Estimated Time:** 1 day

**Tasks:**
1. Create `setup/phpunit.xml` configuration
2. Create `setup/tests/bootstrap.php` autoloader
3. Create directory structure: `setup/tests/unit/MageOS/Installer/`
4. Install dev dependencies:
   - PHPUnit 10+
   - mikey179/vfsStream (virtual filesystem)
5. Create abstract base classes:
   - `AbstractVOTest` - Common VO test patterns
   - `FileSystemTestCase` - vfsStream setup
6. Create test utilities:
   - `TestDataBuilder` - Fixture creation
   - `VoAssertions` trait - Common assertions
   - `MockFactory` - Dependency mocking

**Success Criteria:**
- `vendor/bin/phpunit` executes successfully
- Test directory structure mirrors source
- Example test passes

### Stage 1.2: Value Object Tests (13 VOs)
**Status:** Not Started
**Estimated Time:** 3 days

**Test Pattern (applies to all VOs):**
```php
final class {VO}ConfigurationTest extends AbstractVOTest {
    // Construction
    public function test_it_constructs_with_valid_data(): void

    // Serialization
    public function test_to_array_excludes_sensitive_fields(): void
    public function test_to_array_includes_sensitive_when_requested(): void
    public function test_to_array_handles_null_values(): void

    // Deserialization
    public function test_from_array_creates_instance(): void
    public function test_from_array_handles_missing_fields(): void
    public function test_from_array_type_coercion(): void

    // Round-trip
    public function test_round_trip_preserves_data(): void

    // Edge cases (VO-specific)
}
```

**Value Objects to Test:**
1. ✅ `EnvironmentConfiguration` (~10 tests)
2. ✅ `DatabaseConfiguration` (~12 tests) - passwords, validation
3. ✅ `AdminConfiguration` (~12 tests) - passwords, email validation
4. ✅ `StoreConfiguration` (~10 tests) - URL/timezone validation
5. ✅ `BackendConfiguration` (~8 tests)
6. ✅ `SearchEngineConfiguration` (~10 tests) - engine types, ports
7. ✅ `RedisConfiguration` (~12 tests) - multiple DBs, flags
8. ✅ `RabbitMQConfiguration` (~12 tests) - AMQP, passwords
9. ✅ `LoggingConfiguration` (~8 tests)
10. ✅ `SampleDataConfiguration` (~6 tests)
11. ✅ `ThemeConfiguration` (~8 tests)
12. ✅ `CronConfiguration` (~10 tests)
13. ✅ `EmailConfiguration` (~12 tests) - SMTP, passwords

**Total VO Tests:** ~130 tests

**Implementation Order:**
1. `DatabaseConfiguration` first (reference implementation)
2. Replicate pattern for remaining 12 VOs
3. Use `AbstractVOTest` to reduce duplication

**Success Criteria:**
- All VOs serialize/deserialize correctly
- Sensitive fields excluded from toArray(includeSensitive: false)
- Round-trip preserves data
- Type coercion works (string "80" → int 80)
- 95%+ coverage per VO

### Stage 1.3: InstallationContext Tests
**Status:** Not Started
**Estimated Time:** 1 day

**Test File:** `setup/tests/unit/Model/InstallationContextTest.php`

**Test Cases:**
1. `test_it_constructs_empty_context(): void`
2. `test_it_serializes_without_sensitive_data(): void`
3. `test_it_serializes_with_sensitive_data(): void`
4. `test_it_deserializes_from_array(): void`
5. `test_round_trip_preserves_non_sensitive_data(): void`
6. `test_is_ready_with_minimal_config(): void`
7. `test_is_ready_with_missing_required_fields(): void`
8. `test_get_missing_passwords_finds_all_sensitive_fields(): void`
9. `test_get_missing_passwords_returns_empty_when_all_set(): void`
10. `test_handles_null_vos_gracefully(): void`
11. `test_handles_partial_configuration(): void`
12. `test_nested_vo_serialization(): void`
13. `test_handles_malformed_array_data(): void`
14. `test_validation_checks_required_fields(): void`
15. `test_large_configuration_performance(): void`

**Total Tests:** ~15 tests

**Success Criteria:**
- Serialization excludes passwords correctly
- Deserialization reconstructs VOs correctly
- `isReadyForInstallation()` validates properly
- `getMissingPasswords()` finds all sensitive fields
- 95%+ coverage

---

## Phase 2: Configuration Management (Week 2)

**Goal:** Test persistence and state management
**Test Count:** ~35 tests
**Coverage:** ConfigFileManager + EnvConfigWriter (Tier 1)

### Stage 2.1: ConfigFileManager Tests
**Status:** Not Started
**Estimated Time:** 1 day

**Test File:** `setup/tests/unit/Model/Writer/ConfigFileManagerTest.php`

**Test Strategy:** Use vfsStream for virtual file system

**Test Cases:**
1. `test_saves_configuration_to_file(): void`
2. `test_loads_configuration_from_file(): void`
3. `test_round_trip_preserves_data(): void`
4. `test_excludes_sensitive_data_from_saved_file(): void`
5. `test_deletes_configuration_file(): void`
6. `test_handles_missing_file_gracefully(): void`
7. `test_handles_corrupted_json(): void`
8. `test_handles_json_with_extra_fields(): void`
9. `test_handles_permission_denied_on_save(): void`
10. `test_handles_permission_denied_on_load(): void`
11. `test_handles_disk_full_during_save(): void`
12. `test_overwrites_existing_config(): void`
13. `test_sets_restrictive_file_permissions(): void`
14. `test_returns_null_when_no_config_exists(): void`
15. `test_file_ownership_is_correct(): void`

**Total Tests:** ~15 tests

**Success Criteria:**
- Save/load round-trips preserve data
- Sensitive data excluded from file
- File permissions are 0600 (read/write owner only)
- Graceful error handling (missing file, corrupted JSON)
- 95%+ coverage

### Stage 2.2: EnvConfigWriter Tests
**Status:** Not Started
**Estimated Time:** 2 days

**Test File:** `setup/tests/unit/Model/Writer/EnvConfigWriterTest.php`

**Test Strategy:** Use vfsStream + real PHP file operations

**Test Cases:**

**Redis Configuration:**
1. `test_writes_redis_session_config(): void`
2. `test_writes_redis_cache_config(): void`
3. `test_writes_redis_fpc_config(): void`
4. `test_writes_combined_redis_config(): void`
5. `test_writes_redis_with_different_db_numbers(): void`

**RabbitMQ Configuration:**
6. `test_writes_rabbitmq_amqp_config(): void`
7. `test_writes_rabbitmq_with_virtual_host(): void`
8. `test_writes_rabbitmq_with_credentials(): void`

**Preservation Logic:**
9. `test_preserves_existing_backend_config(): void`
10. `test_preserves_existing_database_config(): void`
11. `test_merges_with_existing_cache_config(): void`
12. `test_overwrites_only_redis_sections(): void`
13. `test_overwrites_only_rabbitmq_sections(): void`

**Edge Cases:**
14. `test_creates_file_if_not_exists(): void`
15. `test_handles_empty_env_file(): void`
16. `test_handles_non_array_return_value(): void`
17. `test_handles_corrupted_php_file(): void`
18. `test_handles_file_not_writable(): void`
19. `test_maintains_php_format_with_var_export(): void`
20. `test_handles_complex_nested_arrays(): void`

**Total Tests:** ~20 tests

**Success Criteria:**
- Writes valid PHP files with `<?php return [...]` format
- Preserves existing config (merges, not overwrites)
- Only modifies Redis/RabbitMQ sections
- File remains parseable after write
- 95%+ coverage

---

## Phase 3: Validation Layer (Week 2-3)

**Goal:** Ensure input validation is thorough
**Test Count:** ~50 tests
**Coverage:** 5 Validators (Tier 2)

### Stage 3.1: DatabaseValidator Tests
**Status:** Not Started
**Estimated Time:** 1 day

**Test File:** `setup/tests/unit/Model/Validator/DatabaseValidatorTest.php`

**Test Cases:**
1. `test_builds_dsn_correctly(): void`
2. `test_validates_hostname_format(): void`
3. `test_validates_port_range(): void`
4. `test_validates_database_name(): void`
5. `test_rejects_empty_hostname(): void`
6. `test_rejects_invalid_port(): void`
7. `test_provides_helpful_error_for_connection_failure(): void`
8. `test_provides_helpful_error_for_auth_failure(): void`
9. `test_sanitizes_against_sql_injection(): void`
10. `test_handles_connection_timeout(): void`

**Total Tests:** ~10 tests

**Mocking Strategy:** Mock PDO for unit tests (integration test with real DB separately)

### Stage 3.2: UrlValidator Tests
**Status:** Not Started
**Estimated Time:** 0.5 days

**Test File:** `setup/tests/unit/Model/Validator/UrlValidatorTest.php`

**Test Cases (use data providers):**
1. `test_accepts_valid_urls(string $url): void` [@dataProvider validUrlProvider]
   - http://example.com
   - https://example.com
   - http://localhost:8080
   - https://example.com/path?query=value
   - International domains (IDN)

2. `test_rejects_invalid_urls(string $url, string $expectedError): void` [@dataProvider invalidUrlProvider]
   - Missing scheme
   - Invalid protocol (ftp://)
   - Malformed URLs
   - Invalid characters

**Total Tests:** ~10 tests (via data providers)

### Stage 3.3: EmailValidator Tests
**Status:** Not Started
**Estimated Time:** 0.5 days

**Test File:** `setup/tests/unit/Model/Validator/EmailValidatorTest.php`

**Test Cases (use data providers):**
1. `test_accepts_valid_emails(string $email): void` [@dataProvider validEmailProvider]
   - Standard formats
   - Plus addressing (user+tag@example.com)
   - Subdomains
   - International characters

2. `test_rejects_invalid_emails(string $email): void` [@dataProvider invalidEmailProvider]
   - Missing @
   - Invalid domain
   - Invalid characters
   - Too long

**Total Tests:** ~10 tests (via data providers)

### Stage 3.4: SearchEngineValidator Tests
**Status:** Not Started
**Estimated Time:** 1 day

**Test File:** `setup/tests/unit/Model/Validator/SearchEngineValidatorTest.php`

**Test Cases:**
1. `test_validates_opensearch_connection(): void`
2. `test_validates_elasticsearch_connection(): void`
3. `test_validates_port_range(): void`
4. `test_validates_hostname_format(): void`
5. `test_handles_connection_timeout(): void`
6. `test_handles_connection_refused(): void`
7. `test_handles_http_errors(): void`
8. `test_provides_helpful_error_messages(): void`
9. `test_validates_engine_type_enum(): void`
10. `test_validates_index_prefix(): void`

**Total Tests:** ~10 tests

**Mocking Strategy:** Mock HTTP client for unit tests

### Stage 3.5: PasswordValidator Tests
**Status:** Not Started
**Estimated Time:** 1 day

**Test File:** `setup/tests/unit/Model/Validator/PasswordValidatorTest.php`

**Test Cases:**
1. `test_enforces_minimum_length(): void`
2. `test_requires_uppercase_letter(): void`
3. `test_requires_lowercase_letter(): void`
4. `test_requires_number(): void`
5. `test_requires_special_character(): void`
6. `test_accepts_valid_password(): void`
7. `test_rejects_too_short_password(): void`
8. `test_rejects_password_without_uppercase(): void`
9. `test_rejects_password_without_lowercase(): void`
10. `test_rejects_password_without_number(): void`
11. `test_rejects_password_without_special_char(): void`
12. `test_provides_helpful_error_messages(): void`
13. `test_handles_unicode_characters(): void`
14. `test_checks_maximum_length_if_applicable(): void`

**Total Tests:** ~14 tests

---

## Phase 4: Orchestration Logic (Week 3)

**Goal:** Test stage navigation and flow control
**Test Count:** ~30 tests
**Coverage:** StageNavigator + StageResult (Tier 1)

### Stage 4.1: StageNavigator Tests
**Status:** Not Started
**Estimated Time:** 2 days

**Test File:** `setup/tests/unit/Model/Stage/StageNavigatorTest.php`

**Test Strategy:** Mock individual stages, control their return values

**Test Cases:**

**Forward Navigation:**
1. `test_executes_stages_in_correct_order(): void`
2. `test_handles_continue_result(): void`
3. `test_passes_context_between_stages(): void`
4. `test_reaches_completion_after_final_stage(): void`
5. `test_updates_progress_after_each_stage(): void`

**Back Navigation:**
6. `test_handles_go_back_result(): void`
7. `test_cannot_go_back_from_installation_stages(): void`
8. `test_can_go_back_from_config_stages(): void`
9. `test_back_navigation_updates_progress_correctly(): void`
10. `test_back_navigation_to_previous_stage(): void`

**Error Handling:**
11. `test_handles_abort_result(): void`
12. `test_handles_retry_result(): void`
13. `test_handles_exception_in_stage(): void`
14. `test_prevents_infinite_retry_loop(): void`
15. `test_prevents_infinite_back_loop(): void`

**Progress Tracking:**
16. `test_calculates_progress_with_weights(): void`
17. `test_progress_never_exceeds_100_percent(): void`
18. `test_progress_never_goes_negative(): void`
19. `test_info_stages_have_zero_weight(): void`
20. `test_installation_stages_have_high_weight(): void`

**Edge Cases:**
21. `test_handles_empty_stage_list(): void`
22. `test_handles_single_stage(): void`
23. `test_all_stages_return_go_back(): void`
24. `test_stage_execution_order_is_deterministic(): void`
25. `test_context_mutations_persist_across_stages(): void`

**Total Tests:** ~25 tests

**Success Criteria:**
- All stage transitions work correctly
- Back navigation respects stage rules
- Progress calculation is accurate
- Error handling is graceful
- 95%+ coverage

### Stage 4.2: StageResult Tests
**Status:** Not Started
**Estimated Time:** 0.5 days

**Test File:** `setup/tests/unit/Model/Stage/StageResultTest.php`

**Test Cases:**
1. `test_continue_result_has_correct_value(): void`
2. `test_go_back_result_has_correct_value(): void`
3. `test_retry_result_has_correct_value(): void`
4. `test_abort_result_has_correct_value(): void`
5. `test_enum_values_are_unique(): void`

**Total Tests:** ~5 tests

---

## Phase 5: Supporting Services (Week 4)

**Goal:** Test command execution and detection
**Test Count:** ~50 tests
**Coverage:** ProcessRunner + Detectors (Tier 2-3)

### Stage 5.1: ProcessRunner Tests
**Status:** Not Started
**Estimated Time:** 1 day

**Test File:** `setup/tests/unit/Model/Command/ProcessRunnerTest.php`

**Test Cases:**
1. `test_executes_command_successfully(): void`
2. `test_captures_command_output(): void`
3. `test_captures_command_errors(): void`
4. `test_handles_command_failure(): void`
5. `test_handles_command_timeout(): void`
6. `test_sanitizes_command_arguments(): void`
7. `test_prevents_command_injection(): void`
8. `test_sets_working_directory(): void`
9. `test_sets_environment_variables(): void`
10. `test_returns_process_result_object(): void`

**Total Tests:** ~10 tests

**Mocking Strategy:** Mock Symfony Process component

### Stage 5.2: Detector Tests (6 Detectors)
**Status:** Not Started
**Estimated Time:** 3 days

**Test Files:**
- `DatabaseDetectorTest.php` (~5 tests)
- `DocumentRootDetectorTest.php` (~5 tests)
- `SearchEngineDetectorTest.php` (~5 tests)
- `RedisDetectorTest.php` (~5 tests)
- `RabbitMQDetectorTest.php` (~5 tests)
- `UrlDetectorTest.php` (~5 tests)

**Common Test Pattern:**
1. `test_detects_when_service_available(): void`
2. `test_returns_null_when_service_unavailable(): void`
3. `test_auto_detects_connection_details(): void`
4. `test_handles_detection_errors_gracefully(): void`
5. `test_provides_sensible_defaults(): void`

**Total Tests:** ~30 tests (6 detectors × 5 tests)

**Mocking Strategy:** Mock service connections, control availability

### Stage 5.3: Command Configurers Tests
**Status:** Not Started
**Estimated Time:** 1 day

**Test Files:**
- `CronConfigurerTest.php` (~3 tests)
- `EmailConfigurerTest.php` (~4 tests)
- `ModeConfigurerTest.php` (~3 tests)

**Total Tests:** ~10 tests

**Mocking Strategy:** Mock ProcessRunner, verify command arguments

---

## Phase 6: Polish & Integration (Week 4-5)

**Goal:** Fill coverage gaps and test critical stages
**Test Count:** ~35 tests
**Coverage:** Selected stages + gaps (Tier 2-3)

### Stage 6.1: Critical Stage Tests
**Status:** Not Started
**Estimated Time:** 2 days

**Priority Stages to Test:**
1. `MagentoInstallationStageTest.php` (~8 tests)
   - Command argument building
   - Database config passing
   - Admin config passing
   - Error handling

2. `ThemeInstallationStageTest.php` (~7 tests)
   - Theme selection
   - Composer integration
   - Installation verification

3. `PermissionCheckStageTest.php` (~5 tests)
   - Directory permission checks
   - File permission checks
   - Warning messages

**Total Tests:** ~20 tests

### Stage 6.2: Coverage Gap Analysis
**Status:** Not Started
**Estimated Time:** 2 days

**Tasks:**
1. Run `vendor/bin/phpunit --coverage-html coverage/`
2. Identify classes below 85% coverage
3. Add tests to reach coverage targets
4. Focus on uncovered branches and edge cases

**Estimated Additional Tests:** ~15 tests

### Stage 6.3: CI/CD Integration
**Status:** Not Started
**Estimated Time:** 1 day

**Tasks:**
1. Add GitHub Actions workflow for tests
2. Configure coverage reporting
3. Set up coverage threshold enforcement (85%)
4. Block merges that reduce coverage

---

## Test Utilities & Infrastructure

### Required Test Utilities

**File:** `setup/tests/Util/TestDataBuilder.php`
```php
final class TestDataBuilder {
    public static function validDatabaseConfig(): DatabaseConfiguration
    public static function validAdminConfig(): AdminConfiguration
    public static function validInstallationContext(): InstallationContext
    public static function minimalInstallationContext(): InstallationContext
    // ... builders for all VOs
}
```

**File:** `setup/tests/Util/VoAssertions.php`
```php
trait VoAssertions {
    protected function assertSerializationPreservesSensitiveData($vo): void
    protected function assertRoundTripPreservesData($vo): void
    protected function assertSensitiveFieldsExcluded($vo): void
}
```

**File:** `setup/tests/Util/MockFactory.php`
```php
final class MockFactory {
    public static function mockStage(StageResult $result): InstallationStageInterface
    public static function mockProcessRunner(ProcessResult $result): ProcessRunner
    public static function mockValidator(bool $isValid): ValidatorInterface
}
```

**File:** `setup/tests/TestCase/AbstractVOTest.php`
```php
abstract class AbstractVOTest extends TestCase {
    use VoAssertions;

    abstract protected function createValidInstance(): object;
    abstract protected function getSensitiveFields(): array;

    // Common tests inherited by all VO tests
    public function test_it_serializes_without_sensitive_data(): void { /* ... */ }
    public function test_it_deserializes_from_array(): void { /* ... */ }
    public function test_round_trip_preserves_data(): void { /* ... */ }
}
```

**File:** `setup/tests/TestCase/FileSystemTestCase.php`
```php
abstract class FileSystemTestCase extends TestCase {
    protected $vfs;

    protected function setUp(): void {
        parent::setUp();
        $this->vfs = vfsStream::setup('root');
    }

    protected function getVirtualFilePath(string $filename): string {
        return vfsStream::url("root/{$filename}");
    }
}
```

---

## Test Execution & CI/CD

### Local Development

**Run all tests:**
```bash
cd setup
vendor/bin/phpunit
```

**Run specific test:**
```bash
vendor/bin/phpunit tests/unit/Model/InstallationContextTest.php
```

**Generate coverage report:**
```bash
vendor/bin/phpunit --coverage-html coverage/
open coverage/index.html
```

**Run with coverage threshold:**
```bash
vendor/bin/phpunit --coverage-text --coverage-clover coverage.xml
```

### CI/CD Pipeline (GitHub Actions)

**File:** `.github/workflows/tests.yml`
```yaml
name: Tests

on: [push, pull_request]

jobs:
  phpunit:
    runs-on: ubuntu-latest

    steps:
      - uses: actions/checkout@v3

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.2
          extensions: mbstring, pdo, pdo_mysql
          coverage: xdebug

      - name: Install dependencies
        run: |
          cd setup
          composer install --no-interaction --prefer-dist

      - name: Run tests with coverage
        run: |
          cd setup
          vendor/bin/phpunit --coverage-text --coverage-clover coverage.xml

      - name: Check coverage threshold (85%)
        run: |
          cd setup
          php check-coverage.php coverage.xml 85

      - name: Upload coverage to Codecov
        uses: codecov/codecov-action@v3
        with:
          file: setup/coverage.xml
          fail_ci_if_error: true
```

### Coverage Enforcement Script

**File:** `setup/check-coverage.php`
```php
<?php
$coverageFile = $argv[1] ?? 'coverage.xml';
$threshold = (int)($argv[2] ?? 85);

$xml = simplexml_load_file($coverageFile);
$metrics = $xml->project->metrics;
$coverage = ($metrics['coveredstatements'] / $metrics['statements']) * 100;

echo sprintf("Coverage: %.2f%%\n", $coverage);

if ($coverage < $threshold) {
    echo sprintf("FAIL: Coverage %.2f%% is below threshold %d%%\n", $coverage, $threshold);
    exit(1);
}

echo sprintf("PASS: Coverage %.2f%% meets threshold %d%%\n", $coverage, $threshold);
exit(0);
```

---

## Troubleshooting & Common Issues

### Issue: Tests are slow
**Solution:**
- Ensure using unit tests (no I/O, no real services)
- Mock external dependencies
- Use vfsStream instead of real filesystem
- Move slow tests to integration suite

### Issue: Tests are flaky
**Solution:**
- Remove sleep() calls
- Remove random values
- Use deterministic data
- Isolate test state (no shared fixtures)

### Issue: Coverage not improving
**Solution:**
- Run coverage report: `vendor/bin/phpunit --coverage-html coverage/`
- Check uncovered lines
- Add tests for branches and edge cases
- Test error conditions

### Issue: Can't mock dependency
**Solution:**
- Use dependency injection
- Extract interface if needed
- Test at higher level if truly untestable
- Consider integration test instead

---

## Success Metrics

### Phase Completion Criteria

**Phase 1 Complete:**
- ✅ 145 tests passing
- ✅ VOs: 95%+ coverage
- ✅ InstallationContext: 95%+ coverage
- ✅ Test infrastructure working

**Phase 2 Complete:**
- ✅ 180 cumulative tests passing
- ✅ ConfigFileManager: 95%+ coverage
- ✅ EnvConfigWriter: 95%+ coverage

**Phase 3 Complete:**
- ✅ 230 cumulative tests passing
- ✅ All validators: 85%+ coverage
- ✅ Edge cases covered

**Phase 4 Complete:**
- ✅ 260 cumulative tests passing
- ✅ StageNavigator: 95%+ coverage
- ✅ All state transitions tested

**Phase 5 Complete:**
- ✅ 310 cumulative tests passing
- ✅ ProcessRunner: 85%+ coverage
- ✅ All detectors: 70%+ coverage

**Phase 6 Complete:**
- ✅ 345 cumulative tests passing
- ✅ Overall coverage: 85%+
- ✅ CI enforcing coverage
- ✅ No skipped tests

### Overall Project Success

**Definition of Done:**
- ✅ 345+ unit tests passing
- ✅ 85%+ overall coverage
- ✅ 95%+ coverage on Tier 1 classes
- ✅ <5s execution time for all unit tests
- ✅ CI/CD pipeline enforcing coverage
- ✅ Test documentation complete
- ✅ Zero flaky tests
- ✅ Zero skipped/incomplete tests

---

## Timeline & Resource Allocation

### Single Developer Timeline
- **Week 1:** Phase 1 (Foundation) - 145 tests
- **Week 2:** Phase 2 + 3 (Persistence + Validation) - 85 tests
- **Week 3:** Phase 3 + 4 (Validation + Orchestration) - 80 tests
- **Week 4:** Phase 5 + 6 (Services + Polish) - 85 tests
- **Total:** 4-5 weeks, ~345 tests

### Parallel Development (2 Developers)
- **Week 1:**
  - Dev A: Phase 1 (VOs 1-7 + Infrastructure)
  - Dev B: Phase 1 (VOs 8-13 + InstallationContext)
- **Week 2:**
  - Dev A: Phase 2 (ConfigFileManager + EnvConfigWriter)
  - Dev B: Phase 3 (Validators 1-3)
- **Week 3:**
  - Dev A: Phase 4 (StageNavigator)
  - Dev B: Phase 3 + 5 (Validators 4-5 + ProcessRunner)
- **Week 4:**
  - Dev A: Phase 6 (Critical stages)
  - Dev B: Phase 5 + 6 (Detectors + Coverage gaps)
- **Total:** 3-4 weeks, ~345 tests

### Velocity Assumptions
- Simple tests (VOs, validators): 8-10 tests/hour
- Complex tests (orchestration, I/O): 4-6 tests/hour
- Average: 40-50 tests per developer per day

---

## Maintenance & Evolution

### Adding New Tests
1. Mirror source structure in tests directory
2. Use appropriate abstract base class
3. Follow naming convention: `{ClassName}Test.php`
4. Use snake_case for test methods
5. Mark class as `final`
6. Run coverage check before committing

### Refactoring Tests
- Keep tests independent (no shared state)
- Extract common setup to `setUp()` method
- Use data providers for parameterized tests
- Refactor when 3+ tests have same setup

### Deprecating Tests
- Never delete tests without reason
- Mark flaky tests as `@group flaky` (don't skip)
- Fix root cause of flakiness
- Document why test was removed if necessary

---

## Appendix: Class-to-Test Mapping

### Tier 1 (Critical Path) - 95%+ Coverage
- `InstallationContext` → `InstallationContextTest`
- `ConfigFileManager` → `ConfigFileManagerTest`
- `EnvConfigWriter` → `EnvConfigWriterTest`
- `StageNavigator` → `StageNavigatorTest`
- All 13 VOs → 13 VO test files

### Tier 2 (High Value) - 85%+ Coverage
- `DatabaseValidator` → `DatabaseValidatorTest`
- `UrlValidator` → `UrlValidatorTest`
- `EmailValidator` → `EmailValidatorTest`
- `SearchEngineValidator` → `SearchEngineValidatorTest`
- `PasswordValidator` → `PasswordValidatorTest`
- `ProcessRunner` → `ProcessRunnerTest`
- `MagentoInstallationStage` → `MagentoInstallationStageTest`

### Tier 3 (Supporting) - 70%+ Coverage
- 6 Detectors → 6 Detector test files
- 3 Command Configurers → 3 Configurer test files
- Selected stages

### Deferred (Integration Test Coverage)
- Config Collectors (thin Laravel Prompts wrappers)
- Most individual stages
- Theme installers (complex external dependencies)

---

**Document Version:** 1.0
**Last Updated:** 2025-12-14
**Status:** Ready for Implementation
