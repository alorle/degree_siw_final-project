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
    var idField = document.getElementById('id');

    // Check username is alphanumeric
    if (/[^a-zA-Z0-9-]/.test(idField.value)) {
        alert('El id debe contener únicamente letras, números o guiones');
        return false;
    }

    return true;
}

function updateId() {
    var titleField = document.getElementById('title');
    var idField = document.getElementById('id');

    idField.value = cleanStr(titleField.value).toLowerCase();
}

function cleanStr(str) {
    // Remove strange characters
    var specialChars = "!@#$^&%*()+=[]\\\/{}|:<>?,.";
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
    str = str.trim().replace(/ /g, '-');

    return str;
}
