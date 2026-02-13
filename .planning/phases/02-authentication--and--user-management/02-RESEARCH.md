# Phase 2: Authentication & User Management - Research

**Researched:** 2026-02-11
**Domain:** CodeIgniter 3 session-based auth + Excel import (PhpSpreadsheet)
**Confidence:** MEDIUM

## Summary

Scope covered: CI3 session-based authentication (admin + pegawai), role checks, password change flow (default NIP), and Excel import/template download for employee data. Sources include CodeIgniter 3 user guide (sessions, upload, form validation, download helper) and PhpSpreadsheet docs for XLSX read/write.

Standard approach for CI3: keep authentication in a dedicated `Auth` controller, use `User_model` for DB access, store auth state in CI session, and enforce login/role guards via helper functions or base controller checks. Excel import should use CodeIgniter Upload library for upload handling and PhpSpreadsheet for parsing; template download should use CI Download helper `force_download`.

**Primary recommendation:** Implement auth and employee import using CI3’s Session + Form Validation + Upload libraries and PhpSpreadsheet; store a `must_change_password` flag for default NIP passwords; enforce role guard on admin-only routes.

## Standard Stack

The established libraries/tools for this domain:

### Core
| Library | Version | Purpose | Why Standard |
|---------|---------|---------|--------------|
| CodeIgniter 3.1.x | 3.1.13 (project) | MVC framework, session auth, uploads, validation | Official framework; built-in session/upload/validation libraries |
| PhpSpreadsheet | latest stable | Read/write Excel (XLSX) for import/template | De-facto PHP Excel library with maintained API |
| PHP password_* | PHP 8.3 runtime | password_hash/password_verify | Standard hashing API; already adopted in Phase 1 |

### Supporting
| Library | Version | Purpose | When to Use |
|---------|---------|---------|-------------|
| CI Download Helper | CI3 | Force download Excel template | Admin template download (AUTH-06) |
| CI Upload Library | CI3 | Validate and store uploaded Excel files | Admin import (AUTH-02) |
| CI Form Validation | CI3 | Validate login / change password inputs | Login, change-password forms |

### Alternatives Considered
| Instead of | Could Use | Tradeoff |
|------------|-----------|----------|
| PhpSpreadsheet | Spout | Spout is faster for very large files but less feature-rich; PhpSpreadsheet standard in PHP ecosystem |

**Installation (if not already present):**
```bash
composer require phpoffice/phpspreadsheet
```

## Architecture Patterns

### Recommended Project Structure
```
application/
├── controllers/
│   ├── Auth.php               # login/logout, password change
│   └── Pegawai.php            # admin-only import/template actions
├── models/
│   └── User_model.php          # user queries, password updates, bulk import helpers
├── helpers/
│   └── fungsi_helper.php       # check_not_login, role guards
├── libraries/
│   └── Fungsi.php              # user session utilities
└── views/
    ├── login.php
    └── user/                   # change password, import UI
```

### Pattern 1: Session-based auth (CI3)
**What:** Store authenticated user ID and role in CI session, read via helper guard.
**When to use:** All authenticated routes; admin-only actions check role.
**Example:**
```php
// Source: https://codeigniter.com/userguide3/libraries/sessions.html
$this->load->library('session');
$this->session->set_userdata(['id_user' => $userId, 'level' => $roleId]);

$id = $this->session->userdata('id_user');
```

### Pattern 2: Form validation for login / password change
**What:** Use CI Form Validation to ensure required fields and password rules.
**When to use:** Login form, change password form, import confirmation fields.
**Example:**
```php
// Source: https://codeigniter.com/userguide3/libraries/form_validation.html
$this->load->library('form_validation');
$this->form_validation->set_rules('password', 'Password', 'required|min_length[8]');
if ($this->form_validation->run() == FALSE) {
    // show form errors
}
```

### Pattern 3: Upload + parse Excel file
**What:** Use CI Upload library to store XLSX, then PhpSpreadsheet to read rows.
**When to use:** Admin import of pegawai data (NIP, Nama, Unit).
**Example:**
```php
// Source: https://codeigniter.com/userguide3/libraries/file_uploading.html
$config['upload_path'] = './uploads/';
$config['allowed_types'] = 'xlsx|xls';
$this->load->library('upload', $config);
if ($this->upload->do_upload('userfile')) {
    $data = $this->upload->data();
}
```
```php
// Source: https://github.com/phpoffice/phpspreadsheet/blob/master/docs/topics/reading-and-writing-to-file.md
$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
$spreadsheet = $reader->load("import.xlsx");
```

### Pattern 4: Template download
**What:** Use CI Download helper to serve template file.
**When to use:** Admin downloads Excel template (AUTH-06).
**Example:**
```php
// Source: https://codeigniter.com/userguide3/helpers/download_helper.html
force_download('/path/to/template.xlsx', NULL);
```

### Anti-Patterns to Avoid
- **Storing plain NIP passwords:** Must store bcrypt hash only; never store raw passwords.
- **Role checks in views only:** Enforce role guard in controller or helper before action.
- **Parsing Excel without upload validation:** Always validate file type and size via CI Upload library.

## Don't Hand-Roll

Problems that look simple but have existing solutions:

