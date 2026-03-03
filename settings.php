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
 * Admin settings for local_tgpacourses.
 *
 * @package    local_tgpacourses
 * @copyright  2026 TGPA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {

    $settings = new admin_settingpage('local_tgpacourses', get_string('pluginname', 'local_tgpacourses'));

    // Build role choices dynamically from all roles in the system.
    $roles = role_fix_names(get_all_roles(), context_system::instance(), ROLENAME_ORIGINAL);
    $rolechoices = [];
    foreach ($roles as $role) {
        $rolechoices[$role->id] = $role->localname;
    }

    // Allowed Roles (multi-select).
    $settings->add(new admin_setting_configmultiselect(
        'local_tgpacourses/allowedroles',
        get_string('setting_allowedroles', 'local_tgpacourses'),
        get_string('setting_allowedroles_desc', 'local_tgpacourses'),
        [],
        $rolechoices
    ));

    // Allowed Usernames (comma-separated text).
    $settings->add(new admin_setting_configtextarea(
        'local_tgpacourses/allowedusernames',
        get_string('setting_allowedusernames', 'local_tgpacourses'),
        get_string('setting_allowedusernames_desc', 'local_tgpacourses'),
        ''
    ));

    // Course Director role shortname.
    $settings->add(new admin_setting_configtext(
        'local_tgpacourses/directorrole',
        get_string('setting_directorrole', 'local_tgpacourses'),
        get_string('setting_directorrole_desc', 'local_tgpacourses'),
        ''
    ));

    // Course Coordinator role shortname.
    $settings->add(new admin_setting_configtext(
        'local_tgpacourses/coordinatorrole',
        get_string('setting_coordinatorrole', 'local_tgpacourses'),
        get_string('setting_coordinatorrole_desc', 'local_tgpacourses'),
        ''
    ));

    $ADMIN->add('localplugins', $settings);
}
