# Phase 4: Request Management Workflow - Research

**Researched:** 2026-02-11
**Domain:** CodeIgniter 3 request workflow + transactional stock reservation/delivery
**Confidence:** MEDIUM

## Summary

Scope researched: CodeIgniter 3 patterns for transactional updates, form validation (including callbacks and validating arrays), session flashdata, and query builder arithmetic updates. This phase must guarantee stock consistency across request lifecycle transitions (pending → approved → delivered/rejected) while supporting admin modifications and partial delivery with auto-cancel of undelivered quantities.

Standard approach in this stack is CI3 MVC with controller-driven workflows, model methods wrapping database transactions, and Query Builder for atomic arithmetic updates (set with escape=FALSE). Validation is handled by Form Validation rules and callbacks for stock checks. Lists and filters are typically implemented with server-rendered tables and DataTables or CI Pagination.

**Primary recommendation:** Centralize all request state changes (approve/modify/reject/deliver/cancel) in model methods that wrap DB transactions and use arithmetic updates plus affected_rows checks to prevent over-reservation.

## Standard Stack

The established libraries/tools for this domain:

### Core
| Library | Version | Purpose | Why Standard |
|---------|---------|---------|--------------|
| CodeIgniter 3 | 3.1.13 (project) | MVC framework, DB transactions, validation, sessions | Project standard framework |
| MySQL/MariaDB | project DB | Relational store for requests, items, stock states | Supports transactions and constraints |
| AdminLTE | 3.2.0 (project) | Admin UI templates | Project UI standard |

### Supporting
| Library | Version | Purpose | When to Use |
|---------|---------|---------|-------------|
| DataTables | 1.11.4 (project) | Table sorting/filtering/pagination | Admin request list filters |
| jQuery | 3.6.0 (project) | DOM/AJAX for AdminLTE/DataTables | Dynamic UI interactions |
| CI Form Validation | CI3 | Validate request forms + callbacks | Request create/approve/deliver inputs |
| CI DB Transactions | CI3 | Atomic stock adjustments | Approve/modify/deliver/cancel |
| CI Query Builder | CI3 | Safe CRUD + arithmetic updates | Request/stock updates |
| CI Session | CI3 | Flash messages, auth | Status feedback and guards |
| CI Pagination | CI3 | Server-side list pagination | If DataTables not used |

### Alternatives Considered
| Instead of | Could Use | Tradeoff |
|------------|-----------|----------|
| DataTables (client filter) | CI Pagination + server filters | More backend logic, less client interactivity |

**Installation:**
```bash
# No new packages required for Phase 4 in current stack
```

## Architecture Patterns

### Recommended Project Structure
```
application/
├── controllers/
│   └── Request.php              # pegawai + admin request flows
├── models/
│   ├── Request_model.php         # request header/items + status transitions
│   └── Stock_model.php           # stock reservation/delivery adjustments
├── helpers/
│   └── fungsi_helper.php         # check_admin(), check_pegawai()
└── views/
    └── request/                  # list, detail, form, admin approval, delivery
```

### Pattern 1: Transactional status transitions
**What:** Wrap each state change (approve, reject, deliver, cancel) in a DB transaction that updates request status, request_items, and stock states together.
**When to use:** Any workflow that touches both request data and stock quantities.
**Example:**
```php
// Source: https://codeigniter.com/userguide3/database/transactions
$this->db->trans_start();
$this->db->query('AN SQL QUERY...');
$this->db->query('ANOTHER QUERY...');
$this->db->trans_complete();
```

### Pattern 2: Arithmetic stock updates via Query Builder
**What:** Use set() with escape=FALSE to atomically decrement/increment stock columns.
**When to use:** Reserve (available → reserved), deliver (reserved → used), auto-cancel (reserved → available).
**Example:**
```php
// Source: https://codeigniter.com/userguide3/database/query_builder
$this->db->set('available_qty', 'available_qty-1', FALSE);
$this->db->set('reserved_qty', 'reserved_qty+1', FALSE);
$this->db->where('id_item', $item_id);
$this->db->update('items');
```

