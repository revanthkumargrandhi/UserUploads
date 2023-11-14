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
 * Bulk user schedule script from a comma separated file
 *
 * @package    local_users
 */
require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/csvlib.class.php');

$iid = optional_param('iid', 0, PARAM_INT);
$previewrows = optional_param('previewrows', 10, PARAM_INT);

@set_time_limit(60 * 60); // 1 hour should be enough.
raise_memory_limit(MEMORY_HUGE);

global $USER, $DB, $PAGE, $OUTPUT;
$systemcontext = context_system::instance();
$PAGE->set_context($systemcontext);
$PAGE->set_url('/local/users/bulkupload.php');
$strheading = get_string('pluginname', 'local_users') . ' : ' . get_string('uploadusers', 'local_users');
$PAGE->set_title($strheading);
$PAGE->requires->jquery_plugin('ui-css');
require_login();
if (!has_capability('local/users:create', $systemcontext)) {
    throw new required_capability_exception($systemcontext, 'local/users:create', 'nopermissions', '');
}

$PAGE->navbar->add(get_string('users', 'local_users'), new moodle_url('/local/users/index.php'));
$PAGE->navbar->add(get_string('bulkupload', 'local_users'));
$returnurl = new moodle_url('/local/users/index.php');
$stdfields = ['firstname', 'lastname', 'username', 'email', 'password'];
$prffields = [];

$mform = new local_users\form\uploadform($CFG->wwwroot . '/local/users/bulkupload.php');
if ($mform->is_cancelled()) {
    redirect($returnurl);
} else if ($formdata = $mform->get_data()) {
    echo $OUTPUT->header();
    $iid = csv_import_reader::get_new_iid('uploadusers');
    $cir = new csv_import_reader($iid, 'uploadusers');
    $content = $mform->get_file_content('userfile');
    $readcount = $cir->load_csv_content($content,  'utf-8', ',');

    unset($content);
    if ($readcount === false) {
        throw new moodle_exception('csvloaderror', '', $returnurl);
    } else if ($readcount == 0) {
        throw new moodle_exception('csvemptyfile', 'error', $returnurl);
    }
	// Test if columns ok(to validate the csv file content).
    $linenum = 1;
    $mfieldscount = 0;
    $successcreatedcount = 0;
    $filecolumns = (new local_users\local\users)->validate_user_upload_columns($cir, $stdfields, $prffields, $returnurl);
    $cir->init();
    loop:

    while ($line = $cir->next()) {
        $linenum++;
        $schedule_data = new stdClass();
        foreach ($line as $keynum => $value) {
            if (!isset($filecolumns[$keynum])) {
                continue;
            }
            $k = $filecolumns[$keynum];
            $key = array_search($k, $stdfields);
            $schedule_data->$k = $value;
        }
        $validations = (new local_users\local\users)->data_validation($schedule_data, $linenum, $formatteddata);
        $createdcount = (new local_users\local\users)->adduser($validations);
        if ($createdcount == 1) {
            $successcreatedcount++;
        }
    }
    $cir->cleanup(true);
    echo $OUTPUT->box_start('boxwidthnarrow boxaligncenter generalbox', 'uploadresults');
    echo '<div class="panel panel-primary">';
    if ($successcreatedcount > 0) {
        $success->count = $successcreatedcount;
        $success->linenum = $linenum - 1;
        echo get_string('recordsupdated', 'local_users', $success);
    } else {
        echo get_string('zerorecordsupdated', 'local_users');
    }
    if ($mfieldscount > 0) {
        echo '<div class="panel-body">' . get_string('uploaderrors', 'local_users') . ': ' . $mfieldscount . '</div>';
    }
    echo '</div>';
    if ($mfieldscount > 0) {
        echo get_string('fillwithouterrors', 'local_users');
    }

    echo $OUTPUT->box_end();
    $params = ['href' => $CFG->wwwroot. '/local/users/index.php', 'class' => "btn btn-secondary"];
    echo html_writer::tag('a', get_string('continue', 'local_users'), $params);
    echo $OUTPUT->footer();
    die;
} else {
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('uploadusers', 'local_users'));
    $helpparams = ['href' => $CFG->wwwroot. '/local/users/help.php', 'class' => "btn btn-secondary ml-2 float-right"];
    echo html_writer::tag('a', get_string('help', 'local_users'), $helpparams);
    $sampleparams = ['href' => $CFG->wwwroot. '/local/users/sample.php', 'class' => "btn btn-secondary float-right"];
    echo html_writer::tag('a', get_string('sample_csv', 'local_users'), $sampleparams);
    
    $mform->display();
    echo $OUTPUT->footer();
    die;
} 
