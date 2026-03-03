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
 * Language strings for local_tgpacourses.
 *
 * @package    local_tgpacourses
 * @copyright  2026 TGPA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'TGPA Courses';
$string['courses'] = 'Courses';
$string['tgpacourses'] = 'TGPA Courses';
$string['tgpacourses:view'] = 'View TGPA Courses page';

// Settings strings.
$string['setting_allowedroles'] = 'Allowed Roles';
$string['setting_allowedroles_desc'] = 'Select the roles that should see the custom Courses navigation and page. Users assigned any of these roles at system level will get the executive course view.';
$string['setting_allowedusernames'] = 'Allowed Usernames';
$string['setting_allowedusernames_desc'] = 'Comma-separated list of usernames that should also see the custom Courses page, regardless of role assignment.';
$string['setting_directorrole'] = 'Course Director Role Shortname';
$string['setting_directorrole_desc'] = 'The role shortname used for Course Directors within courses (e.g. coursedirector). This is used to display the Course Director for each course.';
$string['setting_coordinatorrole'] = 'Course Coordinator Role Shortname';
$string['setting_coordinatorrole_desc'] = 'The role shortname used for Course Coordinators within courses (e.g. coursecoordinator). This is used to display the Course Coordinator for each course.';

// Page strings.
$string['pagetitle'] = 'TGPA Courses Overview';
$string['completedcourses'] = 'Completed Courses';
$string['currentcourses'] = 'Current Courses';
$string['upcomingcourses'] = 'Upcoming Courses';
$string['coursename'] = 'Course Name';
$string['category'] = 'Category';
$string['startdate'] = 'Start Date';
$string['enddate'] = 'End Date';
$string['coursedirector'] = 'Course Director';
$string['coursecoordinator'] = 'Course Coordinator';
$string['participants'] = 'Participants';
$string['nocourses'] = 'No courses found in this category.';
$string['notauthorized'] = 'You are not authorized to view this page.';
$string['na'] = 'N/A';
$string['noenddate'] = 'No end date';
