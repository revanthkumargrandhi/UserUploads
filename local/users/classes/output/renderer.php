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
namespace local_users\output;

use plugin_renderer_base;

class renderer extends plugin_renderer_base {
    public function user_create_actions() {
        echo $this->render_from_template('local_users/actions', []);
    }
    public function users_info($data) {
        echo $this->render_from_template('local_users/userview', ['data' => $data]);
    }
    public function showemaillogs($data) {
        echo $this->render_from_template('local_users/emaillogs', ['data' => $data]);
    }
}
