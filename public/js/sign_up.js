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

function validateForm() {
    var usernameField = document.getElementById('username');
    var passwordField = document.getElementById('password');
    var passwordValidationField = document.getElementById('password_validation');

    // Check username is alphanumeric
    if (/[^a-zA-Z0-9.]/.test(usernameField.value)) {
        alert('El nombre de usuario debe contener únicamente letras o números');
        return false;
    }

    // Check password
    if (passwordField.value != passwordValidationField.value) {
        alert('Las contraseñas deben coincidir');
        return false;
    }

    return true;
}

function updateName() {
    var nameField = document.getElementById('name');
    var usernameField = document.getElementById('username');

    usernameField.value = cleanStr(nameField.value).toLowerCase();
}

function cleanStr(str) {
    // Remove strange characters
    var specialChars = "!@#$^&%*()+=-[]\\\/{}|:<>?,";
    for (var i = 0; i < specialChars.length; i++) {
        str = str.replace(new RegExp("\\" + specialChars[i], 'gi'), '');
    }

    // Replace accent chars with corresponding char
    var accentChars = "ÃÀÁÄÂÈÉËÊÌÍÏÎÒÓÖÔÙÚÜÛãàáäâèéëêìíïîòóöôùúüûÑñÇç";
    var nonAccentChars = "AAAAAEEEEIIIIOOOOUUUUaaaaaeeeeiiiioooouuuunncc";
    for (i = 0; i < accentChars.length; i++) {
        // name = name.replace(accentChars.charAt(i), nonAccentChars.charAt(i));
        str = str.replace(new RegExp("\\" + accentChars[i], 'gi'), nonAccentChars[i]);
    }

    // Delete spaces
    str = str.replace(/ /g, '');

    return str;
}
