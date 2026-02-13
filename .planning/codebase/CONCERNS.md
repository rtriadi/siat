# Codebase Concerns

**Analysis Date:** 2026-02-10

## Tech Debt

**Weak Password Hashing:**
- Issue: Using SHA1 for password hashing - cryptographically broken since 2005
- Files: `application/controllers/Auth.php:18`
- Impact: User passwords vulnerable to rainbow table attacks and collision attacks
- Fix approach: Migrate to PHP's `password_hash()` with bcrypt/argon2. Requires password reset for all users or one-time migration on login.

**SQL Injection Vulnerabilities:**
- Issue: Raw SQL queries with unescaped user input
- Files:
  - `application/helpers/fungsi_helper.php:25` - `check_status_lipa()` function
  - `application/helpers/fungsi_helper.php:27` - Direct variable interpolation in SQL
  - `application/libraries/Fungsi.php:15` - `user_login()` function
- Impact: Critical - attackers can read/modify/delete database contents, potentially gaining admin access
- Fix approach: Convert all raw queries to Query Builder or use parameter binding. Example: `$this->db->where('id_user', $id_user)->get('tbl_users')`

**Hardcoded Repetitive Navigation:**
- Issue: 23+ nearly identical Lipa report links generated via PHP loop instead of data-driven approach
- Files: `application/views/layout/nav.php:47-424`
- Impact: Unmaintainable - adding/removing/reordering reports requires editing 424-line view file
- Fix approach: Move menu structure to database or config array, render with single loop

**Empty Database Credentials:**
- Issue: Database connection configured but username/password/database name are empty strings
- Files: `application/config/database.php:79-81`
- Impact: Application cannot connect to database; likely relies on manual configuration per environment
- Fix approach: Use environment variables with `.env` file (not tracked in git) or document setup requirements

**Missing User Model:**
- Issue: Auth controller references `user_model` but `application/models/` directory is empty
- Files: `application/controllers/Auth.php:11`
- Impact: Login functionality broken - fatal error on form submission
- Fix approach: Create `User_model.php` with `get_by_username()` method

**Commented-Out Security Check:**
- Issue: `check_already_login()` helper function exists but is commented out in Auth constructor
- Files: `application/controllers/Auth.php:10`
- Impact: Users can access login page even when already authenticated (minor UX issue)
- Fix approach: Uncomment or remove depending on desired behavior

## Known Bugs

**Missing Model File:**
- Symptoms: Fatal error when accessing login page - "Unable to locate the model you have specified: user_model"
- Files: `application/controllers/Auth.php:11`
- Trigger: Navigate to `/auth/login`
- Workaround: None - application is non-functional without model

**Undefined Dashboard Route:**
- Symptoms: 404 error after successful login
- Files: `application/controllers/Auth.php:27` redirects to 'dashboard' controller
- Trigger: Submit valid login credentials
- Workaround: Create `Dashboard.php` controller or change redirect target

**Base URL Not Configured:**
- Symptoms: Asset loading may fail, links may break in production
- Files: `application/config/config.php:26` - `$config["base_url"] = "";`
- Trigger: Deploying to subdirectory or different domain
- Workaround: Set base URL manually in config or via environment detection

## Security Considerations

**CSRF Protection Disabled:**
- Risk: Cross-Site Request Forgery attacks possible - malicious sites can submit forms on behalf of authenticated users
- Files: `application/config/config.php:461` - `$config["csrf_protection"] = false;`
- Current mitigation: None
- Recommendations: Enable CSRF protection, add tokens to all forms

**XSS Filtering Disabled:**
- Risk: Cross-Site Scripting attacks via user input reflected in pages
- Files: `application/config/config.php:445` - `$config["global_xss_filtering"] = false;`
- Current mitigation: None (note: global XSS filtering is deprecated in CI3)
- Recommendations: Use per-input validation/escaping, output encoding in views (`htmlspecialchars` or `esc()`)

**Empty Encryption Key:**
- Risk: Session encryption, cookie encryption, and any encrypted data uses weak/no encryption
- Files: `application/config/config.php:330` - `$config["encryption_key"] = "";`
- Current mitigation: None
- Recommendations: Generate strong random key (32+ characters), store in environment variable

**Debug Mode Enabled:**
- Risk: Stack traces and database errors exposed to users in production
- Files: `application/config/constants.php:14` - `SHOW_DEBUG_BACKTRACE` set to TRUE
- Current mitigation: Environment detection in `index.php` (defaults to 'development')
- Recommendations: Ensure `ENVIRONMENT` set to 'production' on live server, disable debug backtrace in production

**Session Save Path Not Set:**
- Risk: Sessions stored in default system temp directory - may be readable by other users on shared hosting
- Files: `application/config/config.php:391` - `$config["sess_save_path"] = null;`
- Current mitigation: None
- Recommendations: Set dedicated session directory with proper permissions: `application/cache/sessions/`

**Cookie Security Flags Disabled:**
- Risk: Cookies transmitted over HTTP, accessible via JavaScript
- Files: `application/config/config.php:415-416` - `cookie_secure` and `cookie_httponly` both FALSE
- Current mitigation: None
- Recommendations: Enable `cookie_httponly` (prevents XSS cookie theft), enable `cookie_secure` if using HTTPS

