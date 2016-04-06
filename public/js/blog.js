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

document.onscroll = function () {
    var actions = document.getElementById('actions');

    if (actions == null) {
        return;
    }

    var children = [].slice.call(document.getElementById('actions').children);

    if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight) {
        children.forEach(function (element) {
            element.className = 'hidden';
        });
    } else {
        children.forEach(function (element) {
            element.className = 'visible';
        });
    }
};
