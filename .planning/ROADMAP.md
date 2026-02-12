# ROADMAP: SIAT - Sistem Inventori ATK Terpadu

**Project:** Office supply (ATK) inventory management system for government offices  
**Core Value:** Accurate stock tracking with zero anomalies — reserved stock is tracked separately from available stock to prevent over-allocation, and all stock movements are logged for complete auditability.

**Phases:** 6  
**Depth:** Standard  
**Coverage:** 45/45 requirements mapped ✓

---

## Overview

This roadmap delivers SIAT in 6 phases, building from authentication foundation through core inventory features to reporting capabilities. Each phase completes a coherent, verifiable capability that enables the next phase.

**Phase Flow:** Setup → Authentication → Stock Management → Request Workflow → Notifications → Reports & Audit

---

## Phase 1: Project Setup & Configuration

**Goal:** Development environment is ready and foundation issues are resolved.

**Dependencies:** None (foundation phase)

**Requirements Mapped:** None (infrastructure setup)

**Completed:** 2026-02-10

**Plans:** 4 plans (4/4 complete)

Plans:
- [x] 01-01-PLAN.md — Environment Configuration (base URL, database config, composer.lock)
- [x] 01-02-PLAN.md — Security Upgrade (SHA1 to bcrypt password migration)
- [x] 01-03-PLAN.md — Database Setup (schema creation with user tables)
- [x] 01-04-PLAN.md — Verification (end-to-end setup testing)

**Success Criteria:**

1. Developer can run the application locally with proper database connection
2. Base URL is configured correctly for all environment links
3. Password hashing upgraded from SHA1 to bcrypt across all authentication points
4. composer.lock file exists in repository for reproducible builds
5. Database schema initialized with base tables (users, roles)

---

## Phase 2: Authentication & User Management

**Goal:** Admin and employees can securely access the system with proper account management.

**Dependencies:** Phase 1 (requires configured environment)

**Requirements Mapped:** AUTH-01, AUTH-02, AUTH-03, AUTH-04, AUTH-05, AUTH-06

**Plans:** 4 plans (4/4 complete)

Plans:
- [x] 02-01-PLAN.md — Core auth/session state + schema flags
- [x] 02-02-PLAN.md — Role-based dashboards and redirects
- [x] 02-03-PLAN.md — Password change + default-password enforcement
- [x] 02-04-PLAN.md — Employee import preview + template download

**Success Criteria:**

1. Admin can login with username/password and access admin dashboard
2. Pegawai can login with username/password and access employee interface
3. Admin can upload Excel file with employee data and system creates accounts (username=NIP, password=NIP)
4. Pegawai sees password change reminder on first login with default NIP password
5. Pegawai can change their password from default NIP to custom password
6. Admin can download Excel template for employee bulk import

**Completed:** 2026-02-11

---

## Phase 3: Stock Management

**Goal:** Admin can manage inventory items with accurate stock tracking across three states (Available, Reserved, Used).

**Dependencies:** Phase 2 (requires authenticated admin)

**Requirements Mapped:** STOCK-01, STOCK-02, STOCK-03, STOCK-04, STOCK-05, STOCK-06, STOCK-07, STOCK-08, STOCK-09, STOCK-10, STOCK-11

**Success Criteria:**

1. Admin can manually add new items with category, name, and initial quantity
2. Admin can edit item details and adjust stock quantities
3. Admin can upload Excel file to bulk restock items with validation and preview before save
4. Admin can download Excel template for stock import
5. System displays items organized by category (writing tools, paper, etc.)
6. System shows low stock alert when available stock falls below threshold
7. Admin can view current stock levels with breakdown (Available/Reserved/Used)
8. System blocks new requests when item's available stock is zero

**Plans:** 3 plans (3/3 complete)

Plans:
- [x] 03-01-PLAN.md — Stock schema + core models (categories, items, movements)
- [x] 03-02-PLAN.md — Manual stock CRUD UI with alerts and breakdown
- [x] 03-03-PLAN.md — Excel restock import with preview + template

**Completed:** 2026-02-11

---

## Phase 4: Request Management Workflow

**Goal:** Employees can request items and admin can approve/modify/reject/deliver requests with accurate stock reservation and delivery tracking.

**Dependencies:** Phase 3 (requires stock items to exist)

**Requirements Mapped:** REQ-01, REQ-02, REQ-03, REQ-04, REQ-05, REQ-06, REQ-07, REQ-08, REQ-09, REQ-10, REQ-11, REQ-12, REQ-13, REQ-14

