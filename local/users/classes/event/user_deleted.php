<?php
// This file is part of Moodle - http://moodle.org/
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
 * @package    local_users
 */
namespace local_users\event;

defined('MOODLE_INTERNAL') || die();

class user_deleted extends \core\event\base {
    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['crud'] = 'd';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['objecttable'] = 'local_users';
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        global $DB;
        return get_string('userdeletedmsg', 'local_users');
        
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('delete', 'local_users');
    }

    /**
     * Get URL related to the action
     *
     * @return \moodle_url
     */
    public function get_url() {
        global $DB;
       
    }
}
