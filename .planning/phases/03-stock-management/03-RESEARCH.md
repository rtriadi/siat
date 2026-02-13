# Phase 3: Stock Management - Research

**Researched:** 2026-02-11
**Domain:** CodeIgniter 3 inventory CRUD + stock state tracking + Excel import (PhpSpreadsheet)
**Confidence:** MEDIUM

## Summary

Scope researched: CI3 patterns for CRUD + bulk import, PhpSpreadsheet read/write, and data-layer patterns for inventory stock state tracking. The project already uses CodeIgniter 3.1.13 and PhpSpreadsheet ^5.4. Standard approach in this stack is MVC controllers/models, CI Query Builder with transactions for multi-step stock updates, and CI Upload + PhpSpreadsheet for Excel import/preview flows.

Stock management should use a master item table with category reference and explicit fields for available/reserved/used, plus an audit/movement log to keep changes traceable. Excel import must be validated before commit and use session-stored preview rows. Low stock alert depends on a per-item threshold and should be computed from Available only. Admin-only access is enforced via `check_admin()` guards.

**Primary recommendation:** Use CI3 Query Builder + transactions for all stock adjustments and bulk restock commits; use CI Upload + PhpSpreadsheet for import preview/commit; add stock movement logging table and per-item low-stock threshold.

## Standard Stack

The established libraries/tools for this domain:

### Core
| Library | Version | Purpose | Why Standard |
|---------|---------|---------|--------------|
| CodeIgniter 3 | 3.1.13 (project) | MVC framework, DB, sessions, validation, uploads | Official framework used by project |
| PhpSpreadsheet | ^5.4 (composer) | Read/write Excel (XLSX/XLS) | De-facto PHP Excel library |
| MySQL/MariaDB (InnoDB) | project DB | Relational store for items, categories, stock states | Required for transactions/constraints |

### Supporting
| Library | Version | Purpose | When to Use |
|---------|---------|---------|-------------|
| CI Upload Library | CI3 | Validate and store uploaded Excel files | Stock restock import (STOCK-03..05) |
| CI Form Validation | CI3 | Validate item create/edit + import metadata | Manual item forms, import confirmation |
| CI Download Helper | CI3 | Force Excel template download | Stock import template (STOCK-06) |
| CI DB Transactions | CI3 | Atomic multi-step updates | Any stock adjustment + bulk restock |
| CI Query Builder | CI3 | CRUD and batch updates | Item CRUD, insert_batch/update_batch |

### Alternatives Considered
| Instead of | Could Use | Tradeoff |
|------------|-----------|----------|
| PhpSpreadsheet | Spout | Spout faster for huge files but less features; PhpSpreadsheet is standard in PHP ecosystem |

**Installation (already present):**
```bash
composer require phpoffice/phpspreadsheet
```

## Architecture Patterns

### Recommended Project Structure
```
application/
├── controllers/
│   └── Stock.php                 # admin-only stock UI + import
├── models/
│   ├── Stock_model.php           # item CRUD + stock adjustments
│   └── Category_model.php        # category CRUD (if separate)
├── helpers/
│   └── fungsi_helper.php         # check_admin() guard
└── views/
    └── stock/                    # index, form, import_preview
```

### Pattern 1: Transactional stock adjustments
**What:** Wrap any stock change (manual adjust, bulk restock) in a DB transaction to keep available/reserved/used consistent.
**When to use:** Every operation that updates multiple columns or rows.
**Example:**
```php
// Source: https://codeigniter.com/userguide3/database/transactions
$this->db->trans_start();
$this->db->query('AN SQL QUERY...');
$this->db->query('ANOTHER QUERY...');
$this->db->trans_complete();
```

### Pattern 2: Batch import with preview
**What:** Upload Excel → parse → validate → store valid rows in session → show preview → commit via insert_batch/update_batch.
**When to use:** Bulk restock import (STOCK-03..05).
**Example:**
```php
// Source: https://codeigniter.com/userguide3/libraries/file_uploading.html
$config['upload_path'] = './uploads/';
$config['allowed_types'] = 'xlsx|xls';
$this->load->library('upload', $config);
if (! $this->upload->do_upload('import_file')) {
    $error = $this->upload->display_errors();
}
```
```php
// Source: https://github.com/phpoffice/phpspreadsheet/blob/master/docs/topics/reading-and-writing-to-file.md
$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
$spreadsheet = $reader->load("import.xlsx");
```

### Pattern 3: Batch insert/update for restock
**What:** Use insert_batch/update_batch for bulk changes.
**When to use:** Import commit or large restock updates.
**Example:**
```php
// Source: https://codeigniter.com/userguide3/database/query_builder
$this->db->insert_batch('mytable', $data);
```

### Anti-Patterns to Avoid
- **Manual SQL concatenation for imports:** use Query Builder + insert_batch to avoid SQL injection and improve consistency.
- **Stock updates without transactions:** causes mismatch between Available/Reserved/Used and log table.
- **Parsing Excel without validation:** leads to invalid quantities or missing categories.

## Don't Hand-Roll

Problems that look simple but have existing solutions:

| Problem | Don't Build | Use Instead | Why |
|---------|-------------|-------------|-----|
| Excel parsing | Custom XLSX reader | PhpSpreadsheet | XLSX is complex; parsing safely is hard |
| File upload validation | Manual $_FILES checks | CI Upload library | Built-in validation and errors |
| File download headers | Manual header management | CI Download helper | Correct MIME/headers across browsers |
| Batch writes | Custom loops with per-row queries | insert_batch/update_batch | Better performance and consistency |

