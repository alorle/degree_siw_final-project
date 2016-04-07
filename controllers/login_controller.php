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
use Models\User;
use utils\Session;
use Views\LoginView;

class LoginController extends AbstractController
{
    const KEY_POST_FORM = 'login';
    const KEY_POST_USERNAME = 'username';
    const KEY_POST_PASSWORD = 'password';

    const STR_INVALID = 'Usuario o contraseña invalidos';

    /**
     * LoginController constructor.
     */
    public function __construct()
    {
        if (Session::checkUserSession()) {
            // User is logged
            redirectHome();
        } else {
            if (isset($_POST[self::KEY_POST_FORM])) {
                // Login button have been pressed, so check login fields
                $username = filter_var($_POST[self::KEY_POST_USERNAME], FILTER_SANITIZE_STRING);
                $password = filter_var($_POST[self::KEY_POST_PASSWORD], FILTER_SANITIZE_STRING);

                if (empty($username) || empty($password)) {
                    // Login fields are empty (after sanitize),
                    // so display login view with error message
                    $this->setView(new LoginView(self::STR_INVALID));
                } else {
                    // Get user with given name and password
                    $user = User::getUserByNameAndPassword($username, md5($password));

                    // Login info will be correct if $user is set
                    if (isset($user)) {
                        // Create the new session key
                        $session = md5($user->getName() . $user->getEmail() . time());

                        // Update and store new session key
                        User::updateSession($user->getName(), $session);
                        Session::setUserSession($session);

                        redirectHome();
                    } else {
                        // Display login view with error message,
                        // as name or password is invalid
                        $this->setView(new LoginView(self::STR_INVALID));
                    }
                }
            } else {
                // Login button have not been pressed, so display login view
                $this->setView(new LoginView());
            }
        }
    }
}
