# Upgrade Guide

This document provides upgrade notes for `local_tgpacourses`.

## General Upgrade Steps

1. Back up Moodle database and code.
2. Replace plugin code in:
   - `local/tgpacourses`
3. Visit:
   - **Site administration → Notifications**
4. Complete upgrade prompts.
5. Purge caches:
   - **Site administration → Development → Purge caches**

## Post-upgrade Checks

- Verify plugin settings:
  - Allowed Roles
  - Allowed Usernames
  - Director Role Shortname
  - Coordinator Role Shortname
- Validate navigation behavior with:
  - One eligible user
  - One non-eligible user
- Confirm `/local/tgpacourses/index.php` access control and course rendering.

## Version Notes

### 1.0.0
- Initial release. No prior upgrade path.