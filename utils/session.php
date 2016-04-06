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

    const PERM_WRITER = 1;
    const PERM_MODERATOR = 2;
    const PERM_ADMIN = 3;

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

    public static function checkUserPermission($permission)
    {
        $user = self::getUser();

        if (!isset($user)) {
            return false;
        }

        switch ($permission) {
            case self::PERM_WRITER:
                return $user->isWriter();
            case self::PERM_MODERATOR:
                return $user->isModerator();
            case self::PERM_ADMIN:
                return $user->isAdmin();
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
        unset($_SESSION[self::KEY_SESSION]);
        unset($_SESSION[self::KEY_VALID_TIME]);
    }

    public static function getUser()
    {
        self::getSessionInstance();

        if (!self::checkUserSession()) {
            return null;
        }

        // Get user session key
        $session = filter_var($_SESSION[self::KEY_SESSION], FILTER_SANITIZE_STRING);

        // Get user with given session key
        return User::getBySession($session);
    }

    public static function getUserId()
    {
        $user = self::getUser();

        if (isset($user)) {
            return $user->getId();
        }

        return null;
    }

    public static function getUserName()
    {
        $user = self::getUser();

        if (isset($user)) {
            return $user->getName();
        }

        return null;
    }
}
