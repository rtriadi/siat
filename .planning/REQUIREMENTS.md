# Requirements: SIAT - Sistem Inventori ATK Terpadu

**Defined:** 2026-02-10
**Core Value:** Accurate stock tracking with zero anomalies — reserved stock is tracked separately from available stock to prevent over-allocation, and all stock movements are logged for complete auditability.

## v1 Requirements

Requirements for initial release. Each maps to roadmap phases.

### Authentication

- [x] **AUTH-01**: Admin and pegawai can login with username and password
- [x] **AUTH-02**: Admin can upload Excel file with employee data (NIP, Nama, Unit)
- [x] **AUTH-03**: System auto-generates employee accounts (username=NIP, password=NIP)
- [x] **AUTH-04**: System shows password change reminder when pegawai uses default NIP password
- [x] **AUTH-05**: Pegawai can change their own password
- [x] **AUTH-06**: Admin can download Excel template for employee import

### Stock Management

- [ ] **STOCK-01**: Admin can manually add new items with category, name, initial quantity
- [ ] **STOCK-02**: Admin can manually edit item details and adjust quantities
- [ ] **STOCK-03**: Admin can upload Excel file to bulk restock items
- [ ] **STOCK-04**: System validates Excel format and quantity values before import
- [ ] **STOCK-05**: System shows preview of Excel data before final save
- [ ] **STOCK-06**: Admin can download Excel template for restock import
- [ ] **STOCK-07**: Admin can categorize items by type (writing tools, paper, etc.)
- [ ] **STOCK-08**: System tracks three stock states: Available, Reserved, Used
- [ ] **STOCK-09**: System shows low stock alert when available stock below threshold
- [ ] **STOCK-10**: System blocks new requests when available stock is zero
- [ ] **STOCK-11**: Admin can view current stock levels with breakdown (available/reserved/used)

### Request Management

- [x] **REQ-01**: Pegawai can create request by selecting items and quantities
- [x] **REQ-02**: System validates requested quantity against available stock
- [x] **REQ-03**: Pegawai can view list of own requests with status
- [x] **REQ-04**: Pegawai can view details of each request (items, quantities, status, notes)
- [x] **REQ-05**: Admin can view list of all requests with filters (pending/approved/delivered/rejected)
- [x] **REQ-06**: Admin can approve request and reserve stock
- [x] **REQ-07**: Admin can modify requested quantity when approving and add reason note
- [x] **REQ-08**: Admin can reject request with reason note
- [x] **REQ-09**: System reserves approved quantities (deduct from Available, add to Reserved)
- [x] **REQ-10**: Admin can mark items as delivered using checklist interface
- [x] **REQ-11**: System deducts delivered quantities from Reserved and adds to Used
- [x] **REQ-12**: System auto-cancels undelivered quantities (return to Available from Reserved)
- [x] **REQ-13**: Request status tracked through lifecycle: pending → approved → delivered/rejected
- [x] **REQ-14**: Pegawai can cancel own pending request (before admin approval)

### Notifications

- [ ] **NOTIF-01**: Pegawai receives in-app notification when request is approved
- [ ] **NOTIF-02**: Pegawai receives in-app notification when request is rejected
- [ ] **NOTIF-03**: Pegawai receives in-app notification when request is delivered
- [ ] **NOTIF-04**: Admin receives in-app notification when new request is submitted
- [ ] **NOTIF-05**: Admin receives in-app notification when stock falls below threshold

### Reports & Audit

- [ ] **REPORT-01**: Admin can view request history report (who, what, when, status)
- [ ] **REPORT-02**: Admin can filter request history by date range, pegawai, status
- [ ] **REPORT-03**: Admin can view stock movement report (item, in/out, date, user, balance)
- [ ] **REPORT-04**: Admin can filter stock movement by date range, item, category
- [ ] **REPORT-05**: System logs all stock changes to audit trail (user, timestamp, action, reason)
- [ ] **REPORT-06**: Admin can view audit trail with filters
- [ ] **REPORT-07**: Admin can export request history to Excel
- [ ] **REPORT-08**: Admin can export stock movement to Excel
- [ ] **REPORT-09**: Admin can export current stock levels to Excel

## v2 Requirements

Deferred to future release. Tracked but not in current roadmap.

### Advanced Notifications