| Problem | Don't Build | Use Instead | Why |
|---------|-------------|-------------|-----|
| Excel parsing | Custom XLSX reader | PhpSpreadsheet | XLSX is complex; PhpSpreadsheet handles formats safely |
| File upload validation | Manual $_FILES handling | CI Upload library | Built-in validation, error handling, metadata |
| Session management | Custom cookies/$_SESSION | CI Session library | Built-in security, regen, storage drivers |
| File download | Manual headers | CI Download helper | Correct headers, consistent behavior |

**Key insight:** CI3 provides stable primitives (session/validation/upload); focus on business logic instead of rewriting these layers.

## Common Pitfalls

### Pitfall 1: Default NIP password detection
**What goes wrong:** Cannot detect “default” password once hashed; reminder never shown or shown always.
**Why it happens:** Hashing is one-way; comparing to NIP string is impossible.
**How to avoid:** Add a `must_change_password` (boolean) or `is_default_password` flag set on account creation; clear after successful change.
**Warning signs:** Reminder logic based on password string or hash comparison.

### Pitfall 2: Duplicate NIP/username in import
**What goes wrong:** Import fails mid-way or creates partial records.
**Why it happens:** Unique constraints on `user.username` and `user.nip`.
**How to avoid:** Pre-validate import rows and wrap insert in transaction; report duplicates before commit.
**Warning signs:** DB error “Duplicate entry” during import.

### Pitfall 3: Excel row parsing issues (headers/empty rows)
**What goes wrong:** Header row inserted as data; empty trailing rows create blank users.
**Why it happens:** No explicit header skipping or row validation.
**How to avoid:** Skip first row explicitly; validate required columns (NIP, Nama, Unit) per row.
**Warning signs:** Users created with NIP=“NIP” or empty fields.

### Pitfall 4: Uploading wrong file types
**What goes wrong:** CSV or non-Excel files accepted, parser throws errors.
**Why it happens:** Missing `allowed_types` or too broad rules.
**How to avoid:** Restrict to `xlsx|xls` and enforce size limits.
**Warning signs:** PhpSpreadsheet load exceptions on upload.

### Pitfall 5: Role guard missing on admin endpoints
**What goes wrong:** Pegawai can access import/template download.
**Why it happens:** Only login guard is applied.
**How to avoid:** Add role check (Admin) to import and template routes in controller or helper.
**Warning signs:** Pegawai session can access admin pages.

## Code Examples

Verified patterns from official sources:

### Session set/get
```php
// Source: https://codeigniter.com/userguide3/libraries/sessions.html
$this->load->library('session');
$this->session->set_userdata(['username' => 'john_doe', 'user_id' => 123]);
$username = $this->session->userdata('username');
```

### Upload handling
```php
// Source: https://codeigniter.com/userguide3/libraries/file_uploading.html
$config['upload_path'] = './uploads/';
$config['allowed_types'] = 'xlsx|xls';
$this->load->library('upload', $config);
if (! $this->upload->do_upload('userfile')) {
    $error = $this->upload->display_errors();
}
```

### Excel load (PhpSpreadsheet)
```php
// Source: https://github.com/phpoffice/phpspreadsheet/blob/master/docs/topics/reading-and-writing-to-file.md
$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
$spreadsheet = $reader->load("import.xlsx");
```

### File download
```php
// Source: https://codeigniter.com/userguide3/helpers/download_helper.html
force_download('/path/to/template.xlsx', NULL);
```

## State of the Art

| Old Approach | Current Approach | When Changed | Impact |
|--------------|------------------|--------------|--------|
| sha1() password hashing | password_hash/password_verify (PASSWORD_DEFAULT) | Phase 1 | Secure storage and future-proofing |

**Deprecated/outdated:**
- **SHA1 password hashes:** replaced by password_hash/password_verify and hash-on-login migration.

## Open Questions

1. **How to store “default NIP password” state?**
   - What we know: Passwords are hashed; direct comparison to NIP isn’t possible.
   - What’s unclear: Whether DB already has a flag column for “must change password”.
   - Recommendation: Add `must_change_password` (TINYINT) to `user` table or store in session on first login after import.

2. **Where to keep Excel template file?**
   - What we know: Need admin download (AUTH-06) and consistent format (NIP, Nama, Unit).
   - What’s unclear: Final storage path (assets/templates vs application/templates).
   - Recommendation: Store under `assets/templates/pegawai_import.xlsx` and serve via Download helper.

## Sources

### Primary (HIGH confidence)
- /websites/codeigniter_userguide3 - session library, upload library, form validation, download helper
- https://codeigniter.com/userguide3/libraries/sessions.html
- https://codeigniter.com/userguide3/libraries/file_uploading.html
- https://codeigniter.com/userguide3/libraries/form_validation.html
- https://codeigniter.com/userguide3/helpers/download_helper.html
- /phpoffice/phpspreadsheet - read/write XLSX

### Secondary (MEDIUM confidence)
- https://github.com/phpoffice/phpspreadsheet/blob/master/docs/topics/reading-and-writing-to-file.md

### Tertiary (LOW confidence)
- None

## Metadata

**Confidence breakdown:**
- Standard stack: HIGH - official CI3 and PhpSpreadsheet docs
- Architecture: MEDIUM - project-specific patterns inferred from codebase
- Pitfalls: MEDIUM - based on common CI3 + Excel import failure modes

**Research date:** 2026-02-11
**Valid until:** 2026-03-11
