# Codebase Structure

**Analysis Date:** 2026-02-10

## Directory Layout

```
ci3_starter_adminlte3/
├── application/           # Application code (MVC)
│   ├── cache/            # Output cache storage
│   ├── config/           # Configuration files
│   ├── controllers/      # HTTP request handlers
│   ├── core/             # Extended core classes
│   ├── helpers/          # Global utility functions
│   ├── hooks/            # Event hooks
│   ├── language/         # Localization files
│   ├── libraries/        # Custom libraries
│   ├── logs/             # Application logs
│   ├── models/           # Data access layer
│   ├── third_party/      # Third-party packages
│   └── views/            # Template files
│       ├── errors/       # Error page templates
│       └── layout/       # Layout templates
├── assets/               # AdminLTE theme and frontend resources
│   ├── build/            # Source files for building theme
│   ├── dist/             # Compiled CSS, JS, images
│   ├── docs/             # AdminLTE documentation
│   ├── pages/            # AdminLTE example pages
│   └── plugins/          # Frontend plugins (Bootstrap, DataTables, etc.)
├── system/               # CodeIgniter 3 framework core
├── index.php             # Front controller
├── .htaccess             # Apache URL rewriting
└── composer.json         # Composer configuration
```

## Directory Purposes

**application/**
- Purpose: All application-specific code
- Contains: MVC components, configuration, custom extensions
- Key files: Controllers, models, views, config files

**application/controllers/**
- Purpose: HTTP request handlers
- Contains: Controller classes extending `CI_Controller`
- Key files: `Auth.php` (authentication), `Welcome.php` (default homepage)
- Naming: PascalCase files matching class names

**application/models/**
- Purpose: Data access and business logic
- Contains: Model classes (currently sparse)
- Key files: Currently minimal - referenced `user_model` in Auth controller
- Naming: snake_case with `_model` suffix

**application/views/**
- Purpose: HTML templates and presentation logic
- Contains: PHP template files, layout wrappers
- Key files: `login.php`, `welcome_message.php`, `layout/template.php`, `layout/nav.php`
- Naming: snake_case `.php` files

**application/views/layout/**
- Purpose: Master templates and reusable layout components
- Contains: Main template wrapper, navigation partials
- Key files: `template.php` (master layout), `nav.php` (navigation)

**application/views/errors/**
- Purpose: Error page templates
- Contains: HTML and CLI error templates for different error types
- Subdirectories: `html/` (web errors), `cli/` (command-line errors)

**application/config/**
- Purpose: Application configuration
- Contains: Database, routing, autoloading, constants
- Key files: `config.php`, `database.php`, `routes.php`, `autoload.php`

**application/libraries/**
- Purpose: Custom reusable components
- Contains: Application-specific libraries
- Key files: `Template.php` (view rendering), `Fungsi.php` (user utilities)
- Naming: PascalCase files matching class names

**application/helpers/**
- Purpose: Global utility functions
- Contains: Procedural helper functions
- Key files: `fungsi_helper.php` (auth, date, currency helpers)
- Naming: snake_case with `_helper` suffix

**application/core/**
- Purpose: Extended CodeIgniter core classes
- Contains: Overrides of framework core (MY_Controller, MY_Model, etc.)
- Key files: Currently empty (no extensions)

**application/cache/**
- Purpose: Output cache storage
- Contains: Cached page outputs
- Generated: Yes
- Committed: No (ignored via `.gitignore`)

**application/logs/**
- Purpose: Application log files
- Contains: Error logs, debug logs
- Generated: Yes
- Committed: No (ignored via `.gitignore`)

**application/third_party/**
- Purpose: Third-party PHP packages
- Contains: External libraries not managed by Composer
- Key files: Currently empty

**assets/**
- Purpose: Frontend resources (AdminLTE theme)
- Contains: CSS, JavaScript, images, plugins
- Subdirectories: `dist/` (compiled), `build/` (source), `plugins/` (libraries)

**assets/dist/**
- Purpose: Production-ready frontend assets
- Contains: Minified CSS/JS, optimized images
- Subdirectories: `css/`, `js/`, `img/`

**assets/plugins/**
- Purpose: Frontend libraries and plugins
- Contains: Bootstrap, jQuery, DataTables, Font Awesome, Chart.js, etc.
- Usage: Loaded via `<link>` and `<script>` in layout templates

**assets/build/**
- Purpose: Source files for AdminLTE theme
- Contains: SCSS, uncompiled JavaScript
- Subdirectories: `scss/`, `js/`, `config/`

**system/**
- Purpose: CodeIgniter 3 framework core
- Contains: Framework libraries, database drivers, helpers
- Generated: No (part of framework distribution)
- Committed: Yes (framework files)

## Key File Locations

**Entry Points:**
- `index.php`: Front controller and application bootstrap

**Configuration:**
- `application/config/config.php`: Main application settings
- `application/config/database.php`: Database connection settings
- `application/config/routes.php`: URL routing rules
- `application/config/autoload.php`: Auto-loaded libraries, helpers, models
- `.htaccess`: Apache rewrite rules for clean URLs

**Core Logic:**
- `application/controllers/Auth.php`: Authentication logic
- `application/controllers/Welcome.php`: Homepage controller
- `application/libraries/Template.php`: View rendering system
- `application/libraries/Fungsi.php`: User session utilities
- `application/helpers/fungsi_helper.php`: Global utility functions

**Testing:**
- Not currently implemented - no test directory found

**Assets:**
- `assets/dist/css/adminlte.min.css`: Main theme stylesheet
- `assets/dist/js/adminlte.min.js`: Main theme JavaScript
- `assets/plugins/`: Third-party frontend libraries

## Naming Conventions

**Files:**
- Controllers: PascalCase (e.g., `Auth.php`, `Welcome.php`)
- Models: snake_case with `_model` suffix (e.g., `user_model.php`)
- Views: snake_case (e.g., `login.php`, `welcome_message.php`)
- Libraries: PascalCase (e.g., `Template.php`, `Fungsi.php`)
- Helpers: snake_case with `_helper` suffix (e.g., `fungsi_helper.php`)
- Config: snake_case (e.g., `database.php`, `routes.php`)

**Directories:**
- All lowercase with underscores (e.g., `third_party`, `views`)

**Classes:**
- PascalCase matching filename (e.g., `class Auth`, `class Template`)

**Methods:**
- snake_case for public methods (e.g., `user_login()`, `check_not_login()`)

**Variables:**
- snake_case (e.g., `$user_session`, `$id_user`)

## Where to Add New Code

**New Feature (e.g., User Management):**
- Primary code: `application/controllers/Users.php`
- Model: `application/models/user_model.php` or `users_model.php`
- Views: `application/views/users/` directory
- Tests: Not currently set up

**New Component/Module:**
- Implementation: `application/controllers/{ModuleName}.php`
- Supporting views: `application/views/{module_name}/`
- Database logic: `application/models/{module_name}_model.php`

**Utilities:**
- Shared helpers: `application/helpers/{name}_helper.php`
- Shared libraries: `application/libraries/{Name}.php`

**Authentication/Authorization:**
- Extend: `application/helpers/fungsi_helper.php`
- Or create: `application/libraries/Auth.php` for complex auth logic

**API Endpoints:**
- Create controller: `application/controllers/Api.php`
- Return JSON: Use `$this->output->set_content_type('application/json')`

**Admin Panel Features:**
- Controllers: `application/controllers/{Feature}.php`
- Views: `application/views/{feature}/` with layout template
- Load via: `$this->template->load('layout/template', 'view', $data)`

**Database Migrations:**
- Not currently implemented - CodeIgniter 3 migration support available but not configured
- Would go in: `application/migrations/` (create directory)

**Static Pages:**
- Simple: Add method to existing controller
- Complex: Create new controller in `application/controllers/`
- View: `application/views/pages/{page_name}.php`

## Special Directories

**application/cache/**
- Purpose: Stores cached page output
- Generated: Yes (by CodeIgniter output caching)
- Committed: No (excluded in `.gitignore`)

**application/logs/**
- Purpose: Application error and debug logs
- Generated: Yes (automatic)
- Committed: No (excluded in `.gitignore`)

**assets/dist/**
- Purpose: Compiled frontend assets
- Generated: Yes (from `assets/build/` sources)
- Committed: Yes (production assets)

**assets/build/**
- Purpose: Source files for theme customization
- Generated: No (maintained manually)
- Committed: Yes (for rebuilding assets)

**system/**
- Purpose: CodeIgniter framework core
- Generated: No (distributed with framework)
- Committed: Yes (framework dependency)
- Modify: Never modify directly - use `application/core/` for extensions

---

*Structure analysis: 2026-02-10*
