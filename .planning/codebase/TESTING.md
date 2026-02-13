# Testing Patterns

**Analysis Date:** 2026-02-10

## Test Framework

**Runner:**
- PHPUnit (configured in `composer.json` as dev dependency)
- Version: `4.* || 5.* || 9.*` (flexible version constraint)
- Config: Not detected in application root (no `phpunit.xml` or `phpunit.xml.dist`)

**Assertion Library:**
- PHPUnit's built-in assertions

**Run Commands:**
```bash
composer test:coverage    # Run tests with coverage (configured in composer.json)
```

**Coverage configuration:**
- Script configured: `@putenv XDEBUG_MODE=coverage` + `phpunit --color=always --coverage-text --configuration tests/travis/sqlite.phpunit.xml`
- References non-existent path: `tests/travis/sqlite.phpunit.xml` (not found in codebase)

## Test File Organization

**Location:**
- No tests directory detected in application code
- No test files found matching patterns: `*.test.php`, `*.spec.php`, `*Test.php`

**Naming:**
- Not applicable (no test files exist)

**Structure:**
- Not applicable (no test files exist)

## Test Structure

**Suite Organization:**
- No test suites detected

**Patterns:**
- No testing patterns implemented

## Mocking

**Framework:**
- VfsStream available (configured in `composer.json` dev dependencies)
  - Package: `mikey179/vfsstream` version `1.6.*`
  - Purpose: Virtual filesystem for testing file operations
  - Post-install hook patches VfsStream for PHP 7+ compatibility

**Patterns:**
- No mocking patterns observed (no tests exist)

**What to Mock:**
- Not defined

**What NOT to Mock:**
- Not defined

## Fixtures and Factories

**Test Data:**
- No fixture files or factories detected

**Location:**
- Not applicable

## Coverage

**Requirements:** 
- Coverage reporting configured in composer script
- Target coverage: Not enforced (no minimum threshold detected)

**View Coverage:**
```bash
composer test:coverage
```

**Coverage output:**
- Text format (via `--coverage-text` flag)

## Test Types

**Unit Tests:**
- None detected in application code

**Integration Tests:**
- None detected in application code

**E2E Tests:**
- Not used

**Current test coverage:**
- 0% (no tests exist)

## Common Patterns

**Async Testing:**
- Not applicable (CodeIgniter 3 is synchronous)

**Error Testing:**
- No patterns defined

## Testing Status

**Overall:**
- No application tests found
- Testing infrastructure partially configured (PHPUnit in composer.json) but not implemented
- Test configuration references non-existent paths (`tests/travis/sqlite.phpunit.xml`)

**Recommended testing approach for CodeIgniter 3:**

**Unit Testing Controllers:**
```php
<?php
class AuthTest extends TestCase
{
    public function setUp()
    {
        $this->CI = &get_instance();
        $this->CI->load->library('unit_test');
    }

    public function testLoginWithValidCredentials()
    {
        // Test login logic
    }
}
```

**Unit Testing Libraries:**
```php
<?php
class FungsiTest extends TestCase
{
    public function testUserLogin()
    {
        $fungsi = new Fungsi();
        // Mock session and database
        // Test user_login() method
    }
}
```

**Unit Testing Helpers:**
```php
<?php
class FungsiHelperTest extends TestCase
{
    public function testIndoCurrency()
    {
        $result = indo_currency(1000);
        $this->assertEquals('Rp 1.000,00', $result);
    }

    public function testTglIndo()
    {
        $result = tgl_indo('2024-03-15');
        $this->assertEquals('15 Maret 2024', $result);
    }
}
```

**Integration Testing (Database):**
```php
<?php
class UserModelTest extends TestCase
{
    public function setUp()
    {
        // Load test database
        $this->CI->load->database('test');
    }

    public function testGetByUsername()
    {
        // Test database queries
    }
}
```

**Key areas requiring tests:**
- Authentication logic (`application/controllers/Auth.php`)
- Password hashing/verification (currently using insecure SHA1)
- SQL injection prevention (raw queries in `application/libraries/Fungsi.php` and `application/helpers/fungsi_helper.php`)
- Session management (login/logout flows)
- Helper functions (formatting functions in `application/helpers/fungsi_helper.php`)
- Template rendering (`application/libraries/Template.php`)

---

*Testing analysis: 2026-02-10*
