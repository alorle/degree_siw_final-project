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
namespace App\Models;


class Session
{
    const KEY_SESSION_KEY = 'user_session_key';
    const KEY_VALID_TIME = 'user_valid_time';

    /**
     * Instantiates a new session. If a session exists, it does nothing, if not, it starts a new session.
     * @return bool Whether the session was started correctly
     */
    private static function getSessionInstance()
    {
        return isset($_SESSION) || session_start();
    }

    /**
     * Sets user's session
     * @param $session_key
     */
    public static function setUserSession($session_key)
    {
        self::getSessionInstance();
        $_SESSION[self::KEY_SESSION_KEY] = $session_key;
        $_SESSION[self::KEY_VALID_TIME] = time() + 24 * 60 * 60;
    }

    /**
     * Removes user's session
     */
    public static function unsetUserSession()
    {
        self::getSessionInstance();
        unset($_SESSION[self::KEY_SESSION_KEY]);
        unset($_SESSION[self::KEY_VALID_TIME]);
    }

    /**
     * Checks if session is valid
     */
    public static function isUserSessionValid()
    {
        self::getSessionInstance();
        if (isset($_SESSION[self::KEY_SESSION_KEY]) && isset($_SESSION[self::KEY_VALID_TIME])) {
            // All user session variables are available. Get and filter them
            $session = filter_var($_SESSION[self::KEY_SESSION_KEY], FILTER_SANITIZE_STRING);
            $valid_time = $_SESSION[self::KEY_VALID_TIME];

            // Return true if $valid_time is greater than the current time and
            // if user with given session exists in database
            return $valid_time >= time() && !is_null(User::getBySession($session));
        }
        return false;
    }

    /**
     * Get current user
     * @return User|null
     */
    public static function getCurrentUser()
    {
        self::getSessionInstance();
        if (self::isUserSessionValid()) {
            $session = filter_var($_SESSION[self::KEY_SESSION_KEY], FILTER_SANITIZE_STRING);
            return User::getBySession($session);
        }
        return null;
    }
}
