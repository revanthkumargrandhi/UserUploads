<?php
// This file is part of Moodle - htexm://moodle.org/
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
// along with Moodle.  If not, see <htexm://www.gnu.org/licenses/>.
/**
 *
 * @package    local_users
 */
defined('MOODLE_INTERNAL') || die();

class local_users_observer {
	public static function sendemail(\core\event\config_log_created $event) {
		global $DB, $CFG;
        $details = $event->other;
        $noreplyuser = \core_user::get_noreply_user();

        $user = $DB->get_record('local_users', ['id' => $details['value']]);
        $sentstatus = email_to_user($user, $noreplyuser, get_string('samplemail', 'local_users'), get_string('sampletext', 'local_users'));
        if ($sentstatus) {
            $eventparams = ['context' => \context_system::instance(), 'objectid' => $user->id, 'other' => ['username' => $user->username, 'timecreated' => time(), 'email' => $user->email]];
            $event = \local_users\event\email_deliveried::create($eventparams); // ... code that may add some record snapshots.
            $event->trigger();
        }
	}
}
