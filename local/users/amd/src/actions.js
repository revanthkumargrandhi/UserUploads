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

import ModalFactory from 'core/modal_factory';
import ModalEvents from 'core/modal_events';
import Ajax from 'core/ajax';
import {get_string as getString} from 'core/str';

const Selectors = {
    actions: {
        deleteuser: '[data-action="deleteuser"]',
    },
};

export const init = () => {
    document.addEventListener('click', function(e) {
        // e.stopImmediatePropagation();
        let element = e.target.closest(Selectors.actions.deleteuser);
        if (element) {
            const id = element.getAttribute('data-id');
            ModalFactory.create({
                title: getString('deleteuser', 'local_users'),
                type: ModalFactory.types.SAVE_CANCEL,
                body: getString('deleteconfirm', 'local_users')
            }).done(function(modal) {
                this.modal = modal;
                modal.setSaveButtonText(getString('delete', 'local_users'));
                modal.getRoot().on(ModalEvents.save, function(e) {
                    e.preventDefault();
                    var params = {};
                    params.id = id;
                    var promise = Ajax.call([{
                        methodname: 'local_users_deleteuser',
                        args: params
                    }]);
                    promise[0].done(function(resp) {
                        window.location = M.cfg.wwwroot + '/local/users/index.php';
                    }).fail(function() {
                        // do something with the exception
                         console.log('exception');
                    });
                }.bind(this));
                modal.show();
            }.bind(this));
        }
    });
};

