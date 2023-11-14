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
 * Bulk user schedule script from a comma separated file
 *
 * @package    local_users
 */
namespace local_users\local;

use csv_import_reader;
use stdClass;
use moodle_exception;

class users {
    /**
     * Fetching all users and displaying the list of users
     */
    public function get_users() {
        global $DB, $PAGE;
        $renderer = $PAGE->get_renderer('local_users');
        $users = $DB->get_records('local_users');
        $data = [];
        foreach ($users as $user) {
            $row = [];
            $row['id'] = $user->id;
            $row['firstname'] = $user->firstname;
            $row['lastname'] = $user->lastname;
            $row['email'] = $user->email;
            $data[] = $row;
        } 
        $renderer->users_info($data);
    }
    /**
     * Fetching all Email logs and displaying in the table
    */
    public function get_emaillogs() {
        global $DB, $PAGE;
        $renderer = $PAGE->get_renderer('local_users');
        $logs = $DB->get_records('logstore_standard_log', ['eventname' => '\local_users\event\email_deliveried']);
        $data = [];
        foreach ($logs as $log) {
            $row = [];
            $details = json_decode($log->other);
            $row['username'] = $details->username;
            $row['email'] = $details->email;
            $row['deliveredtime'] = date('Y M d H:m', $details->timecreated);
            $data[] = $row;
        } 

        $renderer->showemaillogs($data);        
    }
    public function validate_user_upload_columns(csv_import_reader $cir, $stdfields, $profilefields, $returnurl) {
        $columns = $cir->get_columns();
        if (empty($columns)) {
            $cir->close();
            $cir->cleanup();
            throw new moodle_exception('cannotreadtmpfile', 'error', $returnurl);
        }
        if (count($columns) < 1) {
            $cir->close();
            $cir->cleanup();
            throw new moodle_exception('csvfewcolumns', 'error', $returnurl);
        }
        $processed = [];
        foreach ($columns as $key => $unused) {
            $field = $columns[$key];
            $lcfield = false;
            if (in_array($field, $stdfields)) {
                $newfield = $field;
            } else {
                $cir->close();
                $cir->cleanup();
                throw new moodle_exception('invalidfieldname', 'error', $returnurl, $field);
            }
            if (in_array($newfield, $processed)) {
                $cir->close();
                $cir->cleanup();
                throw new moodle_exception('duplicatefieldname', 'error', $returnurl, $newfield);
            }
            $processed[$key] = $newfield;
        }
        return $processed;
    }
    public function data_validation($data, $linenum, &$formatteddata) {
        global $DB, $USER;
        $warnings = []; // Warnings List.
        $errors = []; // Errors List.
        $mfields = []; // Mandatory Fields.
        $formatteddata = new stdClass(); //Formatted Data for inserting into DB.

        $result = new stdClass();
        if (empty($data->firstname)) {
            $result->column = 'Firstname';
            $result->linenum = $linenum;
            $errors[] = get_string('notbeempty', 'local_users', $result);
        } else {
            $formatteddata->firstname = $data->firstname;
        }

        if (empty($data->lastname)) {
            $result->column = 'Lastname';
            $result->linenum = $linenum;
            $errors[] = get_string('notbeempty', 'local_users', $result);
        } else {
            $formatteddata->lastname = $data->lastname;
        }
        if (empty($data->username)) {
            $result->column = 'Username';
            $result->linenum = $linenum;
            $errors[] = get_string('notbeempty', 'local_users', $result);
        } else if($data->username) {
            $id = $DB->get_field('local_users', 'id', ['username' => $data->username]);
            if ($id > 0) {
                $result->column = 'Username';
                $result->value = $data->username;
                $result->linenum = $linenum;
                $errors[] = get_string('alreadyexists', 'local_users', $result);
            } else {
                $formatteddata->username = $data->username;
            }
        }

        if (empty($data->email)) {
            $result->column = 'Email';
            $result->linenum = $linenum;
            $errors[] = get_string('notbeempty', 'local_users', $result);
        } else if($data->username) {
            $id = $DB->get_field('local_users', 'id', ['email' => $data->email]);
            if ($id) {
                $result->column = 'Email';
                $result->value = $data->email;
                $result->linenum = $linenum;
                $errors[] = get_string('alreadyexists', 'local_users', $result);
            } else {
                $formatteddata->email = $data->email;
            }
        }

        if (empty($data->password)) {
            $result->column = 'Password';
            $result->linenum = $linenum;
            $errors[] = get_string('notbeempty', 'local_users', $result);
        } else {
            $formatteddata->password = md5($data->password);
        }
        $formatteddata->timecreated = time();

        return compact('mfields', 'errors', 'formatteddata');
    }
    public function adduser($validations=null) {
        global $DB, $USER;
        if (count($validations['errors']) > 0) {
            echo implode(' ', $validations['errors']);
        } else {
            $uploadeduser = $this->create_user($validations['formatteddata']);
            if($uploadeduser) {
                $eventparams = ['context' => \context_system::instance(), 'objectid' => $uploadeduser, 'other' => ['name' => $validations['formatteddata']]];
                $event = \local_users\event\user_inserted::create($eventparams); // ... code that may add some record snapshots.
                $event->trigger();
                return true;
            }
            return false;
        }
    }
    public function create_user($data) {
        global $DB;
        try {
            $id = $DB->insert_record('local_users', $data);
            $user = $DB->get_record('local_users', ['id' => $id]);
            $noreplyuser = \core_user::get_noreply_user();
            
            $sentstatus = email_to_user($user, $noreplyuser, get_string('registereduser', 'local_users'), get_string('successfullycreated', 'local_users'));
            if ($sentstatus) {
                $eventparams = ['context' => \context_system::instance(), 'objectid' => $id, 'other' => ['username' => $user->username, 'timecreated' => time(), 'email' => $user->email]];
                $event = \local_users\event\email_deliveried::create($eventparams); // ... code that may add some record snapshots.
                $event->trigger();
            }
        } catch(moodle_exception $e){
            throw new moodle_exception($e);
            return false;
        }

        return $id;
    }
}
