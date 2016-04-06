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

namespace utils;


use Models\User;

class Session
{
    const KEY_SESSION = 'user_session';
    const KEY_VALID_TIME = 'user_valid_time';

    private static function getSessionInstance()
    {
        return isset($_SESSION) || session_start();
    }

    public static function checkUserSession()
    {
        self::getSessionInstance();
        if (isset($_SESSION[self::KEY_SESSION]) && isset($_SESSION[self::KEY_VALID_TIME])) {
            // All user session variables are available
            $session = filter_var($_SESSION[self::KEY_SESSION], FILTER_SANITIZE_STRING);
            $valid_time = $_SESSION[self::KEY_VALID_TIME];

            // Return true if $valid_time is greater than the current time and
            // if user with given session exists in database
            return $valid_time >= time() && !is_null(User::getBySession($session));
        }

        return false;
    }

    public static function setUserSession($session)
    {
        self::getSessionInstance();
        $_SESSION[self::KEY_SESSION] = $session;
        $_SESSION[self::KEY_VALID_TIME] = time() + 24 * 60 * 60;
    }

    public static function unsetUserSession()
    {
        self::getSessionInstance();
        $_SESSION[self::KEY_VALID_TIME] = 0;
    }

    public static function getUserName()
    {
        self::getSessionInstance();

        // Get user session key
        $session = filter_var($_SESSION[self::KEY_SESSION], FILTER_SANITIZE_STRING);

        // Get user with given session key
        $user = User::getBySession($session);

        if (isset($user)) {
            return $user->getName();
        }

        return null;
    }

    public static function getUser()
    {
        self::getSessionInstance();

        // Get user session key
        $session = filter_var($_SESSION[self::KEY_SESSION], FILTER_SANITIZE_STRING);

        // Get user with given session key
        return User::getBySession($session);
    }
}
