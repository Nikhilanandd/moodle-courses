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
 * TGPA Courses overview page.
 *
 * Displays all visible courses categorized as Completed, Current, and Upcoming
 * with Course Director, Course Coordinator, and participant count.
 *
 * @package    local_tgpacourses
 * @copyright  2026 TGPA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/tgpacourses/lib.php');

require_login();

// Check if user is eligible.
if (!local_tgpacourses_is_eligible_user($USER->id)) {
    throw new moodle_exception('notauthorized', 'local_tgpacourses');
}

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/tgpacourses/index.php'));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('pagetitle', 'local_tgpacourses'));
$PAGE->set_heading(get_string('pagetitle', 'local_tgpacourses'));

// Fetch categorized courses.
$categorized = local_tgpacourses_get_categorized_courses();

// Enrich each category with additional data.
$currentcourses = local_tgpacourses_enrich_courses($categorized['current']);
$upcomingcourses = local_tgpacourses_enrich_courses($categorized['upcoming']);
$completedcourses = local_tgpacourses_enrich_courses($categorized['completed']);

echo $OUTPUT->header();

// Inline CSS for professional executive styling.
echo '<style>
.tgpa-section {
    margin-bottom: 2rem;
}
.tgpa-section-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    border-radius: 0.5rem 0.5rem 0 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: #fff;
}
.tgpa-section-header.current {
    background: linear-gradient(135deg, #0f6cbf, #1a73e8);
}
.tgpa-section-header.upcoming {
    background: linear-gradient(135deg, #d4760a, #e68a00);
}
.tgpa-section-header.completed {
    background: linear-gradient(135deg, #1e7e34, #28a745);
}
.tgpa-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: rgba(255,255,255,0.25);
    border-radius: 1rem;
    padding: 0.1rem 0.65rem;
    font-size: 0.85rem;
    font-weight: 700;
    margin-left: 0.5rem;
}
.tgpa-table-wrap {
    border: 1px solid #dee2e6;
    border-top: none;
    border-radius: 0 0 0.5rem 0.5rem;
    overflow-x: auto;
    margin-bottom: 0;
}
.tgpa-table {
    width: 100%;
    margin-bottom: 0;
    border-collapse: collapse;
}
.tgpa-table thead th {
    background: #f8f9fa;
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    color: #495057;
    padding: 0.75rem 1rem;
    border-bottom: 2px solid #dee2e6;
    white-space: nowrap;
}
.tgpa-table tbody td {
    padding: 0.7rem 1rem;
    border-bottom: 1px solid #eee;
    vertical-align: middle;
    font-size: 0.93rem;
}
.tgpa-table tbody tr:last-child td {
    border-bottom: none;
}
.tgpa-table tbody tr:hover {
    background-color: #f1f6ff;
}
.tgpa-course-link {
    font-weight: 600;
    color: #0f6cbf;
    text-decoration: none;
}
.tgpa-course-link:hover {
    text-decoration: underline;
}
.tgpa-nocourses {
    padding: 1.5rem;
    text-align: center;
    color: #888;
    font-style: italic;
    border: 1px solid #dee2e6;
    border-top: none;
    border-radius: 0 0 0.5rem 0.5rem;
}
.tgpa-participant-badge {
    display: inline-block;
    background: #e9ecef;
    color: #495057;
    border-radius: 0.75rem;
    padding: 0.15rem 0.65rem;
    font-weight: 600;
    font-size: 0.88rem;
}
</style>';

/**
 * Render a course table section.
 *
 * @param string $title Section title.
 * @param string $cssclass CSS class for the header colouring.
 * @param array $courses Array of enriched course objects.
 */
function local_tgpacourses_render_section($title, $cssclass, $courses) {
    $count = count($courses);

    echo '<div class="tgpa-section">';
    echo '<div class="tgpa-section-header ' . $cssclass . '">';
    echo '<span>' . $title . '</span>';
    echo '<span class="tgpa-badge">' . $count . '</span>';
    echo '</div>';

    if (empty($courses)) {
        echo '<div class="tgpa-nocourses">' . get_string('nocourses', 'local_tgpacourses') . '</div>';
        echo '</div>';
        return;
    }

    echo '<div class="tgpa-table-wrap">';
    echo '<table class="tgpa-table">';
    echo '<thead><tr>';
    echo '<th>' . get_string('coursename', 'local_tgpacourses') . '</th>';
    echo '<th>' . get_string('category', 'local_tgpacourses') . '</th>';
    echo '<th>' . get_string('startdate', 'local_tgpacourses') . '</th>';
    echo '<th>' . get_string('enddate', 'local_tgpacourses') . '</th>';
    echo '<th>' . get_string('coursedirector', 'local_tgpacourses') . '</th>';
    echo '<th>' . get_string('coursecoordinator', 'local_tgpacourses') . '</th>';
    echo '<th>' . get_string('participants', 'local_tgpacourses') . '</th>';
    echo '</tr></thead>';
    echo '<tbody>';

    foreach ($courses as $course) {
        $startdatestr = $course->startdate ? userdate($course->startdate, get_string('strftimedatefull')) : get_string('na', 'local_tgpacourses');
        $enddatestr = (!empty($course->enddate) && (int)$course->enddate > 0)
            ? userdate($course->enddate, get_string('strftimedatefull'))
            : get_string('noenddate', 'local_tgpacourses');

        echo '<tr>';
        echo '<td><a class="tgpa-course-link" href="' . s($course->courseurl) . '">' . $course->fullname . '</a></td>';
        echo '<td>' . $course->categoryname . '</td>';
        echo '<td>' . $startdatestr . '</td>';
        echo '<td>' . $enddatestr . '</td>';
        echo '<td>' . s($course->directors) . '</td>';
        echo '<td>' . s($course->coordinators) . '</td>';
        echo '<td><span class="tgpa-participant-badge">' . (int)$course->participantcount . '</span></td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
    echo '</div>';
    echo '</div>';
}

// Render sections: Current → Upcoming → Completed.
local_tgpacourses_render_section(
    get_string('currentcourses', 'local_tgpacourses'),
    'current',
    $currentcourses
);

local_tgpacourses_render_section(
    get_string('upcomingcourses', 'local_tgpacourses'),
    'upcoming',
    $upcomingcourses
);

local_tgpacourses_render_section(
    get_string('completedcourses', 'local_tgpacourses'),
    'completed',
    $completedcourses
);

echo $OUTPUT->footer();