- **NOTIF-V2-01**: Email notifications for request status changes
- **NOTIF-V2-02**: Email notifications for low stock alerts
- **NOTIF-V2-03**: Configurable notification preferences per user

### Enhanced Reporting

- **REPORT-V2-01**: Dashboard with charts (stock trends, request statistics)
- **REPORT-V2-02**: Export reports to PDF format
- **REPORT-V2-03**: Scheduled automated reports via email

### User Management

- **USER-V2-01**: Admin can manually add/edit/deactivate individual employees
- **USER-V2-02**: Admin can reset employee passwords
- **USER-V2-03**: Role-based permissions (super admin, warehouse staff, viewer)

### Stock Features

- **STOCK-V2-01**: Item photos/images
- **STOCK-V2-02**: Barcode/QR code support for items
- **STOCK-V2-03**: Stock opname (physical count verification)
- **STOCK-V2-04**: Automatic reorder point suggestions

## Out of Scope

Explicitly excluded. Documented to prevent scope creep.

| Feature | Reason |
|---------|--------|
| Real-time push notifications | In-app notifications sufficient; email deferred to v2 |
| Mobile native app | Responsive web interface accessible on mobile browsers |
| Barcode scanning | Manual entry and Excel import sufficient for v1 |
| Multi-location warehouses | Single warehouse sufficient for initial deployment |
| Purchase order integration | External procurement process outside system scope |
| Budget/cost tracking | Focus on physical inventory only, not financial |
| Approval workflow (multi-level) | Single admin approval sufficient for v1 |
| Case-specific tracking | Standard ATK items only, not linked to legal cases |
| Item expiration tracking | ATK items typically non-perishable |

## Traceability

Which phases cover which requirements. Updated during roadmap creation.

| Requirement | Phase | Status |
|-------------|-------|--------|
| AUTH-01 | Phase 2 | Complete |
| AUTH-02 | Phase 2 | Complete |
| AUTH-03 | Phase 2 | Complete |
| AUTH-04 | Phase 2 | Complete |
| AUTH-05 | Phase 2 | Complete |
| AUTH-06 | Phase 2 | Complete |
| STOCK-01 | Phase 3 | Pending |
| STOCK-02 | Phase 3 | Pending |
| STOCK-03 | Phase 3 | Pending |
| STOCK-04 | Phase 3 | Pending |
| STOCK-05 | Phase 3 | Pending |
| STOCK-06 | Phase 3 | Pending |
| STOCK-07 | Phase 3 | Pending |
| STOCK-08 | Phase 3 | Pending |
| STOCK-09 | Phase 3 | Pending |
| STOCK-10 | Phase 3 | Pending |
| STOCK-11 | Phase 3 | Pending |
| REQ-01 | Phase 4 | Complete |
| REQ-02 | Phase 4 | Complete |
| REQ-03 | Phase 4 | Complete |
| REQ-04 | Phase 4 | Complete |
| REQ-05 | Phase 4 | Complete |
| REQ-06 | Phase 4 | Complete |
| REQ-07 | Phase 4 | Complete |
| REQ-08 | Phase 4 | Complete |
| REQ-09 | Phase 4 | Complete |
| REQ-10 | Phase 4 | Complete |
| REQ-11 | Phase 4 | Complete |
| REQ-12 | Phase 4 | Complete |
| REQ-13 | Phase 4 | Complete |
| REQ-14 | Phase 4 | Complete |
| NOTIF-01 | Phase 5 | Pending |
| NOTIF-02 | Phase 5 | Pending |
| NOTIF-03 | Phase 5 | Pending |
| NOTIF-04 | Phase 5 | Pending |
| NOTIF-05 | Phase 5 | Pending |
| REPORT-01 | Phase 6 | Pending |
| REPORT-02 | Phase 6 | Pending |
| REPORT-03 | Phase 6 | Pending |
| REPORT-04 | Phase 6 | Pending |
| REPORT-05 | Phase 6 | Pending |
| REPORT-06 | Phase 6 | Pending |
| REPORT-07 | Phase 6 | Pending |
| REPORT-08 | Phase 6 | Pending |
| REPORT-09 | Phase 6 | Pending |

**Coverage:**
- v1 requirements: 45 total
- Mapped to phases: 45
- Unmapped: 0 ✓

---
*Requirements defined: 2026-02-10*
*Last updated: 2026-02-10 after initial definition*
