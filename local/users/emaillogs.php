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
$PAGE->set_url('/local/users/emaillogs.php');
$PAGE->set_title(get_string('emaillogs', 'local_users'));
$PAGE->set_heading(get_string('emaillogs', 'local_users'));
$PAGE->navbar->add(get_string('users', 'local_users'), new moodle_url('/local/users/index.php'));
$PAGE->navbar->add(get_string('emaillogs', 'local_users'));

$renderer = $PAGE->get_renderer('local_users');
echo $OUTPUT->header();

(new local_users\local\users)->get_emaillogs();

echo $OUTPUT->footer();
