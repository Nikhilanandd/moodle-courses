# TGPA Courses (`local_tgpacourses`)

A Moodle 4.5 LTS local plugin that customizes course navigation and provides an executive course dashboard for configured users.

## 1) Overview

For eligible users (configured by admin):

- Hides **My courses** in navigation
- Adds **Courses** node linking to:
  - `/local/tgpacourses/index.php`

For all other users:

- Moodle default navigation remains unchanged

---

## 2) Features

- Role-based and username-based eligibility (fully configurable)
- No hardcoded role IDs, role names, or usernames
- Executive course page with dynamic categorization:
  - **Current Courses**
  - **Upcoming Courses**
  - **Completed Courses**
- Per-course details:
  - Full name
  - Category
  - Start date
  - End date
  - Course Director
  - Course Coordinator
  - Active participant count (excluding suspended users)

---

## 3) Requirements

- Moodle **4.5 LTS**
- PHP version supported by Moodle 4.5
- Theme with Boost-compatible layout (default Boost works)

---

## 4) Installation (ZIP Upload)

1. From your Moodle codebase, ensure plugin folder is:
   - `local/tgpacourses`
2. Zip the `tgpacourses` folder.
3. In Moodle:
   - **Site administration → Plugins → Install plugins**
4. Upload ZIP and complete installation prompts.
5. Purge caches if needed:
   - **Site administration → Development → Purge caches**

---

## 5) Configuration

Go to:

- **Site administration → Plugins → Local plugins → TGPA Courses**

Set the following:

1. **Allowed Roles** (multi-select)
2. **Allowed Usernames** (comma-separated)
3. **Director Role Shortname**
4. **Coordinator Role Shortname**

> Recommended: configure both role shortnames to match your course-level role shortnames exactly.

---

## 6) Access Logic

A user is considered eligible if they match **any** of:

- Has at least one configured role (system context check)
- Username appears in configured allowed username list

Eligible users get modified navigation and can access the executive page.

Non-eligible users are denied access to `/local/tgpacourses/index.php`.

---

## 7) Course Classification Rules

For visible courses (excluding Site Course, ID = 1):

1. **Completed**  
   `enddate < now`
2. **Current**  
   `startdate <= now` and `enddate >= now`
3. **Upcoming**  
   `startdate > now`

If `enddate` is empty/zero, classification falls back to `startdate`-based logic.

---

## 8) Security & API Notes

- Uses Moodle APIs for:
  - Navigation hook
  - Role checks
  - Enrolment user counts
  - Role user retrieval in course context
- No Moodle core modifications
- No `/my/index.php` override
- No global behavior override for all users

---

## 9) File Structure

- `version.php`
- `lib.php`
- `index.php`
- `settings.php`
- `db/access.php`
- `lang/en/local_tgpacourses.php`

---

## 10) Troubleshooting

- **Courses node not appearing**
  - Verify user matches configured role/username
  - Purge caches
- **Director/Coordinator not shown**
  - Confirm role shortnames are correct
  - Confirm role assignments exist in course context
- **Participant count seems low**
  - Suspended users are intentionally excluded

---

## 11) License

This project is licensed under the **GNU General Public License v3.0**.  
See [`LICENSE`](LICENSE).