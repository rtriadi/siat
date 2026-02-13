# Architecture

**Analysis Date:** 2026-02-10

## Pattern Overview

**Overall:** Model-View-Controller (MVC)

**Key Characteristics:**
- CodeIgniter 3 framework-based architecture
- Traditional server-rendered PHP application
- Separation of concerns via MVC layers
- Template-based view rendering with AdminLTE3 theme
- Session-based authentication

## Layers

**Controllers:**
- Purpose: Handle HTTP requests and orchestrate application logic
- Location: `application/controllers/`
- Contains: Request handlers, input validation, response generation
- Depends on: Models, Libraries, Helpers, Views
- Used by: HTTP routing layer (via `index.php`)
- Examples: `application/controllers/Auth.php`, `application/controllers/Welcome.php`

**Models:**
- Purpose: Data access and business logic
- Location: `application/models/`
- Contains: Database queries, data validation, business rules
- Depends on: Database layer (CodeIgniter Query Builder)
- Used by: Controllers
- Note: Currently minimal model layer (only referenced in Auth controller)

**Views:**
- Purpose: Presentation layer and HTML generation
- Location: `application/views/`
- Contains: PHP templates, HTML markup, UI components
- Depends on: Layout templates, CSS/JS assets
- Used by: Controllers via Template library
- Structure: Layout-based rendering with master template

**Libraries (Custom):**
- Purpose: Reusable application components and utilities
- Location: `application/libraries/`
- Contains: Template rendering (`Template.php`), user utilities (`Fungsi.php`)
- Depends on: CodeIgniter core
- Used by: Controllers (auto-loaded)

**Helpers:**
- Purpose: Global utility functions
- Location: `application/helpers/`
- Contains: Authentication checks, date formatting, currency formatting
- Depends on: CodeIgniter instance
- Used by: Controllers, Views, Libraries
- Example: `application/helpers/fungsi_helper.php`

**System (Framework Core):**
- Purpose: CodeIgniter 3 framework core
- Location: `system/`
- Contains: Core libraries, database drivers, routing, security
- Depends on: PHP runtime
- Used by: All application layers

**Assets:**
- Purpose: Frontend resources and AdminLTE theme
- Location: `assets/`
- Contains: CSS, JavaScript, images, plugins (Bootstrap, jQuery, DataTables)
- Depends on: None (static resources)
- Used by: Views via `<link>` and `<script>` tags

## Data Flow

**Request Processing:**

1. HTTP request hits `index.php` (front controller)
2. CodeIgniter routing maps URL to Controller method
3. Controller loads required Models, Libraries, Helpers
4. Controller processes business logic via Models/Libraries
5. Controller passes data to View via Template library
6. Template library wraps content view in layout template
7. HTML response rendered and returned to client

**State Management:**
- Session data stored via CodeIgniter session library
- User authentication state in `$_SESSION['id_user']`
- Flash messages for temporary feedback (`set_flashdata`)

## Key Abstractions

**Template System:**
- Purpose: Layout-based view composition
- Examples: `application/libraries/Template.php`
- Pattern: Wrapper pattern - wraps content views in master layout
- Usage: `$this->template->load('layout/template', 'view_name', $data)`

**Fungsi Library:**
- Purpose: User session management and utilities
- Examples: `application/libraries/Fungsi.php`
- Pattern: Utility class accessing CodeIgniter instance
- Usage: `$this->fungsi->user_login()` returns current user data

**Helper Functions:**
- Purpose: Stateless utility functions
- Examples: `check_not_login()`, `indo_currency()`, `tgl_indo()`
- Pattern: Procedural helper functions using CodeIgniter super-object
- Usage: Called directly from any layer

## Entry Points

**Front Controller:**
- Location: `index.php`
- Triggers: All HTTP requests (via `.htaccess` rewrite)
- Responsibilities: Bootstrap framework, define environment, route requests

**Default Route:**
- Location: Defined in `application/config/routes.php`
- Default controller: `Welcome`
- Pattern: `$route['default_controller'] = 'welcome'`

**Authentication Entry:**
- Location: `application/controllers/Auth.php`
- Methods: `login()`, `logout()`
- Responsibilities: User authentication, session management

## Error Handling

**Strategy:** Framework-level error handling with custom error views

**Patterns:**
- HTTP errors: Custom error views in `application/views/errors/html/`
- CLI errors: Separate views in `application/views/errors/cli/`
- Application errors: Flash messages via `$this->session->set_flashdata()`
- Database errors: CodeIgniter database error handling (configurable)

## Cross-Cutting Concerns

**Logging:** CodeIgniter logging system to `application/logs/`

**Validation:** Controller-level input validation using CodeIgniter Form Validation library

**Authentication:** Session-based with helper functions (`check_not_login()`, `check_already_login()`)

**Security:** 
- CSRF protection (configurable in CodeIgniter)
- XSS filtering via CodeIgniter Input class
- SQL injection prevention via Query Builder/Active Record
- Password hashing using `sha1()` (weak - should be upgraded)

**Caching:** 
- Page cache: `application/cache/` (CodeIgniter output caching)
- Not actively used in current implementation

---

*Architecture analysis: 2026-02-10*
