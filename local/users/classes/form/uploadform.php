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
namespace local_users\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->libdir.'/csvlib.class.php');
use moodleform;

class uploadform extends moodleform {
	public function definition() {
        $mform = $this->_form;

        $filemanageroptions = array(
            'maxbytes' => 10240,
            'accepted_types' => 'csv',
            'maxfiles' => 1,
        );
        
        $mform->addElement('filepicker', 'userfile', get_string('file'), null, $filemanageroptions);
        $mform->addHelpButton('userfile', 'uploaddec', 'local_users');
        $mform->addRule('userfile', null, 'required');

        $this->add_action_buttons(true, get_string('upload'));
    }
}
