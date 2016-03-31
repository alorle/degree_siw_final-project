<?php
/**
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

namespace Controllers;

use Core\AbstractController;
use utils\Session;
use views\SignUpView;

class SignUpController extends AbstractController
{
    const KEY_POST_FORM = 'sign_up';
    const KEY_POST_USERNAME = 'username';
    const KEY_POST_EMAIL = 'email';
    const KEY_POST_PASSWORD = 'password';

    const STR_INVALID = 'Alguno de los campos es inválido';

    /**
     * SignUpController constructor.
     */
    public function __construct()
    {
        if (Session::checkUserSession()) {
            // User is logged
            redirectHome();
        } else {
            if (isset($_POST[self::KEY_POST_FORM])) {
                // SignUp button have been pressed, so check fields
                $username = filter_var($_POST[self::KEY_POST_USERNAME], FILTER_SANITIZE_STRING);
                $email = filter_var($_POST[self::KEY_POST_EMAIL], FILTER_SANITIZE_EMAIL);
                $password = filter_var($_POST[self::KEY_POST_PASSWORD], FILTER_SANITIZE_STRING);

                if (empty($username) || empty($email) || empty($password)) {
                    // SignUp fields are empty (after sanitize),
                    // so display sign up view with error message
                    $this->setView(new SignUpView(self::STR_INVALID));
                } else {
                    // SignUp data is OK. Store it in database and open a new session
                    // TODO: store new user in database
                    Session::setUserSession($username, md5($password));
                    redirectHome();
                }
            } else {
                // SignUp button have not been pressed, so display sign up view
                $this->setView(new SignUpView());
            }
        }
    }
}
