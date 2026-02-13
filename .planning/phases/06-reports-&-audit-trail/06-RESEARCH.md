# Phase 6: Reports & Audit Trail - Research

**Researched:** 2026-02-12
**Domain:** CodeIgniter 3 reporting, audit trail, Excel export (PhpSpreadsheet)
**Confidence:** MEDIUM

## Summary

Phase 6 can be implemented using the existing CodeIgniter 3 stack with MySQL tables already present for requests and stock movements. The `stock_movement` table (created in `database/schema.sql`) already acts as the audit trail for stock changes, and existing models (`Stock_model`, `Request_model`) show how movement logging is done transactionally. Report pages should query these tables with filters (date ranges, user/pegawai, status, item, category), and the same filter logic should be reused for exports.

For Excel exports, the project already depends on `phpoffice/phpspreadsheet` (^5.4). Use PhpSpreadsheet to generate XLSX files and stream them to the browser. Official docs warn that *any* output before headers will corrupt the Excel file; this should inform planning of controller actions and output buffering. Running balance for stock movements is not stored, so it must be derived at query time (prefer SQL window functions if MySQL supports it; otherwise compute in PHP by applying a consistent movement-type-to-delta mapping).

**Primary recommendation:** Use PhpSpreadsheet for all Excel exports and compute stock running balance from `stock_movement` with a defined movement-type delta map.

## Standard Stack

The established libraries/tools for this domain:

### Core
| Library | Version | Purpose | Why Standard |
|---------|---------|---------|--------------|
| CodeIgniter 3 | Project (CI3) | MVC web framework | Existing app framework and controllers/models |
| MySQL (mysqli driver) | Project config | Query and filter report data | Current DB setup (CI database driver: mysqli) |
| PhpSpreadsheet | ^5.4 (composer.json) | Excel export (XLSX) | Official, already required dependency |

### Supporting
| Library | Version | Purpose | When to Use |
|---------|---------|---------|-------------|
| CodeIgniter Query Builder | CI3 | Filtered queries | Report filters and joins |

### Alternatives Considered
| Instead of | Could Use | Tradeoff |
|------------|-----------|----------|
| PhpSpreadsheet | None (requirement mandates Excel export) | Not applicable |

**Installation:**
```bash
composer require phpoffice/phpspreadsheet:^5.4
```

## Architecture Patterns

### Recommended Project Structure
```
application/
├── controllers/
│   └── Reports.php        # admin-only report pages + export actions
├── models/
│   ├── Request_model.php  # request history data
│   └── Stock_model.php    # stock movement + items
└── views/
    └── reports/           # report list + filters + export buttons
```

### Pattern 1: Request history report via joined header + items
**What:** Query request_header + request_item + user + stock_item for report rows, with date/user/status filters.
**When to use:** REPORT-01, REPORT-02 (request history view + filters).
**Example:**
```php
// Source: application/models/Request_model.php (existing filtering pattern)
$this->db->from('request_header');
if (!empty($filters['status'])) {
    $this->db->where('status', $filters['status']);
}
if (!empty($filters['user_id'])) {
    $this->db->where('user_id', $filters['user_id']);
}
```

### Pattern 2: Stock movement audit trail from stock_movement
**What:** Use `stock_movement` as the audit log, join user + item + category, filter by date/item/category.
**When to use:** REPORT-03, REPORT-04, REPORT-05, REPORT-06.
**Example:**
```php
// Source: application/models/Stock_model.php
$this->db
    ->select('stock_movement.*, user.nama as user_name')
    ->from('stock_movement')
    ->join('user', 'user.id_user = stock_movement.user_id', 'left')
    ->where('stock_movement.item_id', $id_item)
    ->order_by('stock_movement.created_at', 'DESC');
```

### Pattern 3: Running balance derived from movement types
**What:** Compute balance by applying a consistent delta per movement_type (e.g., `in` +qty, `out` -qty, `adjust` +/-qty, `reserve` -qty available, `cancel` +qty available, `deliver` no change to available but move reserved->used).
**When to use:** REPORT-03 (running balance).
**Example:**
```php
// Source: derived from stock_movement schema + Stock_model usage
// Pseudocode: running balance for available_qty
foreach ($rows as $row) {
  switch ($row['movement_type']) {
    case 'in':      $balance += $row['qty_delta']; break;
    case 'out':     $balance -= $row['qty_delta']; break;
    case 'adjust':  $balance += $row['qty_delta']; break;
    case 'reserve': $balance -= $row['qty_delta']; break;
    case 'cancel':  $balance += $row['qty_delta']; break;
    case 'deliver': /* available unchanged */ break;
  }
}
```

