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


class Session
{
    const KEY_USERNAME = 'user_username';
    const KEY_PASSWORD = 'user_password';
    const KEY_VALID_TIME = 'user_valid_time';

    private static function getSessionInstance()
    {
        return isset($_SESSION) || session_start();
    }

    public static function checkUserSession()
    {
        self::getSessionInstance();
        if (isset($_SESSION[self::KEY_USERNAME]) && isset($_SESSION[self::KEY_PASSWORD]) && isset($_SESSION[self::KEY_VALID_TIME])) {
            // All user session variables are available
            $valid = $_SESSION[self::KEY_VALID_TIME];

            if ($valid >= time()) {
                $username = filter_var($_SESSION[self::KEY_USERNAME], FILTER_SANITIZE_STRING);
                $password = filter_var($_SESSION[self::KEY_PASSWORD], FILTER_SANITIZE_STRING);

                if (!empty($username) && !empty($password)) {
                    // TODO: check user info with database data
                    return true;
                }
            }
        }

        return false;
    }

    public static function setUserSession($username, $password_hash)
    {
        self::getSessionInstance();
        $_SESSION[self::KEY_USERNAME] = $username;
        $_SESSION[self::KEY_PASSWORD] = $password_hash;
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
        return $_SESSION[self::KEY_USERNAME];
    }
}