**No Input Validation Layer:**
- Risk: Unvalidated user input reaches database and business logic
- Files: `application/controllers/Auth.php:16-18` - Direct `$this->input->post()` usage without validation
- Current mitigation: CodeIgniter's Input class provides basic XSS cleaning (when enabled)
- Recommendations: Add form validation rules using CI's Form Validation library

## Performance Bottlenecks

**Save Queries Enabled:**
- Problem: All SQL queries stored in memory for debugging
- Files: `application/config/database.php:95` - `'save_queries' => TRUE`
- Cause: Debug feature left enabled
- Improvement path: Disable in production to reduce memory usage on high-traffic sites

**Large Navigation File:**
- Problem: 424-line navigation view loaded on every page
- Files: `application/views/layout/nav.php`
- Cause: Hardcoded menu items instead of data-driven rendering
- Improvement path: Cache rendered navigation, or simplify menu structure

**No Query Caching:**
- Problem: Repeated identical queries not cached
- Files: `application/config/database.php:86` - `'cache_on' => FALSE`
- Cause: Default configuration
- Improvement path: Enable query caching for read-heavy operations, set cache directory

## Fragile Areas

**Authentication System:**
- Files: `application/controllers/Auth.php`, `application/helpers/fungsi_helper.php:3-19`, `application/libraries/Fungsi.php:12-17`
- Why fragile: Multiple SQL injection points, missing model dependency, weak password hashing, no rate limiting
- Safe modification: Do not modify existing code - rewrite entire auth system using modern practices
- Test coverage: None detected

**Helper Functions with Direct SQL:**
- Files: `application/helpers/fungsi_helper.php:21-30`
- Why fragile: SQL injection vulnerability, no error handling, assumes database connection exists
- Safe modification: Always use Query Builder, never pass user input directly
- Test coverage: None detected

## Scaling Limits

**File-Based Sessions:**
- Current capacity: Suitable for <1000 concurrent users
- Limit: File locking contention on high-traffic sites
- Scaling path: Switch to database or Redis session storage (`sess_driver = 'database'` or `'redis'`)

**No Caching Layer:**
- Current capacity: Direct database queries for all requests
- Limit: Database becomes bottleneck at ~100-500 requests/second
- Scaling path: Implement Redis/Memcached for query caching and session storage

## Dependencies at Risk

**PHP 5.3.7 Minimum Requirement:**
- Risk: CodeIgniter 3.1.13 supports PHP 5.3.7-8.x, but running PHP 8.3.6
- Impact: Potential compatibility issues with deprecated features (PHP 8+ stricter type checking)
- Migration plan: Test thoroughly on PHP 8.3; plan migration to CodeIgniter 4 (built for PHP 7.4+)

**CodeIgniter 3 End-of-Life:**
- Risk: CodeIgniter 3.1.13 (released 2021) in maintenance mode - security fixes only
- Impact: No new features, eventual security vulnerabilities
- Migration plan: Plan migration to CodeIgniter 4 or Laravel within 12-24 months

**AdminLTE Assets:**
- Risk: Large third-party admin template in `assets/` - version unknown, may be outdated
- Impact: Potential security vulnerabilities in jQuery, Bootstrap, or other bundled libraries
- Migration plan: Audit `assets/package-lock.json`, update dependencies, or switch to CDN

## Missing Critical Features

**No Authentication Middleware:**
- Problem: No centralized auth check - each controller must manually call `check_not_login()`
- Blocks: Cannot enforce authentication across entire application sections
- Fix approach: Implement CI hooks or base controller with auth check

**No Database Migration System:**
- Problem: Migration config exists but no migration files
- Blocks: Cannot version control database schema changes
- Fix approach: Enable migrations, create initial schema migration

**No Error Logging:**
- Problem: `application/logs/` directory empty (only index.html)
- Blocks: Cannot debug production issues or track errors
- Fix approach: Verify log threshold in config, ensure directory is writable

**No API Endpoints:**
- Problem: Traditional form-based application only
- Blocks: Cannot integrate with mobile apps or external systems
- Fix approach: Add REST API controllers with token-based authentication

## Test Coverage Gaps

**Zero Test Coverage:**
- What's not tested: Entire application
- Files: All files in `application/`
- Risk: Any change can break authentication, SQL queries, or navigation without detection
- Priority: High - start with Auth controller and helper functions (security-critical)

**No Automated Testing Infrastructure:**
- What's not tested: No test files, no PHPUnit config detected
- Files: N/A
- Risk: Cannot verify code quality or prevent regressions
- Priority: High - set up PHPUnit, write tests for SQL injection vulnerabilities

**No Input Validation Tests:**
- What's not tested: Form validation, XSS prevention, SQL injection prevention
- Files: `application/controllers/Auth.php`, `application/helpers/fungsi_helper.php`
- Risk: Security vulnerabilities remain undetected
- Priority: Critical - test all user input paths with malicious payloads

---

*Concerns audit: 2026-02-10*
