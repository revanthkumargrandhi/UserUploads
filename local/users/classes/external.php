<?php
use local_trainingprogram\local\trainingprogram;

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
 *
 * @package    local_users
 */
 
defined('MOODLE_INTERNAL') || die;

class local_users_external extends external_api {

    public static function delete_user_parameters(){
        return new external_function_parameters(
            array(
                'id' => new external_value(PARAM_INT, 'id', 0)
                )
        );

    }  
    public static function delete_user($id){
        global $DB;
        $params = self::validate_parameters(self::delete_user_parameters(),
                                    ['id' => $id]);
        $context = context_system::instance();
        if ($id) {
            $DB->delete_records('local_users', ['id' => $id]);
            $event = \local_users\event\user_deleted::create(array('context' => $context, 'objectid' => $id));
            $event->trigger();
        } else {
            throw new moodle_exception('Error in deleting');
            return false;
        }
    }   
    public static function delete_user_returns() {
        return new external_value(PARAM_BOOL, 'return');
    }
}
