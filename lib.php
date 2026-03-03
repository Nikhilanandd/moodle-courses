<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Library functions for local_tgpacourses.
 *
 * @package    local_tgpacourses
 * @copyright  2026 TGPA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Check if a user is eligible for the TGPA Courses executive view.
 *
 * A user is eligible if they have any of the configured allowed roles
 * at system context, OR their username is in the allowed usernames list.
 *
 * @param int $userid The user ID to check. Defaults to current user.
 * @return bool True if the user is eligible.
 */
function local_tgpacourses_is_eligible_user($userid = 0) {
    global $USER, $DB;

    if (empty($userid)) {
        $userid = $USER->id;
    }

    // Check allowed roles.
    $allowedroles = get_config('local_tgpacourses', 'allowedroles');
    if (!empty($allowedroles)) {
        $roleids = explode(',', $allowedroles);
        $roleids = array_map('trim', $roleids);
        $roleids = array_filter($roleids);

        if (!empty($roleids)) {
            $systemcontext = context_system::instance();
            $userroles = get_user_roles($systemcontext, $userid, false);

            foreach ($userroles as $userrole) {
                if (in_array($userrole->roleid, $roleids)) {
                    return true;
                }
            }
        }
    }

    // Check allowed usernames.
    $allowedusernames = get_config('local_tgpacourses', 'allowedusernames');
    if (!empty($allowedusernames)) {
        $usernames = explode(',', $allowedusernames);
        $usernames = array_map('trim', $usernames);
        $usernames = array_filter($usernames);

        if (!empty($usernames)) {
            $user = $DB->get_record('user', ['id' => $userid], 'username');
            if ($user && in_array($user->username, $usernames)) {
                return true;
            }
        }
    }

    return false;
}

/**
 * Extend the global navigation to hide "My courses" and add "Courses" node
 * for eligible users.
 *
 * @param global_navigation $navigation The global navigation object.
 */
function local_tgpacourses_extend_navigation(global_navigation $navigation) {
    global $USER, $PAGE;

    // Only modify for logged-in users.
    if (!isloggedin() || isguestuser()) {
        return;
    }

    // Check if the current user is eligible.
    if (!local_tgpacourses_is_eligible_user($USER->id)) {
        return;
    }

    // Hide "My courses" navigation node.
    $mycourses = $navigation->find('mycourses', global_navigation::TYPE_ROOTNODE);
    if ($mycourses) {
        $mycourses->showinflatnavigation = false;
        $mycourses->remove();
    }

    // Also try the 'courses' key used in some Moodle versions.
    $coursesnode = $navigation->find('courses', global_navigation::TYPE_ROOTNODE);
    if ($coursesnode) {
        $coursesnode->showinflatnavigation = false;
        $coursesnode->remove();
    }

    // Add "Courses" navigation node pointing to our custom page.
    $url = new moodle_url('/local/tgpacourses/index.php');
    $node = $navigation->add(
        get_string('courses', 'local_tgpacourses'),
        $url,
        navigation_node::TYPE_CUSTOM,
        get_string('courses', 'local_tgpacourses'),
        'local_tgpacourses_courses',
        new pix_icon('i/course', '')
    );
    $node->showinflatnavigation = true;

    // Place after "Home" by setting sort order.
    if ($node) {
        $node->set_force_into_more_menu(false);
    }
}

/**
 * Get users assigned a specific role (by shortname) in a course context.
 *
 * @param int $courseid The course ID.
 * @param string $roleshortname The role shortname to look up.
 * @return array Array of user objects (id, firstname, lastname, fullname).
 */
function local_tgpacourses_get_role_users_in_course($courseid, $roleshortname) {
    global $DB;

    if (empty($roleshortname)) {
        return [];
    }

    $role = $DB->get_record('role', ['shortname' => $roleshortname]);
    if (!$role) {
        return [];
    }

    $context = context_course::instance($courseid, IGNORE_MISSING);
    if (!$context) {
        return [];
    }

    $users = get_role_users($role->id, $context, false, 'u.id, u.firstname, u.lastname');
    return $users ? $users : [];
}