**Key insight:** CI3 already provides robust primitives (upload, validation, DB transactions); focus on stock logic and auditability rather than reimplementing primitives.

## Common Pitfalls

### Pitfall 1: Inconsistent stock state updates
**What goes wrong:** Available/Reserved/Used values no longer add up after edits or imports.
**Why it happens:** Updates happen in multiple queries without transaction or no single source of truth.
**How to avoid:** Wrap all stock changes in transactions and centralize adjustments in Stock_model methods.
**Warning signs:** Negative available stock or totals not matching history.

### Pitfall 2: Low-stock alerts based on total stock
**What goes wrong:** Alerts never trigger or trigger too late.
**Why it happens:** Using total (available+reserved+used) instead of Available only.
**How to avoid:** Define and compute alert threshold based on Available stock only.
**Warning signs:** Items marked “low” while still reserved but not available.

### Pitfall 3: Excel numeric coercion
**What goes wrong:** Item codes or quantities become scientific notation or lose leading zeros.
**Why it happens:** Spreadsheet cell types not handled; casting directly to int/float.
**How to avoid:** Treat codes as strings; validate quantities separately.
**Warning signs:** Imported IDs differ from template or display in exponential form.

### Pitfall 4: Duplicate items or categories
**What goes wrong:** Same item name appears multiple times, confusing updates.
**Why it happens:** No unique constraints or duplicate checks in import.
**How to avoid:** Enforce uniqueness by (category_id, item_name) or explicit SKU; pre-validate import rows.
**Warning signs:** Multiple rows for same item after import.

### Pitfall 5: No audit trail
**What goes wrong:** Stock changes cannot be reconciled or explained.
**Why it happens:** Only current stock stored; no movement log.
**How to avoid:** Add stock_movement table capturing who/when/what change.
**Warning signs:** Disputes about stock without evidence.

## Code Examples

Verified patterns from official sources:

### Upload handling for Excel
```php
// Source: https://codeigniter.com/userguide3/libraries/file_uploading.html
$this->load->library('upload');
$config['upload_path'] = './uploads/';
$config['allowed_types'] = 'xlsx|xls';
$config['max_size'] = 2048;
$this->upload->initialize($config);
if (! $this->upload->do_upload('import_file')) {
    $error = $this->upload->display_errors();
}
```

### Read Excel with PhpSpreadsheet
```php
// Source: https://github.com/phpoffice/phpspreadsheet/blob/master/docs/topics/reading-and-writing-to-file.md
$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
$spreadsheet = $reader->load("import.xlsx");
```

### Transaction wrapper
```php
// Source: https://codeigniter.com/userguide3/database/transactions
$this->db->trans_start();
$this->db->query('AN SQL QUERY...');
$this->db->query('ANOTHER QUERY...');
$this->db->trans_complete();
```

### Batch insert
```php
// Source: https://codeigniter.com/userguide3/database/query_builder
$this->db->insert_batch('mytable', $data);
```

## State of the Art

| Old Approach | Current Approach | When Changed | Impact |
|--------------|------------------|--------------|--------|
| Single stock column | Separate Available/Reserved/Used + movement log | v1 requirements | Prevents over-allocation, enables audit |

**Deprecated/outdated:**
- **Manual stock math in controllers:** should be centralized in model/service methods with transactions.

## Open Questions

1. **Schema details for stock tracking**
   - What we know: Three stock states required; low-stock threshold required.
   - What's unclear: Table structure (items, categories, stock movements) and constraints.
   - Recommendation: Define `item` table with `available_qty`, `reserved_qty`, `used_qty`, `low_stock_threshold`, and a `stock_movement` table with reason + user_id.

2. **Import matching strategy**
   - What we know: Bulk restock via Excel with validation and preview.
   - What's unclear: How imported rows match existing items (ID vs name+category).
   - Recommendation: Include Item ID or SKU in template for deterministic updates; fallback to name+category with strict validation.

3. **Category management**
   - What we know: Items organized by category; categories like writing tools, paper.
   - What's unclear: Whether categories are fixed or editable by admin.
   - Recommendation: Implement a category lookup table with admin CRUD if not fixed.

## Sources

### Primary (HIGH confidence)
- /websites/codeigniter_userguide3 - Upload library, Download helper, Form validation, Query Builder, Transactions
- https://codeigniter.com/userguide3/libraries/file_uploading.html
- https://codeigniter.com/userguide3/helpers/download_helper.html
- https://codeigniter.com/userguide3/database/transactions
- https://codeigniter.com/userguide3/database/query_builder
- /phpoffice/phpspreadsheet - Read/write XLSX
- https://github.com/phpoffice/phpspreadsheet/blob/master/docs/topics/reading-and-writing-to-file.md

### Secondary (MEDIUM confidence)
- Project Phase 2 research patterns for Excel preview/commit flow

### Tertiary (LOW confidence)
- None

## Metadata

**Confidence breakdown:**
- Standard stack: HIGH - verified by project composer.json + CI3 docs
- Architecture: MEDIUM - inferred from CI3 patterns + existing import flow
- Pitfalls: MEDIUM - common stock system failure modes

**Research date:** 2026-02-11
**Valid until:** 2026-03-11