### Anti-Patterns to Avoid
- **Output before Excel headers:** Any stray output corrupts the XLSX stream; no echo/whitespace before headers.
- **N+1 queries for report rows:** Use joins to avoid per-row lookups for user/item/category.
- **Undefined running balance rules:** If movement-type delta rules aren’t explicit, balances will be inconsistent.

## Don't Hand-Roll

Problems that look simple but have existing solutions:

| Problem | Don't Build | Use Instead | Why |
|---------|-------------|-------------|-----|
| Excel export | Manual XML/CSV output | PhpSpreadsheet | Handles XLSX structure, formatting, and stream output safely |
| Audit trail | Custom logs in controllers | `stock_movement` table | Already transactional, indexed, and part of stock workflows |

**Key insight:** Reports must be derived from authoritative transactional tables (`request_*`, `stock_movement`) to preserve audit integrity.

## Common Pitfalls

### Pitfall 1: Corrupted Excel downloads
**What goes wrong:** XLSX files are corrupt or headers cannot be set.
**Why it happens:** Any output (whitespace/BOM/echo) before headers/`php://output`.
**How to avoid:** Ensure no output before streaming; keep export methods dedicated.
**Warning signs:** Browser downloads a few bytes or Excel says “file format or extension is not valid.”

### Pitfall 2: Running balance mismatch
**What goes wrong:** Reported balance doesn’t match stock_item.available_qty.
**Why it happens:** Movement types affect different stock buckets; missing a delta rule.
**How to avoid:** Define and document movement_type→delta mapping and reuse for both report and export.
**Warning signs:** Balance diverges after reserve/deliver/cancel operations.

### Pitfall 3: Date range off-by-one
**What goes wrong:** Rows on the end date are excluded.
**Why it happens:** Using `< end_date` vs inclusive `<= end_date 23:59:59`.
**How to avoid:** Normalize input dates and use inclusive boundaries for timestamps.
**Warning signs:** End-of-day transactions missing from report.

## Code Examples

Verified patterns from official sources:

### Write XLSX to file (PhpSpreadsheet)
```php
// Source: https://github.com/phpoffice/phpspreadsheet/blob/master/docs/topics/reading-and-writing-to-file.md
$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
$writer->save("05featuredemo.xlsx");
```

### Output to browser (notes)
```text
// Source: https://github.com/phpoffice/phpspreadsheet/blob/master/docs/topics/recipes.md
// Notes: Do not output any content before headers; Xlsx writer uses temporary storage for php://output.
```

## State of the Art

| Old Approach | Current Approach | When Changed | Impact |
|--------------|------------------|--------------|--------|
| N/A in repo | PhpSpreadsheet XLSX | Dependency present in composer.json | Standardized Excel output |

**Deprecated/outdated:**
- None verified in project docs.

## Open Questions

1. **What MySQL version is deployed?**
   - What we know: CI3 uses mysqli; schema uses InnoDB.
   - What's unclear: MySQL 8 window functions availability for running balance.
   - Recommendation: If MySQL < 8, compute running balance in PHP.

2. **What should “running balance” represent?**
   - What we know: Stock has Available/Reserved/Used; movements cover reserve/deliver/cancel.
   - What's unclear: Report should show balance for available only or total stock.
   - Recommendation: Define balance as available_qty for REPORT-03 unless stakeholders require otherwise.

## Sources

### Primary (HIGH confidence)
- /phpoffice/phpspreadsheet - docs for writing XLSX and browser output notes
- C:\xampp\htdocs\siat\composer.json - confirms PhpSpreadsheet dependency
- C:\xampp\htdocs\siat\database\schema.sql - stock_movement/request tables
- C:\xampp\htdocs\siat\application\models\Stock_model.php - movement logging patterns
- C:\xampp\htdocs\siat\application\models\Request_model.php - request data patterns

### Secondary (MEDIUM confidence)
- CodeIgniter 3 query builder usage inferred from existing models

### Tertiary (LOW confidence)
- None

## Metadata

**Confidence breakdown:**
- Standard stack: HIGH - dependency and framework found in repo
- Architecture: MEDIUM - based on existing model patterns
- Pitfalls: MEDIUM - PhpSpreadsheet docs + typical CI export issues

**Research date:** 2026-02-12
**Valid until:** 2026-03-12