/**
 * Get the count of active (non-suspended) enrolled users in a course.
 *
 * @param int $courseid The course ID.
 * @return int The number of active enrolled users.
 */
function local_tgpacourses_get_participant_count($courseid) {
    $context = context_course::instance($courseid, IGNORE_MISSING);
    if (!$context) {
        return 0;
    }

    $enrolledusers = get_enrolled_users($context, '', 0, 'u.id', null, 0, 0, true);
    return count($enrolledusers);
}

/**
 * Fetch and categorize all visible courses.
 *
 * @return array Associative array with keys 'completed', 'current', 'upcoming'.
 */
function local_tgpacourses_get_categorized_courses() {
    global $DB;

    $now = time();

    // Fetch all visible courses, excluding the site course (id=1).
    $courses = get_courses('all', 'c.fullname ASC', 'c.*');

    $categorized = [
        'completed' => [],
        'current' => [],
        'upcoming' => [],
    ];

    foreach ($courses as $course) {
        // Skip the site-level course.
        if ((int)$course->id === SITEID) {
            continue;
        }

        // Skip hidden courses.
        if (empty($course->visible)) {
            continue;
        }

        $startdate = (int)$course->startdate;
        $enddate = (int)$course->enddate;

        if ($enddate > 0) {
            // End date is set.
            if ($enddate < $now) {
                $categorized['completed'][] = $course;
            } else if ($startdate <= $now && $enddate >= $now) {
                $categorized['current'][] = $course;
            } else if ($startdate > $now) {
                $categorized['upcoming'][] = $course;
            } else {
                // Fallback: treat as current.
                $categorized['current'][] = $course;
            }
        } else {
            // No end date — use startdate comparison.
            if ($startdate <= $now) {
                $categorized['current'][] = $course;
            } else {
                $categorized['upcoming'][] = $course;
            }
        }
    }

    return $categorized;
}

/**
 * Build the enriched course data for display.
 *
 * For each course, adds category name, director, coordinator, and participant count.
 *
 * @param array $courses Array of course objects.
 * @return array Array of enriched course data objects.
 */
function local_tgpacourses_enrich_courses($courses) {
    global $DB;

    $directorrole = get_config('local_tgpacourses', 'directorrole');
    $coordinatorrole = get_config('local_tgpacourses', 'coordinatorrole');

    $enriched = [];

    foreach ($courses as $course) {
        $data = new stdClass();
        $data->id = $course->id;
        $data->fullname = format_string($course->fullname);
        $data->startdate = $course->startdate;
        $data->enddate = $course->enddate;

        // Category name.
        $category = $DB->get_record('course_categories', ['id' => $course->category], 'name');
        $data->categoryname = $category ? format_string($category->name) : get_string('na', 'local_tgpacourses');

        // Course URL.
        $data->courseurl = (new moodle_url('/course/view.php', ['id' => $course->id]))->out(false);

        // Course Director(s).
        $directors = local_tgpacourses_get_role_users_in_course($course->id, $directorrole);
        $directornames = [];
        foreach ($directors as $director) {
            $directornames[] = fullname($director);
        }
        $data->directors = !empty($directornames) ? implode(', ', $directornames) : get_string('na', 'local_tgpacourses');

        // Course Coordinator(s).
        $coordinators = local_tgpacourses_get_role_users_in_course($course->id, $coordinatorrole);
        $coordinatornames = [];
        foreach ($coordinators as $coordinator) {
            $coordinatornames[] = fullname($coordinator);
        }
        $data->coordinators = !empty($coordinatornames) ? implode(', ', $coordinatornames) : get_string('na', 'local_tgpacourses');

        // Participant count.
        $data->participantcount = local_tgpacourses_get_participant_count($course->id);

        $enriched[] = $data;
    }

    return $enriched;
}
