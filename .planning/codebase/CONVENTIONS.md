# Coding Conventions

**Analysis Date:** 2026-02-10

## Naming Patterns

**Files:**
- Controllers: PascalCase (e.g., `Auth.php`, `Welcome.php`)
- Libraries: PascalCase (e.g., `Fungsi.php`, `Template.php`)
- Helpers: snake_case with `_helper` suffix (e.g., `fungsi_helper.php`)
- Views: snake_case (e.g., `welcome_message.php`, `login.php`)
- Config files: snake_case (e.g., `autoload.php`, `database.php`)

**Functions:**
- Helpers: snake_case (e.g., `check_already_login()`, `indo_currency()`, `tgl_indo()`)
- Controller methods: snake_case (e.g., `public function login()`)
- Library methods: snake_case (e.g., `user_login()`)

**Variables:**
- snake_case throughout codebase (e.g., `$user_session`, `$user_data`, `$id_user`)
- Arrays use snake_case (e.g., `$template_data`, `$view_data`)

**Classes:**
- PascalCase without namespace
- Controllers extend `CI_Controller`
- Libraries are standalone classes

## Code Style

**Formatting:**
- Tool: EditorConfig configured
- Indent style: Tabs (as per `.editorconfig`)
- End of line: LF (Unix-style)
- Charset: UTF-8
- Insert final newline: true
- Mixed indentation observed: Some files use tabs, some use spaces (inconsistent)

**Linting:**
- PHP: No linting configuration detected in application code
- JavaScript (AdminLTE assets): ESLint configured (`.eslintrc.json`)
  - Extends: xo, compat, import, unicorn plugins
  - Indent: 2 spaces
  - Semi: never (no semicolons)
  - Comma dangle: never
  - Object curly spacing: always
- CSS: Stylelint configured (`.stylelintrc`)
  - Extends: stylelint-config-twbs-bootstrap/scss

**Indentation inconsistency:**
- `application/controllers/Welcome.php`: Uses tabs
- `application/controllers/Auth.php`: Uses spaces (4 spaces)
- `application/libraries/Fungsi.php`: Uses tabs
- `application/libraries/Template.php`: Uses spaces

**Line endings:**
- Mixed: Some files use CRLF (`\r\n`), some use LF (`\n`)
- Auth.php and Template.php: CRLF
- Welcome.php and Fungsi.php: LF

## Import Organization

**CodeIgniter auto-loading:**
- Libraries: `template`, `fungsi` (configured in `application/config/autoload.php`)
- Helpers: `url`, `fungsi` (configured in `application/config/autoload.php`)

**Load patterns:**
- Models loaded in constructors: `$this->load->model('user_model')`
- Views loaded in controller methods: `$this->load->view('login')`
- Template loading via custom Template library: `$this->template->load()`

**No namespaces or modern imports:**
- CodeIgniter 3 does not use PHP namespaces
- Classes loaded via autoload config or `$this->load` methods

## Error Handling

**Patterns:**
- Security check at file top: `defined('BASEPATH') OR exit('No direct script access allowed')`
  - Variations observed: `OR exit` (uppercase), `or exit` (lowercase), with quotes or parentheses
- Flash messages for user-facing errors: `$this->session->set_flashdata('error', 'Message')`
- No try-catch blocks observed in sample code
- No custom error handling classes detected
- Direct database queries without error handling: `$this->db->query("SELECT * FROM tbl_users WHERE id_user = '$id_user'")->row()`

**SQL Injection vulnerability:**
- Raw SQL queries with string interpolation observed in `application/libraries/Fungsi.php`:
  - `"SELECT * FROM tbl_users WHERE id_user = '$id_user'"`
- Similar pattern in `application/helpers/fungsi_helper.php`:
  - `"SELECT * FROM tbl_laporan WHERE bulan = $bulan AND tahun = $tahun"`
  - `"SELECT * FROM tbl_laporan WHERE nama_laporan = '$nama_laporan' AND bulan = $bulan AND tahun = $tahun"`

**Authentication pattern:**
- Password hashing: SHA1 (insecure legacy method)
  - `$password = sha1($this->input->post('password'))` in `application/controllers/Auth.php`
- Session-based auth: `$this->session->set_userdata('id_user', $user['id_user'])`
- Auth guards via helper functions: `check_already_login()`, `check_not_login()`

## Logging

**Framework:** CodeIgniter's built-in logging (no custom logging library detected)

**Patterns:**
- No explicit log statements observed in application code
- Commented-out code used instead of logging: `// check_already_login();` in Auth controller
- Comments in Indonesian language throughout codebase for inline documentation

## Comments

**When to Comment:**
- Security warnings at file top (BASEPATH check)
- Inline comments for clarification (in Indonesian): `// Jika username ditemukan, cek password`
- TODO comments in placeholder code: `// Ganti 'dashboard' dengan halaman setelah login berhasil`
- Commented-out code left in files (not removed): `// $this->session->unset_userdata('id_user');`

**PHPDoc/DocBlocks:**
- Minimal usage observed
- Only one docblock found in sample files: `application/controllers/Welcome.php` has detailed docblock for `index()` method
- Most methods lack docblocks (Auth controller, libraries, helpers)

**Comment language:**
- Mixed English and Indonesian
- Config files: English (CodeIgniter defaults)
- Application code: Predominantly Indonesian

## Function Design

**Size:** 
- Small to medium functions (5-30 lines typical)
- Largest observed: `bulanIndo()` with 30 lines of if-else chain
- Helper functions generally single-purpose

**Parameters:**
- 0-3 parameters typical
- No type hints observed (PHP 5.3.7+ compatibility, pre-strict typing)
- No default parameter values observed

**Return Values:**
- Direct returns without type declarations
- Mixed return types: objects (`->row()`), arrays, strings, void (redirect functions)
- Helper functions return formatted data: `indo_currency()` returns string, `tgl_indo()` returns formatted date string

## Module Design

**Exports:**
- No explicit exports (not applicable to PHP/CodeIgniter 3)
- Classes instantiated by framework or autoloader

**Barrel Files:**
- Not applicable (PHP/CodeIgniter 3 pattern)
- Functionality organized via CodeIgniter's autoload config instead

**Architecture pattern:**
- MVC (Model-View-Controller) via CodeIgniter 3 framework
- Controllers: Extend `CI_Controller`, handle HTTP requests
- Libraries: Custom classes for shared logic (Template, Fungsi)
- Helpers: Standalone functions for utilities (formatting, auth guards)
- Views: PHP templates with embedded short tags (`<?=`)

**Database access:**
- Direct database queries in libraries/helpers (not following repository pattern)
- Models referenced but not examined in this analysis

**Template system:**
- Custom Template library for layout management
- Views loaded into template containers: `$this->template->load($template, $view, $view_data)`

---

*Convention analysis: 2026-02-10*
