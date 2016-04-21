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

namespace App\Controllers;


use App\Core\AbstractController;
use App\Models\Session;
use App\Models\User;
use App\Views\Session\LoginView;
use App\Views\Session\SignUpView;

class SessionController extends AbstractController
{
    const KEY_POST_LOGIN_FORM = 'login';
    const KEY_POST_LOGIN_FORM_USERNAME = 'username';
    const KEY_POST_LOGIN_FORM_PASSWORD = 'password';

    const KEY_POST_SIGN_UP_FORM = 'sign_up';
    const KEY_POST_SIGN_UP_FORM_USERNAME = 'username';
    const KEY_POST_SIGN_UP_FORM_NAME = 'name';
    const KEY_POST_SIGN_UP_FORM_EMAIL = 'email';
    const KEY_POST_SIGN_UP_FORM_PASSWORD = 'password';
    const KEY_POST_SIGN_UP_FORM_PASSWORD_VALIDATION = 'password_validation';

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
                    $user = User::getByUsernameAndPassword($username, md5($password));

                    // Login info will be correct if $user is set
                    if (isset($user)) {
                        // Create the new session key
                        $session_key = md5($user->getUsername() . $user->getEmail() . time());

                        // Update and store new session key
                        Session::setUserSession($session_key);
                        User::updateSession($user->getUsername(), $session_key);

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
        if (Session::isUserSessionValid()) {
            redirect();
        } else {
            if (isset($_POST[self::KEY_POST_SIGN_UP_FORM])) {
                // We have received the sign_up form, so check fields
                $username = filter_var($_POST[self::KEY_POST_SIGN_UP_FORM_USERNAME], FILTER_SANITIZE_STRING);
                $name = filter_var($_POST[self::KEY_POST_SIGN_UP_FORM_NAME], FILTER_SANITIZE_STRING);
                $email = filter_var($_POST[self::KEY_POST_SIGN_UP_FORM_EMAIL], FILTER_SANITIZE_EMAIL);
                $password = filter_var($_POST[self::KEY_POST_SIGN_UP_FORM_PASSWORD], FILTER_SANITIZE_STRING);

                if (empty($username) || empty($name) || empty($email) || empty($password)) {
                    // Sign_up fields are empty (after sanitize),
                    // so display sign up view with error message
                    $this->setView(new SignUpView(self::STR_INVALID_FORM));
                } else {
                    // Check if username is unique
                    if (User::existsUsername($username)) {
                        $this->setView(new SignUpView('Nombre de usuario no válido'));
                        return;
                    }

                    // Check if email is unique
                    if (User::existsEmail($email)) {
                        $this->setView(new SignUpView('Email no válido'));
                        return;
                    }

                    // Create user session key
                    $session_key = md5($username . $email . time());

                    // Insert the new user in the database
                    $inserted = User::insert(array(
                        User::COLUMN_USERNAME => $username,
                        User::COLUMN_NAME => $name,
                        User::COLUMN_EMAIL => $email,
                        User::COLUMN_PASSWORD => md5($password),
                        User::COLUMN_SESSION => $session_key));

                    // If the insertion was successful, store session.
                    // In other case, show an error.
                    if ($inserted) {
                        Session::setUserSession($session_key);
                        redirect();
                    } else {
                        throw new \Exception('Data could not be stored', 500);
                    }
                }
            } else {
                $this->setView(new SignUpView());
            }
        }
    }
}
