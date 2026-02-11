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
