# Phase 1: Project Setup & Configuration - Research

**Researched:** 2026-02-10
**Domain:** CodeIgniter 3 configuration, PHP security (password hashing), Composer dependency management
**Confidence:** HIGH

## Summary

Phase 1 focuses on foundational configuration and security fixes for a CodeIgniter 3.1.13 application running PHP 8.3.6. The research investigated current best practices for environment-based configuration, password hashing migration from SHA1 to bcrypt, Composer lock file management, and database setup patterns.

The standard approach for CodeIgniter 3 projects is to use environment-specific configuration folders (`application/config/{environment}/`) combined with the ENVIRONMENT constant set via server variables. Password security requires immediate migration from the deprecated and insecure SHA1 hashing to PHP's native `password_hash()` function with bcrypt (PASSWORD_DEFAULT or PASSWORD_BCRYPT). Composer lock files must be committed to git to ensure reproducible builds across environments.

Critical findings: SHA1 password hashing is cryptographically broken and must be replaced. CodeIgniter 3's environment system natively supports per-environment config files. Composer lock files guarantee dependency version consistency.

**Primary recommendation:** Use CodeIgniter 3's native environment folders for configuration, migrate all password hashing to `password_hash()` with bcrypt, and commit composer.lock for reproducible builds.

## Standard Stack

The established libraries/tools for this domain:

### Core
| Library | Version | Purpose | Why Standard |
|---------|---------|---------|--------------|
| CodeIgniter | 3.1.13 | PHP MVC framework | Project's existing framework (CI3 in maintenance mode, security fixes only) |
| PHP | 8.3.6 | Runtime | Current project runtime (modern version with password_hash() native support) |
| Composer | Latest stable | PHP dependency manager | Standard for PHP dependency management |
| mysqli | PHP extension | Database driver | CodeIgniter 3's recommended MySQL driver |

