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

var outlineColor = '#EFF4F7';
var outlineColorError = '#FF433D';

function validateForm() {
    var password = document.access_form.password;
    var password_check = document.access_form.password_check;

    if (password.value != password_check.value) {
        alert('Las contraseñas no coinciden');
        return false;
    } else {
        return true;
    }
}

function checkPasswordMatch() {
    var password = document.access_form.password;
    var password_check = document.access_form.password_check;

    if (password.value != password_check.value) {
        password_check.style.outlineColor = outlineColorError;
        return false;
    } else {
        password_check.style.outlineColor = outlineColor;
        return true;
    }
}
