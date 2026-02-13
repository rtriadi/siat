# Technology Stack

**Analysis Date:** 2026-02-10

## Languages

**Primary:**
- PHP 8.3.6 - Server-side application logic (CodeIgniter framework)

**Secondary:**
- JavaScript - Frontend interactions and AdminLTE components
- HTML/CSS - View templates and styling
- SCSS - Stylesheet preprocessing (AdminLTE build system)

## Runtime

**Environment:**
- PHP 8.3.6 (NTS Visual C++ 2019 x64)
- Zend Engine v4.3.6 with Zend OPcache

**Package Manager:**
- Composer - PHP dependency management (root `composer.json`)
- npm - Frontend asset management (assets `package.json`)
- Lockfile: No `composer.lock` present (not committed)
- Lockfile: `assets/package-lock.json` present

## Frameworks

**Core:**
- CodeIgniter 3.1.13 - PHP MVC framework for backend
- AdminLTE 3.2.0 - Bootstrap 4 admin dashboard template

**Testing:**
- PHPUnit 4.* || 5.* || 9.* - PHP testing framework (dev dependency)

**Build/Dev:**
- Node-sass 7.0.1 - SCSS compilation
- Rollup 2.67.0 - JavaScript bundling
- PostCSS 8.4.6 - CSS processing
- Terser 5.10.0 - JavaScript minification
- Browser-sync 2.27.7 - Development server with live reload
- ESLint 8.8.0 - JavaScript linting
- Stylelint 13.13.1 - CSS/SCSS linting

## Key Dependencies

**Critical:**
- Bootstrap 4.6.1 - Frontend UI framework (AdminLTE dependency)
- jQuery 3.6.0 - DOM manipulation and AJAX (required by AdminLTE)
- Font Awesome 5.15.4 - Icon library
- DataTables.net 1.11.4 - Advanced table interactions
- Chart.js 2.9.4 - Data visualization
- Select2 4.0.13 - Enhanced select boxes
- SweetAlert2 11.4.0 - Customizable alerts/modals
- Moment.js 2.29.1 - Date/time manipulation

**Infrastructure:**
- mysqli - Database driver (configured in `application/config/database.php`)
- CodeIgniter Session library - Session management
- CodeIgniter Database library - Database abstraction

## Configuration

**Environment:**
- Environment mode: Defined via `$_SERVER['CI_ENV']` or defaults to 'development' (`index.php`)
- Timezone: Asia/Makassar (set in `application/config/config.php` and `.htaccess`)
- Database: Configured in `application/config/database.php` (hostname, username, password, database name - currently empty)
- Session: Auto-loaded via CodeIgniter session library
- Base URL: Empty string in `application/config/config.php` (must be configured)

**Build:**
- `.htaccess` - Apache mod_rewrite for clean URLs
- `assets/package.json` - npm scripts for CSS/JS compilation
- `assets/build/config/postcss.config.js` - PostCSS configuration
- `assets/build/config/rollup.config.js` - Rollup bundler configuration
- `.editorconfig` - Editor consistency rules

## Platform Requirements

**Development:**
- PHP >= 5.3.7 (minimum per CodeIgniter, actual runtime is PHP 8.3.6)
- Apache with mod_rewrite enabled
- MySQL/MariaDB (mysqli driver configured)
- Node.js and npm (for asset compilation)
- Composer (for PHP dependencies)

**Production:**
- Apache web server with mod_rewrite
- PHP 5.3.7+ (8.3.6 tested)
- MySQL/MariaDB database
- Pre-compiled assets (dist directory)

---

*Stack analysis: 2026-02-10*
