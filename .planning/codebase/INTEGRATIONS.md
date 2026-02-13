# External Integrations

**Analysis Date:** 2026-02-10

## APIs & External Services

**None detected** - No external API integrations found in codebase

## Data Storage

**Databases:**
- MySQL/MariaDB
  - Connection: Configured in `application/config/database.php`
  - Client: mysqli driver (CodeIgniter built-in)
  - Hostname: localhost (default, not configured)
  - Database name: Empty (must be configured)
  - Username/Password: Empty (must be configured)
  - Character set: utf8 (utf8_general_ci collation)

**File Storage:**
- Local filesystem only - No cloud storage integration detected

**Caching:**
- CodeIgniter query caching available but disabled (`cache_on` => FALSE in `application/config/database.php`)
- Memcached configuration file exists (`application/config/memcached.php`) but not auto-loaded
- Zend OPcache enabled at runtime level (PHP extension)

## Authentication & Identity

**Auth Provider:**
- Custom implementation in `application/controllers/Auth.php`
  - Implementation: Session-based authentication
  - Password hashing: SHA-1 (insecure, legacy approach)
  - User data: Stored in `tbl_users` database table
  - Session storage: CodeIgniter session library (auto-loaded)
  - Login check: `application/libraries/Fungsi.php` provides `user_login()` helper
  - User ID stored in session: `$this->session->userdata('id_user')`

## Monitoring & Observability

**Error Tracking:**
- None - No external error tracking service integrated

**Logs:**
- CodeIgniter built-in logging system
- Error reporting configured per environment in `index.php`
- Development mode: Full error display
- Production mode: Errors hidden, selective reporting

## CI/CD & Deployment

**Hosting:**
- Local development (XAMPP environment)
- No production hosting platform configured

**CI Pipeline:**
- None - No CI/CD integration detected

## Environment Configuration

**Required env vars:**
- `CI_ENV` - Application environment (development/testing/production), defaults to 'development'
- Database credentials must be set in `application/config/database.php`:
  - hostname
  - username
  - password
  - database

**Secrets location:**
- Database credentials: `application/config/database.php` (hardcoded, not using env vars)
- No `.env` file detected
- No environment variable usage for secrets

## Webhooks & Callbacks

**Incoming:**
- None detected

**Outgoing:**
- None detected

## Additional Libraries (AdminLTE)

**Frontend Plugins:**
- DataTables ecosystem - Advanced table features (sorting, searching, pagination, export)
- Chart.js - Data visualization
- FullCalendar 5.10.1 - Calendar/scheduling UI
- Summernote 0.8.20 - WYSIWYG editor
- Select2 - Enhanced dropdowns with search
- Daterangepicker 3.1.0 - Date range selection
- Dropzone 5.9.3 - File upload with drag-and-drop
- Toastr 2.1.4 - Toast notifications
- SweetAlert2 - Modal dialogs
- jQuery Validation 1.19.3 - Form validation
- Bootstrap Colorpicker 3.4.0 - Color picker widget
- Inputmask 5.0.7 - Input masking
- CodeMirror 5.65.1 - Code editor
- jsPDF/pdfmake 0.2.4 - PDF generation
- JSZip 3.7.1 - ZIP file creation

**Map Libraries:**
- jQuery Mapael 2.2.0 - Vector maps
- jqvmap 1.5.1 - Interactive vector maps

---

*Integration audit: 2026-02-10*
