# Changelog

All notable changes to `local_tgpacourses` are documented in this file.

## [1.0.0] - 2026-03-03

### Added
- Initial release for Moodle 4.5 LTS.
- Configurable eligibility via:
  - Allowed roles (multi-select)
  - Allowed usernames (comma-separated)
- Navigation behavior for eligible users:
  - Hide **My courses**
  - Add **Courses** node linking to `/local/tgpacourses/index.php`
- Executive courses page with:
  - Login and access restriction
  - Boost-compatible layout
  - Course grouping into Current / Upcoming / Completed
  - Per-course details:
    - Full name
    - Category
    - Start/end dates
    - Course Director
    - Course Coordinator
    - Active participant count (excluding suspended)
- Admin settings page under Local plugins.
- Capability definition: `local/tgpacourses:view`.