### Supporting
| Library | Version | Purpose | When to Use |
|---------|---------|---------|-------------|
| vlucas/phpdotenv | 5.x (optional) | .env file loader | If you need .env file support (CodeIgniter 3 doesn't natively support .env files) |
| password_compat | Not needed | Backport password_hash | Only needed for PHP < 5.5 (current project is PHP 8.3.6) |

### Alternatives Considered
| Instead of | Could Use | Tradeoff |
|------------|-----------|----------|
| CI3 environment folders | .env files via phpdotenv | phpdotenv adds dependency; CI3 native approach is simpler and has no dependencies |
| PASSWORD_DEFAULT (bcrypt) | PASSWORD_ARGON2ID | Argon2 is stronger but requires libargon2/libsodium; bcrypt is universally supported and sufficient for most use cases |
| Committing composer.lock | Not committing lock file | Not committing prevents reproducible builds and causes dependency version drift |

**Installation:**
```bash
# Composer is already installed (composer.json exists)
# Just need to generate lock file
composer install
```

## Architecture Patterns

### Recommended Project Structure
```
application/
├── config/
│   ├── config.php           # Base configuration
│   ├── database.php         # Base database config (empty/defaults)
│   ├── development/         # Development overrides
│   │   ├── config.php       # Dev-specific settings (base_url, debugging)
│   │   └── database.php     # Dev database credentials
│   └── production/          # Production overrides
│       ├── config.php       # Prod-specific settings (base_url, error handling)
│       └── database.php     # Prod database credentials
```

### Pattern 1: Environment-Specific Configuration
**What:** CodeIgniter 3 loads environment-specific config files from `application/config/{ENVIRONMENT}/` folders, overriding base config files.
**When to use:** Always - separates environment-specific values (database credentials, base URLs, debug settings) from base configuration.
**Example:**
```php
// Source: https://codeigniter.com/userguide3/libraries/config

// index.php - Set environment constant
define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'development');

// application/config/development/database.php - Dev-specific database config
<?php
$db['default'] = array(
    'hostname' => 'localhost',
    'username' => 'dev_user',
    'password' => 'dev_password',
    'database' => 'dev_database',
    'dbdriver' => 'mysqli',
    // ... other settings
);

// application/config/production/database.php - Prod-specific database config
<?php
$db['default'] = array(
    'hostname' => getenv('DB_HOST'),
    'username' => getenv('DB_USER'),
    'password' => getenv('DB_PASS'),
    'database' => getenv('DB_NAME'),
    'dbdriver' => 'mysqli',
    // ... other settings
);
```

### Pattern 2: Dynamic Base URL Configuration
**What:** Automatically detect base URL from HTTP request to avoid hardcoding per environment.
**When to use:** When deploying to multiple domains or subdomains without manual config changes.
**Example:**
```php
// Source: https://codeigniter.com/userguide3/installation/upgrade_303

// application/config/config.php
$allowed_domains = array('localhost', 'example.com', 'www.example.com');
$default_domain  = 'example.com';

if (in_array($_SERVER['HTTP_HOST'], $allowed_domains, TRUE)) {
    $domain = $_SERVER['HTTP_HOST'];
} else {
    $domain = $default_domain;
}

if (!empty($_SERVER['HTTPS'])) {
    $config['base_url'] = 'https://'.$domain;
} else {
    $config['base_url'] = 'http://'.$domain;
}
```

### Pattern 3: Secure Password Hashing with bcrypt
**What:** Use PHP's native `password_hash()` and `password_verify()` for password storage and verification.
**When to use:** Always - for all password storage (registration, password changes, authentication).
**Example:**
```php
// Source: https://www.php.net/manual/en/function.password-hash.php

// Registration / Password Change
$password = $_POST['password'];
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
// Store $hashed_password in database

// Login / Authentication
$password = $_POST['password'];
$stored_hash = $user_model->get_password_hash($username);

if (password_verify($password, $stored_hash)) {
    // Password is correct
    
    // Optional: Check if hash needs rehashing (algorithm/cost changed)
    if (password_needs_rehash($stored_hash, PASSWORD_DEFAULT)) {
        $new_hash = password_hash($password, PASSWORD_DEFAULT);
        $user_model->update_password_hash($username, $new_hash);
    }
} else {
    // Password is incorrect
}
```

### Pattern 4: Migrating from SHA1 to bcrypt
**What:** One-time migration strategy to upgrade existing SHA1 hashes to bcrypt without requiring all users to reset passwords.
**When to use:** When migrating from legacy hashing (SHA1, MD5) to modern hashing.
**Example:**
```php
// Migration strategy: Hash-on-login

// Login controller
$password = $this->input->post('password');
$user = $this->user_model->get_by_username($username);

// Check if old SHA1 hash
if (strlen($user->password) === 40) { // SHA1 produces 40-char hex string
    // Old SHA1 system
    if (sha1($password) === $user->password) {
        // Password correct - upgrade to bcrypt
        $new_hash = password_hash($password, PASSWORD_DEFAULT);
        $this->user_model->update_password($user->id, $new_hash);
        
        // Log user in
        $this->session->set_userdata('id_user', $user->id);
        redirect('dashboard');
    } else {
        // Password incorrect
        $this->session->set_flashdata('error', 'Invalid credentials');
    }
} else {
    // Already using bcrypt
    if (password_verify($password, $user->password)) {
        // Log user in
        $this->session->set_userdata('id_user', $user->id);
        redirect('dashboard');
    } else {
        // Password incorrect
        $this->session->set_flashdata('error', 'Invalid credentials');
    }
}
```

### Anti-Patterns to Avoid
- **Hardcoding credentials in config files tracked by git:** Store credentials in environment-specific config files and gitignore them, or use environment variables.
- **Using SHA1/MD5 for password hashing:** These are cryptographically broken. Use `password_hash()` with PASSWORD_DEFAULT (bcrypt).
- **Manually creating salt for password_hash():** The `salt` option is deprecated as of PHP 7.0 and ignored in PHP 8.0. Let `password_hash()` generate cryptographically secure salts automatically.
- **Not committing composer.lock:** Causes dependency version drift and non-reproducible builds.
- **Setting same bcrypt cost across all environments:** Development servers may need lower cost (faster) than production. Use environment-specific config.

## Don't Hand-Roll

Problems that look simple but have existing solutions:

| Problem | Don't Build | Use Instead | Why |
|---------|-------------|-------------|-----|
| Password hashing | Custom salt generation + SHA1/MD5 | `password_hash()` with PASSWORD_DEFAULT | Automatic secure salt generation, timing-attack safe, future-proof (algorithm can change), industry standard |
| Environment detection | Custom environment switching logic | CodeIgniter 3's ENVIRONMENT constant + config folders | Native CI3 feature, no dependencies, clean separation |
| .env file parsing | Custom file parser | vlucas/phpdotenv (if needed) | Handles edge cases (quotes, comments, multiline), widely used, tested |
| Password verification timing attacks | Custom verification logic | `password_verify()` | Built-in timing-attack resistance |
| Dependency version locking | Manual version tracking | composer.lock | Automatic, prevents version drift, reproducible builds |

**Key insight:** PHP 5.5+ provides battle-tested password hashing via `password_hash()` and `password_verify()`. These functions handle salt generation, timing attacks, and future algorithm upgrades automatically. Never implement custom password hashing.

## Common Pitfalls

### Pitfall 1: Empty Base URL Configuration
**What goes wrong:** Asset loading fails, redirects break, links point to wrong URLs in production.
**Why it happens:** CodeIgniter config defaults to empty string for base_url, developers forget to set it.
**How to avoid:** Use dynamic base URL detection (Pattern 2) or set in environment-specific config files.
**Warning signs:** Assets (CSS/JS) not loading, redirects going to `http://index.php/controller` instead of proper URLs.

### Pitfall 2: SHA1 Hash Length Assumption
**What goes wrong:** Migration script breaks because SHA1 hashes are assumed to be database-stored with salt or prefixes.
**Why it happens:** Developers don't verify exact hash format before writing migration.
**How to avoid:** Inspect actual database values first. SHA1 produces exactly 40 hexadecimal characters (160 bits). Check if raw SHA1 or has prefix/salt.
**Warning signs:** Migration fails on first user, hash length != 40 characters.

### Pitfall 3: Not Checking password_needs_rehash()
**What goes wrong:** Users remain on old bcrypt cost even after increasing security (cost parameter).
**Why it happens:** Developers upgrade cost in config but don't rehash existing passwords.
**How to avoid:** Always check `password_needs_rehash()` after successful login and rehash if needed.
**Warning signs:** Old user accounts take noticeably less time to verify than new accounts.

### Pitfall 4: Committing Environment-Specific Credentials
**What goes wrong:** Production database credentials exposed in git repository.
**Why it happens:** Developers put credentials in `application/config/database.php` (base file) instead of environment-specific folders.
**How to avoid:** Use `application/config/{environment}/database.php` files and gitignore them, or use environment variables via `getenv()`.
**Warning signs:** Database credentials visible in git history.

### Pitfall 5: Composer Install vs Update Confusion
**What goes wrong:** Running `composer update` when deploying causes unexpected dependency version changes in production.
**Why it happens:** Developers confuse `composer install` (uses lock file) with `composer update` (ignores lock file).
**How to avoid:** Development: `composer update` (updates lock file). Production/CI: `composer install` (uses lock file).
**Warning signs:** Production deployment breaks with "different behavior than local", dependencies are different versions.

### Pitfall 6: bcrypt 72-Character Truncation
**What goes wrong:** Passwords longer than 72 bytes are silently truncated, weakening security assumptions.
**Why it happens:** bcrypt algorithm has 72-byte limit, developers don't account for this.
**How to avoid:** Document password length limits or pre-hash long inputs with SHA-256 before bcrypt (advanced pattern, see PHP manual notes about "peppering").
**Warning signs:** Users report that changing characters beyond position 72 doesn't affect login.

### Pitfall 7: ENVIRONMENT Constant Not Set
**What goes wrong:** Application always runs in 'development' mode (default), even in production.
**Why it happens:** Developers don't set `CI_ENV` server variable in production web server config.
**How to avoid:** Set `SetEnv CI_ENV production` in Apache VirtualHost or `.htaccess`, or `fastcgi_param CI_ENV production` in nginx.
**Warning signs:** Debug errors showing in production, wrong database being used.

## Code Examples

Verified patterns from official sources:

### Basic Password Hashing (Registration)
```php
// Source: https://www.php.net/manual/en/function.password-hash.php

// Default cost is 12 as of PHP 8.4 (was 10 before 8.4)
$password = $_POST['password'];
$hashed = password_hash($password, PASSWORD_DEFAULT);

// Store $hashed in database (VARCHAR(255) recommended for future algorithm changes)
```

### Password Verification (Login)
```php
// Source: https://www.php.net/manual/en/function.password-verify.php

$password = $_POST['password'];
$stored_hash = $user->password; // Retrieved from database

if (password_verify($password, $stored_hash)) {
    // Password is correct - grant access
    echo 'Password is valid!';
} else {
    // Password is incorrect
    echo 'Invalid password.';
}
```

### Rehashing Passwords After Cost Increase
```php
// Source: https://www.php.net/manual/en/function.password-hash.php

// After successful login
if (password_verify($password, $stored_hash)) {
    // Check if hash needs rehashing (cost/algorithm changed)
    if (password_needs_rehash($stored_hash, PASSWORD_DEFAULT)) {
        $new_hash = password_hash($password, PASSWORD_DEFAULT);
        // Update database with $new_hash
    }
    // Proceed with login
}
```

### Finding Optimal bcrypt Cost
```php
// Source: https://www.php.net/manual/en/function.password-hash.php
// Run this once on target server to find appropriate cost

$timeTarget = 0.350; // 350 milliseconds (good for interactive logins)

$cost = 11;
do {
    $cost++;
    $start = microtime(true);
    password_hash("test", PASSWORD_BCRYPT, ["cost" => $cost]);
    $end = microtime(true);
} while (($end - $start) < $timeTarget);

echo "Appropriate Cost Found: " . ($cost - 1);
// Use this cost value in production config
```

### Environment-Specific Database Config
```php
// Source: https://codeigniter.com/userguide3/database/configuration

// application/config/development/database.php
<?php
$active_group = 'default';
$query_builder = TRUE;

$db['default'] = array(
    'dsn'       => '',
    'hostname'  => 'localhost',
    'username'  => 'root',
    'password'  => '',
    'database'  => 'ci3_starter_dev',
    'dbdriver'  => 'mysqli',
    'dbprefix'  => '',
    'pconnect'  => FALSE,
    'db_debug'  => TRUE,   // Show errors in development
    'cache_on'  => FALSE,
    'cachedir'  => '',
    'char_set'  => 'utf8mb4',
    'dbcollat'  => 'utf8mb4_unicode_ci',
    'swap_pre'  => '',
    'encrypt'   => FALSE,
    'compress'  => FALSE,
    'stricton'  => FALSE,
    'failover'  => array(),
    'save_queries' => TRUE // Debug queries in development
);
```

### Composer Lock File Generation
```bash
# Source: https://getcomposer.org/doc/01-basic-usage

# Generate composer.lock from composer.json
composer install

# Verify lock file is valid
composer validate

# After this, commit composer.lock to git
git add composer.lock
git commit -m "Add composer.lock for reproducible builds"
```

## State of the Art

| Old Approach | Current Approach | When Changed | Impact |
|--------------|------------------|--------------|--------|
| SHA1/MD5 password hashing | `password_hash()` with bcrypt/Argon2 | PHP 5.5.0 (2013) | SHA1 broken since 2005, MD5 since 1996. Modern hashing prevents rainbow table attacks and provides future-proof algorithm upgrades |
| Manual salt generation | Automatic salt via `password_hash()` | PHP 7.0 deprecated manual salt, PHP 8.0 ignores it | Eliminates developer errors in salt generation, uses CSPRNG automatically |
| bcrypt cost 10 | bcrypt cost 12 (default) | PHP 8.4.0 (2024) | Increased security against brute force as hardware improved. Developers should benchmark and adjust per environment |
| CodeIgniter 3 active development | CodeIgniter 3 maintenance mode | 2019 (CI4 released) | CI3 receives security fixes only. Plan migration to CI4 or Laravel within 12-24 months |

**Deprecated/outdated:**
- **SHA1 password hashing (`sha1()`):** Cryptographically broken since 2005, vulnerable to collision attacks. Replace with `password_hash()`.
- **MD5 password hashing (`md5()`):** Broken since 1996, trivially crackable. Replace with `password_hash()`.
- **Manual salt parameter in `password_hash()`:** Deprecated PHP 7.0, ignored PHP 8.0. Let function generate salts automatically.
- **CodeIgniter's `do_hash()` helper:** Deprecated since CI 3.0, removed in CI 3.1+. Use native PHP `hash()` or `password_hash()`.

## Open Questions

Things that couldn't be fully resolved:

1. **Migration strategy for users who never log in again**
   - What we know: Hash-on-login migration works for active users. Inactive users remain on SHA1 hashes.
   - What's unclear: Whether to force password reset for all users, or accept gradual migration with some users remaining on SHA1.
   - Recommendation: Accept gradual migration for low-security apps. Force password reset for high-security apps. Monitor SHA1 hash count in database over time.

2. **Optimal bcrypt cost for PHP 8.3.6 on target production server**
   - What we know: Default cost is 12 as of PHP 8.4. Recommended range is 250-500ms execution time for interactive logins.
   - What's unclear: Actual production server CPU performance (need to benchmark on target hardware).
   - Recommendation: Run benchmark script (see Code Examples) on production server. Start with cost 12, adjust based on results.

3. **Whether to use .env files via phpdotenv or native CI3 environment folders**
   - What we know: CI3 natively supports environment folders. phpdotenv adds dependency and complexity.
   - What's unclear: Team preference, existing deployment workflow, whether .env files are already in use.
   - Recommendation: Use native CI3 environment folders unless team already standardized on .env files across multiple projects.

## Sources

### Primary (HIGH confidence)
- `/websites/codeigniter_userguide3` (Context7) - Database configuration, environment-specific config, base URL setup
- https://www.php.net/manual/en/function.password-hash.php - Official PHP documentation for password_hash()
- https://www.php.net/manual/en/function.password-verify.php - Official PHP documentation for password_verify()
- `/websites/getcomposer_doc` (Context7) - Composer lock file management, install vs update

### Secondary (MEDIUM confidence)
- https://github.com/vlucas/phpdotenv - Popular .env file loader for PHP (optional, not required for CI3)
- Codebase analysis (STACK.md, ARCHITECTURE.md, CONCERNS.md) - Current project state and existing issues

### Tertiary (LOW confidence)
- None - all findings verified with official sources

## Metadata

**Confidence breakdown:**
- Standard stack: HIGH - Based on official CodeIgniter 3 and PHP documentation, current project analysis
- Architecture: HIGH - CodeIgniter 3 patterns verified from official userguide, password hashing from PHP manual
- Pitfalls: HIGH - Common issues documented in official docs, PHP manual warnings, and codebase analysis

**Research date:** 2026-02-10
**Valid until:** 2026-03-12 (30 days - stable technologies, unlikely to change)

**Notes:**
- PHP 8.4 increased default bcrypt cost from 10 to 12 (released 2024-11-21). This project runs PHP 8.3.6, so default is still cost 10 unless explicitly set.
- CodeIgniter 3.1.13 is in maintenance mode (security fixes only). Consider migration to CI4 or Laravel in future phases.
- All password hashing recommendations are based on PHP 8.3 capabilities and current cryptographic best practices as of 2026.
