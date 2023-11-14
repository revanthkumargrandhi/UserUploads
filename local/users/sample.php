<?php
/**
 * This file is part of eAbyas
 *
 * Copyright eAbyas Info Solutons Pvt Ltd, India
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package local_users
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
$format = optional_param('format', 'csv', PARAM_ALPHA);
$systemcontext = context_system::instance();
if ($format) {
    $fields = [
        'firstname' => 'firstname',
        'lastname' => 'lastname',
        'username' => 'username',
        'email' => 'email',
        'password' => 'password',
    ];
    switch ($format) {
        case 'csv' : user_download_csv($fields);
    }
    die;
}
function user_download_csv($fields) {
    global $CFG, $DB;
    require_once($CFG->libdir . '/csvlib.class.php');
    $filename = 'sampleusers';
    $csvexport = new csv_export_writer();
    $csvexport->set_filename($filename);
    $csvexport->add_data($fields);

    $userdata1 = ['Sample', 'User1', 'user1', 'sampleuser1@mailinator.com', 'Welcome#3'];
    $userdata2 = ['Sample', 'User2', 'user2', 'sampleuser2@mailinator.com', 'Welcome#3'];

    $csvexport->add_data($userdata1);
    $csvexport->add_data($userdata2);
    $csvexport->download_file();
    die;
}
