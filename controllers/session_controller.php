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
use Models\Session;
use Models\User;
use Views\LoginView;

class SessionController extends AbstractController
{
    const KEY_POST_LOGIN_FORM = 'login';
    const KEY_POST_LOGIN_FORM_USERNAME = 'username';
    const KEY_POST_LOGIN_FORM_PASSWORD = 'password';

    const STR_INVALID_FORM = 'Datos invalidos';

    /**
     * SessionController constructor.
     * @param $params
     * @throws \Exception
     */
    public function __construct($params)
    {
        $action = '';
        if (isset($params[0])) {
            $action = $params[0];
        }

        switch ($action) {
            case 'login':
                $this->login();
                break;
            case 'logout':
                $this->logout();
                break;
            case 'sign_up':
                $this->sign_up();
                break;
            default:
                throw new \Exception('Unknown action', 500);
        }
    }

    private function login()
    {
        if (Session::isUserSessionValid()) {
            redirect();
        } else {
            if (isset($_POST[self::KEY_POST_LOGIN_FORM])) {
                // We have received the login form, so check fields
                $username = filter_var($_POST[self::KEY_POST_LOGIN_FORM_USERNAME], FILTER_SANITIZE_STRING);
                $password = filter_var($_POST[self::KEY_POST_LOGIN_FORM_PASSWORD], FILTER_SANITIZE_STRING);

                if (empty($username) || empty($password)) {
                    // Login fields are empty (after sanitize),
                    // so display login view with error message
                    $this->setView(new LoginView(self::STR_INVALID_FORM));
                } else {
                    // Get user with given name and password
                    $user = User::getByNameAndPassword($username, md5($password));

                    // Login info will be correct if $user is set
                    if (isset($user)) {
                        // Create the new session key
                        $session_key = md5($user->getName() . $user->getEmail() . time());

                        // Update and store new session key
                        Session::setUserSession($session_key);
                        User::updateSession($user->getName(), $session_key);

                        redirect();
                    } else {
                        $this->setView(new LoginView(self::STR_INVALID_FORM));
                    }
                }
            } else {
                $this->setView(new LoginView());
            }
        }
    }

    private function logout()
    {
        Session::unsetUserSession();
        redirect();
    }

    private function sign_up()
    {
    }
}
