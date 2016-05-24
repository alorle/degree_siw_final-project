/*
 * Copyright (C) 2016 Álvaro Orduna León
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

function onSaveClick(link, username) {
    var data = {
        update: ''
    };

    if (isWriter(username)) {
        console.log(username + ' is a writer');
        data.writer = '1';
    }

    if (isModerator(username)) {
        console.log(username + ' is a moderator');
        data.moderator = '1';
    }

    if (isAdmin(username)) {
        console.log(username + ' is an admin');
        data.admin = '1';
    }

    $.redirectPost(link, data);
}

function onDeleteClick(link, confirmMessage) {
    if (confirm(confirmMessage)) {
        $.redirectPost(link, {delete: ''});
    }
}

function isWriter(username) {
    return $('#writer-' + username).is(":checked");
}

function isModerator(username) {
    return $('#moderator-' + username).is(":checked");
}

function isAdmin(username) {
    return $('#admin-' + username).is(":checked");
}