### Pattern 3: Custom validation callbacks for stock checks
**What:** Use Form Validation callbacks to check per-item quantities and business rules.
**When to use:** Request create and admin approve/modify quantities.
**Example:**
```php
// Source: https://codeigniter.com/userguide3/libraries/form_validation
$this->form_validation->set_rules('qty', 'Quantity', 'required|integer|callback_qty_check');
```

### Pattern 4: Validate arrays (request items)
**What:** Validate array payloads via set_data() when input is structured arrays (e.g., multiple items).
**When to use:** Request create with multiple item rows.
**Example:**
```php
// Source: https://codeigniter.com/userguide3/libraries/form_validation
$this->form_validation->set_data($data_array);
```

### Pattern 5: Pagination for admin list (if server-side)
**What:** Use CI Pagination when server-side list pagination is needed.
**When to use:** Admin request list without DataTables or with large datasets.
**Example:**
```php
// Source: https://codeigniter.com/userguide3/libraries/pagination
$this->load->library('pagination');
$config['base_url'] = 'http://example.com/index.php/request/index';
$config['total_rows'] = 200;
$config['per_page'] = 20;
$this->pagination->initialize($config);
echo $this->pagination->create_links();
```

### Anti-Patterns to Avoid
- **Stock updates outside transactions:** risks mismatched available/reserved/used totals.
- **Direct SQL string concatenation:** increases injection risk and bypasses Query Builder escaping.
- **Approving without re-checking available stock:** allows over-reservation under concurrent approvals.

## Don't Hand-Roll

Problems that look simple but have existing solutions:

| Problem | Don't Build | Use Instead | Why |
|---------|-------------|-------------|-----|
| Transaction management | Manual commit/rollback tracking | CI DB transactions | Built-in error handling + consistency |
| Form validation | Custom validation loops | CI Form Validation + callbacks | Standard rules + error messaging |
| Table pagination/filtering | Custom JS table logic | DataTables | Proven UI behavior, less bug-prone |
| Flash messaging | Custom session logic | CI Session flashdata | Standard pattern for one-time notices |

**Key insight:** CI3 already provides safe primitives for validation, transactions, and UI feedback; custom implementations increase bugs in stock-critical workflows.

## Common Pitfalls

### Pitfall 1: Over-reserving stock
**What goes wrong:** Approved quantities exceed available stock during concurrent approvals.
**Why it happens:** Stock is checked once before update without atomic decrement.
**How to avoid:** In the transaction, update with arithmetic + where available >= qty, then verify affected_rows; rollback if any item fails.
**Warning signs:** Negative available stock or approved quantities not matched by available.

### Pitfall 2: Partial delivery inconsistencies
**What goes wrong:** Reserved quantities are not fully reconciled to used/available after delivery.
**Why it happens:** Only delivered items are updated; undelivered quantities left reserved.
**How to avoid:** On delivery, compute undelivered = approved - delivered, then move reserved → available for the remainder (REQ-12).
**Warning signs:** Reserved stock never decreases after delivery.

### Pitfall 3: Status transitions bypassed
**What goes wrong:** Requests jump directly from pending to delivered or get re-approved.
**Why it happens:** Missing state guards in controller/model logic.
**How to avoid:** Enforce allowed transitions in model methods; reject invalid transitions early.
**Warning signs:** Delivered requests still editable or re-approvable.

### Pitfall 4: Missing audit trail for stock movements
**What goes wrong:** No traceability for reservation/delivery/cancellation.
**Why it happens:** Only current stock fields updated without movement logging.
**How to avoid:** Record a stock_movement row per action (reserve, deliver, cancel) inside the transaction.
**Warning signs:** Disputes about stock changes without evidence.

## Code Examples

Verified patterns from official sources:

