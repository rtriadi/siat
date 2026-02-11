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
