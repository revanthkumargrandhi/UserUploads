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
 * Capability definitions.
 *
 * @package    local_users
 */
require_once('../../config.php');
global $PAGE, $OUTPUT;
$systemcontext = context_system::instance();
$PAGE->set_context($systemcontext);
require_login();
$PAGE->set_url('/local/users/help.php');
$PAGE->set_title(get_string('uploadfilehelp', 'local_users'));
$PAGE->set_heading(get_string('uploadfilehelp', 'local_users'));

echo $OUTPUT->header();
echo get_string('uploadusershelp', 'local_users');

$params = ['href' => $CFG->wwwroot. '/local/users/index.php', 'class' => "btn btn-secondary"];
echo html_writer::tag('a', get_string('continue', 'local_users'), $params);
echo $OUTPUT->footer();
