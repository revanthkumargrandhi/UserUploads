<?php
defined('MOODLE_INTERNAL') || die();
global $DB;

$settingspage = new admin_settingpage('randomemails', new lang_string('randomemails', 'local_users'));
$users = $DB->get_records_sql_menu("SELECT id, CONCAT(firstname, ' ', lastname) FROM {local_users} ");


if ($ADMIN->fulltree) {
    $settingspage->add(new admin_setting_configselect('sendmail', get_string('selectuser', 'local_users'), '', [], $users));
}

$ADMIN->add('localplugins', $settingspage);