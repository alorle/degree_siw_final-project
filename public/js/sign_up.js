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

function validate() {
    const originalColor = "#EFF4F7";
    const errorColor = "#FF433D";

    var usernameValid = true;
    var emailValid = true;
    var passwordValid = true;

    var username = document.access_form.username;
    var email = document.access_form.email;
    var password = document.access_form.password;
    var passwordCheck = document.access_form.password_check;

    if (username.value == '') {
        username.style.outlineColor = errorColor;
        usernameValid = false;
    } else {
        username.style.outlineColor = originalColor;
    }

    if (email.value == '') {
        email.style.outlineColor = errorColor;
        emailValid = false;
    } else {
        email.style.outlineColor = originalColor;
    }

    if (password.value == '' || passwordCheck.value == '' || password.value != passwordCheck.value) {
        password.style.outlineColor = errorColor;
        passwordCheck.style.outlineColor = errorColor;
        passwordValid = false;
    } else {
        password.style.outlineColor = originalColor;
        passwordCheck.style.outlineColor = originalColor;
    }

    return usernameValid && emailValid && passwordValid;
}
