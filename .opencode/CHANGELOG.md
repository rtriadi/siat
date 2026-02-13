# Changelog

## 2026-02-10
- Executed database/schema.sql to initialize siat_db with user_role and user tables.
- Verified default Admin/Pegawai roles and admin account insertion.
- Updated planning docs for 01-03 execution (SUMMARY/STATE/ROADMAP).
- Completed Phase 1 Plan 4 verification summary and state/roadmap updates.

## 2026-02-11
- Added user table fields unit and must_change_password with idempotent patch script.
- Extended auth session setup and user model login metadata updates.
- Added role guard helpers for admin and pegawai routes.
## 2026-02-11
- Added pegawai password change handler and view.
- Enforced must_change_password redirect and dashboard reminder.
- Recorded Phase 2 human verification as passed.
- Marked AUTH-01 to AUTH-06 complete in requirements and traceability.
- Updated roadmap and state for Phase 2 completion.
## 2026-02-11
- Created Phase 3 stock management plans (03-01 to 03-03).
- Updated roadmap with Phase 3 plan list and count.
## 2026-02-11
- Added Phase 4 request management workflow research document.
- Created Phase 4 request management plans (04-01 to 04-03).
- Updated roadmap with Phase 4 plan list and count.
## 2026-02-11
- Added request schema tables and stock movement enum updates for request lifecycle.
- Implemented request lifecycle model with transactional stock reservation and delivery logic.
- Added stock reservation, delivery, and release helpers with movement logging.
## 2026-02-11
- Added Pegawai request controller for create/list/detail/cancel with stock validation.
- Built Pegawai request views (form, list, detail) with status display.
- Added Pegawai navigation link for Permintaan ATK.
## 2026-02-11
- Completed Phase 4 Plan 3 summary and state updates for admin request workflow.
## 2026-02-11
- Verified Phase 4 request workflow and recorded verification report.
- Marked REQ-01 to REQ-14 complete in requirements and traceability.
- Updated roadmap and state for Phase 4 completion.
## 2026-02-11
- Created Phase 5 notifications plan files (05-01, 05-02).
- Added Phase 5 plan list and counts to ROADMAP.
## 2026-02-11
- Added notification table schema and patch for Phase 5.
- Added Notification_model and wired request/stock notification emits.

## 2026-02-12
- Verified Phase 5 notifications with human approval and recorded verification report.
- Updated roadmap, state, and requirements for Phase 5 completion.
- Set default route to auth login page.
- Fixed login view flashdata access via CI instance.
- Load session library in Auth controller for login flashdata.
- Fixed base_url to include /siat so assets load.
- Load database in Auth controller for user model queries.
- Configured DB connection to siat_db with root user.
- Imported database/schema.sql into siat_db.
- Autoloaded session and database libraries for helpers.
- Autoloaded Notification_model for template usage.
- Guarded missing satker fields in navbar display.
- Removed unused legacy menu sections from sidebar.
- Added Phase 6 reports & audit trail research document.
- Created Phase 6 plans for request history, stock movement/audit, and stock levels reports.
- Updated roadmap with Phase 6 plan list and counts.
- Revised Phase 6 plans: added pegawai list wiring for request history, defined running balance baseline rules, and specified stock levels category data source.
- Renamed app branding to "SIAT - Sistem Inventori ATK Terpadu" in template and login views.

## 2026-02-12
- Executed Phase 6 plans 06-01, 06-02, 06-03 for reports and audit trail.
- Added request history report with filters (date, pegawai, status) and Excel export.
- Added stock movement report with running balance calculation and Excel export.
- Added audit trail view with filters for stock change accountability.
- Added current stock levels report with category filter and low-stock indicators.
- Verified Phase 6 goal achievement (7/7 must-haves, 9/9 REPORT requirements).
- Updated ROADMAP.md, STATE.md, and REQUIREMENTS.md for Phase 6 completion.
- Milestone v1.0 complete: all 45 requirements delivered across 6 phases.