**Success Criteria:**

1. Pegawai can create request by selecting items and quantities, with validation against available stock
2. Pegawai can view list of own requests with status (pending/approved/delivered/rejected)
3. Pegawai can cancel own pending request before admin approval
4. Admin can view list of all requests with filters by status
5. Admin can approve request (with optional quantity modification and note), and system reserves stock (deducts from Available, adds to Reserved)
6. Admin can reject request with reason note
7. Admin can mark items as delivered using checklist, and system deducts from Reserved and adds to Used
8. System auto-cancels undelivered quantities after partial delivery (returns to Available from Reserved)
9. Request status transitions correctly: pending → approved → delivered/rejected

**Plans:** 3 plans (3/3 complete)

Plans:
- [x] 04-01-PLAN.md — Request schema + transactional stock transitions
- [x] 04-02-PLAN.md — Pegawai request create/list/detail/cancel UI
- [x] 04-03-PLAN.md — Admin request review/approve/reject/deliver UI

**Completed:** 2026-02-11

---

## Phase 5: Notifications

**Goal:** Users receive timely in-app notifications for request status changes and admin receives alerts for new requests and low stock.

**Dependencies:** Phase 4 (requires request workflow to exist)

**Requirements Mapped:** NOTIF-01, NOTIF-02, NOTIF-03, NOTIF-04, NOTIF-05

**Plans:** 2 plans (2/2 complete)

Plans:
- [x] 05-01-PLAN.md — Notification schema + model + lifecycle emits
- [x] 05-02-PLAN.md — Notification list UI + unread badge + mark read
 
**Completed:** 2026-02-12

**Success Criteria:**

1. Pegawai receives in-app notification when their request is approved
2. Pegawai receives in-app notification when their request is rejected
3. Pegawai receives in-app notification when their request is delivered
4. Admin receives in-app notification when new request is submitted
5. Admin receives in-app notification when stock falls below threshold

---

## Phase 6: Reports & Audit Trail

**Goal:** Admin can generate comprehensive reports on requests and stock movements with complete audit trail for accountability.

**Dependencies:** Phase 4 (requires request and stock data)

**Requirements Mapped:** REPORT-01, REPORT-02, REPORT-03, REPORT-04, REPORT-05, REPORT-06, REPORT-07, REPORT-08, REPORT-09

**Plans:** 3 plans (1/3 complete)

Plans:
- [x] 06-01-PLAN.md — Request history report + filters + Excel export
- [ ] 06-02-PLAN.md — Stock movement report + audit trail + Excel export
- [ ] 06-03-PLAN.md — Current stock levels report + Excel export

**Success Criteria:**

1. Admin can view request history report showing who requested what, when, and status
2. Admin can filter request history by date range, pegawai, and status
3. Admin can view stock movement report showing item, in/out transactions, date, user, and running balance
4. Admin can filter stock movement by date range, item, and category
5. Admin can view audit trail log of all stock changes with user, timestamp, action, and reason
6. Admin can export request history to Excel
7. Admin can export stock movement report to Excel
8. Admin can export current stock levels to Excel

---

## Progress

| Phase | Goal | Requirements | Status | Progress |
|-------|------|--------------|--------|----------|
| 1 | Project Setup & Configuration | Infrastructure | Complete | 100% (4/4 plans complete) |
| 2 | Authentication & User Management | 6 | Complete | 100% (4/4 plans complete) |
| 3 | Stock Management | 11 | Complete | 100% (3/3 plans complete) |
| 4 | Request Management Workflow | 14 | Complete | 100% (3/3 plans complete) |
| 5 | Notifications | 5 | Complete | 100% (2/2 plans complete) |
| 6 | Reports & Audit Trail | 9 | Pending | 0% |

**Overall Progress:** 5/6 phases complete (83%)

---

## Notes

**Phase 1 Rationale:** Although no requirements map to setup, it's essential to resolve known technical debt (weak password hashing, missing config) before building features. This prevents rework later.

**Stock State Architecture:** Phase 3 establishes the three-state stock model (Available, Reserved, Used) that Phase 4 depends on for accurate reservation logic.

**Notification Placement:** Phase 5 comes after core workflow (Phase 4) because notifications are enhancements to existing actions, not prerequisites.

**Audit Trail Integration:** Phase 6 reports rely on data generated in Phases 3-4, so it naturally comes last.

---

*Roadmap created: 2026-02-10*  
*Last updated: 2026-02-12*
