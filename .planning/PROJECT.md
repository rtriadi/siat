# SIAT - Sistem Inventori ATK Terpadu

## What This Is

A web-based office supply (ATK) inventory management system for government offices, where employees can request items and administrators manage stock, approve requests, and handle deliveries. The system ensures accurate stock tracking with robust validation to prevent inventory anomalies.

## Core Value

Accurate stock tracking with zero anomalies — reserved stock is tracked separately from available stock to prevent over-allocation, and all stock movements are logged for complete auditability.

## Requirements

### Validated

<!-- v1.0 MVP Shipped -->

- ✓ MVC architecture with CodeIgniter 3 framework — existing
- ✓ Session-based authentication system — existing
- ✓ AdminLTE 3 dashboard template with Bootstrap 4 — existing
- ✓ Template-based view rendering — existing
- ✓ Database abstraction via CodeIgniter Query Builder — existing
- ✓ Helper functions for common utilities — existing
- ✓ Flash message system for user feedback — existing
- ✓ CSRF protection and XSS filtering — existing
- ✓ Password hashing upgraded to bcrypt — v1.0
- ✓ Employee (pegawai) can login and request office supplies — v1.0
- ✓ Admin can manage stock levels via manual entry and Excel import — v1.0
- ✓ Admin can approve/modify/reject employee requests with notes — v1.0
- ✓ Admin can deliver items and mark requests as completed — v1.0
- ✓ System reserves stock on approval to prevent over-allocation — v1.0 (confirmed effective)
- ✓ System blocks requests when available stock is zero — v1.0
- ✓ Auto-cancel undelivered quantities after partial delivery — v1.0
- ✓ Stock audit trail logs all movements (who, when, why) — v1.0
- ✓ Excel import for bulk employee registration (NIP as username/password) — v1.0
- ✓ Excel import for bulk stock replenishment with validation — v1.0
- ✓ Downloadable Excel templates for imports — v1.0
- ✓ Item categorization for better organization — v1.0
- ✓ Notifications for employees (request status) and admin (new requests) — v1.0
- ✓ Low stock alerts for administrators — v1.0
- ✓ Request history reports (who requested what, when) — v1.0
- ✓ Stock movement reports (in/out, current levels) — v1.0
- ✓ Password change reminder for employees using default NIP password — v1.0

### Active

<!-- v1.1 Planning -->

- [ ] Add request modification capability (employees can edit pending requests)
- [ ] Add request quantity increase after approval (admin-initiated)
- [ ] Add bulk request capability (multiple items in one request)
- [ ] Add email/SMS notifications (optional, real-time alerts)
- [ ] Add department-based access control (filter requests by unit)

### Out of Scope

- Real-time notifications (push) — email/in-app alerts sufficient for v1
- Mobile app — web interface accessible on mobile browsers
- Barcode scanning — manual entry and Excel import sufficient
- Multi-location warehouses — single warehouse for v1
- Purchase order integration — external procurement process
- Budget tracking — focus on physical inventory only
- Case-specific item tracking — standard ATK items only, not linked to legal cases

## Context

**Shipped v1.0:**
- Complete inventory management system deployed
- 8202+ lines of PHP
- 45 requirements delivered
- Tech stack: CodeIgniter 3.1.13 + AdminLTE 3.2.0 + PHP 8.3 + MySQL

**User Feedback:**
- First milestone complete, system ready for pilot testing

**Known Issues:**
- Cancelled requests don't notify admins (minor, user-initiated)
- Delivery only notifies user, not admins (acceptable, admin-initiated)

## Constraints

- **Tech Stack**: Must use existing CodeIgniter 3 + AdminLTE3 codebase — leverages existing investment
- **Security**: Upgrade from SHA1 to bcrypt for password hashing — compliance requirement
- **Data Integrity**: Stock must never go negative — business critical validation
- **Performance**: Excel import must handle at least 100 rows — typical batch size
- **Browser Support**: Must work in modern browsers (Chrome, Firefox, Edge) — government office standard

## Key Decisions

| Decision | Rationale | Outcome |
|----------|-----------|---------|
| Reserve stock on approval, not on request | Prevents race condition where multiple approved requests exceed available stock | ✓ Verified effective |
| Auto-cancel undelivered quantities | Simplifies admin workflow — no manual cleanup needed | ✓ Working as expected |
| NIP as default password with reminder | Balance between convenience and security | ✓ Users prompted on login |
| Three stock states (Available, Reserved, Used) | Clear separation prevents over-allocation | ✓ Core value achieved |
| Excel import with preview before commit | Prevents bulk data errors | ✓ Admin review implemented |

---

*Last updated: 2026-02-12 after v1.0 MVP milestone*