### Transaction wrapper
```php
// Source: https://codeigniter.com/userguide3/database/transactions
$this->db->trans_start();
$this->db->query('AN SQL QUERY...');
$this->db->query('ANOTHER QUERY...');
$this->db->trans_complete();

if ($this->db->trans_status() === FALSE)
{
    // handle failure
}
```

### Arithmetic update with set()
```php
// Source: https://codeigniter.com/userguide3/database/query_builder
$this->db->set('field', 'field+1', FALSE);
$this->db->where('id', 2);
$this->db->update('mytable');
```

### Form validation callback
```php
// Source: https://codeigniter.com/userguide3/libraries/form_validation
$this->form_validation->set_rules('qty', 'Quantity', 'callback_qty_check');
```

### Input retrieval with XSS filtering
```php
// Source: https://codeigniter.com/userguide3/libraries/input
$qty = $this->input->post('qty', TRUE);
```

### Flash message
```php
// Source: https://codeigniter.com/userguide3/libraries/sessions
$this->session->set_flashdata('message', 'Request approved');
```

### Pagination (if server-side list)
```php
// Source: https://codeigniter.com/userguide3/libraries/pagination
$this->load->library('pagination');
$config['base_url'] = 'http://example.com/index.php/request/index';
$config['total_rows'] = 200;
$config['per_page'] = 20;
$this->pagination->initialize($config);
echo $this->pagination->create_links();
```

## State of the Art

| Old Approach | Current Approach | When Changed | Impact |
|--------------|------------------|--------------|--------|
| Deduct stock at request submit | Reserve on approval, then move to used on delivery | v1 requirements | Prevents over-allocation and supports partial delivery |

**Deprecated/outdated:**
- **Direct deduction on request submit:** violates reserved/used separation and makes cancellations hard.

## Open Questions

1. **Request schema details**
   - What we know: Requests have header + items, status lifecycle, notes.
   - What's unclear: Table names/columns, unique request number format.
   - Recommendation: Define `requests` (header) and `request_items` tables with status, timestamps, reason notes, and foreign keys.

2. **Delivery checklist behavior**
   - What we know: Admin marks delivered using checklist; undelivered auto-cancel.
   - What's unclear: Whether partial delivery can happen multiple times or only once.
   - Recommendation: Decide if delivery is a single final action; if multiple deliveries, track per-item delivered_qty cumulatively.

3. **Admin modification limits**
   - What we know: Admin can modify quantities on approval with reason.
   - What's unclear: Whether admin can remove items or add new items during approval.
   - Recommendation: Allow only quantity reductions (simpler stock logic) unless requirement says otherwise.

4. **Concurrent approval strategy**
   - What we know: Stock must not go negative; reserve on approval.
   - What's unclear: If row-level locking or conditional updates are used in CI3.
   - Recommendation: Use conditional updates (available_qty >= qty) with affected_rows checks; if any fail, rollback and show error.

## Sources

### Primary (HIGH confidence)
- /websites/codeigniter_userguide3 - Transactions, Query Builder set/arithmetic, Form Validation callbacks, Input, Session, Pagination
- https://codeigniter.com/userguide3/database/transactions
- https://codeigniter.com/userguide3/database/query_builder
- https://codeigniter.com/userguide3/libraries/form_validation
- https://codeigniter.com/userguide3/libraries/input
- https://codeigniter.com/userguide3/libraries/sessions
- https://codeigniter.com/userguide3/libraries/pagination

### Secondary (MEDIUM confidence)
- Project stack docs: .planning/codebase/STACK.md (versions)

### Tertiary (LOW confidence)
- None

## Metadata

**Confidence breakdown:**
- Standard stack: HIGH - verified by project stack documentation
- Architecture: MEDIUM - derived from CI3 patterns + requirements
- Pitfalls: MEDIUM - common stock workflow failure modes

**Research date:** 2026-02-11
**Valid until:** 2026-03-11
