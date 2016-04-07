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

namespace Models;


use Database\DbHelper;
use Interfaces\CrudInterface;

class User
{
    const TABLE_NAME = 'users';

    const COLUMN_NAME = 'name';
    const COLUMN_EMAIL = 'email';
    const COLUMN_PASSWORD = 'password';
    const COLUMN_SESSION = 'session';
    const COLUMN_SIGN_IN_TIME = 'sign_up_time';
    const COLUMN_WRITER = 'writer';
    const COLUMN_MODERATOR = 'moderator';
    const COLUMN_ADMIN = 'admin';

    private $name;
    private $email;
    private $password;
    private $session;
    private $sign_up_time;
    private $is_writer;
    private $is_moderator;
    private $is_admin;

    public function __construct($row)
    {
        $this->name = $row[self::COLUMN_NAME];
        $this->email = $row[self::COLUMN_EMAIL];
        $this->password = $row[self::COLUMN_PASSWORD];
        $this->session = $row[self::COLUMN_SESSION];
        $this->sign_up_time = $row[self::COLUMN_SIGN_IN_TIME];
        $this->is_writer = $row[self::COLUMN_WRITER];
        $this->is_moderator = $row[self::COLUMN_MODERATOR];
        $this->is_admin = $row[self::COLUMN_ADMIN];
    }

    public function getName()
    {
        return $this->name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getSession()
    {
        return $this->session;
    }

    public function getSignUpTime()
    {
        return $this->sign_up_time;
    }

    public function isWriter()
    {
        return $this->is_writer;
    }

    public function isModerator()
    {
        return $this->is_moderator;
    }

    public function isAdmin()
    {
        return $this->is_admin;
    }

    /**
     * Get a user from database with the given SQL statement
     * @param DbHelper $db_helper
     * @param string $query SQL statement
     * @return User|null
     */
    private static function getUserFromDb($db_helper, $query)
    {
        // Execute query
        $result = $db_helper->query($query);

        // We return a user only if the result is unique
        if (count($result) == 1) {
            return new User($result[0]);
        }

        return null;
    }

    /**
     * Get a user with the given name and password
     * @param string $name Name of the user
     * @param string $password MD5 sum of the user password
     * @return User|null The requested user if exists. If not, return null
     */
    public static function getUserByNameAndPassword($name, $password)
    {
        $db_helper = DbHelper::instance();

        // Sanitize $name and filter it as a string
        $name = $db_helper->real_escape_string($name);
        if (!$name = filter_var($name, FILTER_SANITIZE_STRING)) {
            return null;
        }

        // Sanitize $password and filter it as a string
        $password = $db_helper->real_escape_string($password);
        if (!$password = filter_var($password, FILTER_SANITIZE_STRING)) {
            return null;
        }

        // Build sql query string
        $query = "SELECT * FROM " . self::TABLE_NAME . " WHERE " .
            self::COLUMN_NAME . " = '" . $name . "' AND " .
            self::COLUMN_PASSWORD . " = '" . $password . "'";

        return self::getUserFromDb($db_helper, $query);
    }

    /**
     * Get a user with the given name
     * @param int $name Name of the user
     * @return User|null The requested user if exists. If not, return null
     */
    public static function getByName($name)
    {
        $db_helper = DbHelper::instance();

        // Sanitize $name and filter it as a string
        $name = $db_helper->real_escape_string($name);
        if (!$name = filter_var($name, FILTER_SANITIZE_STRING)) {
            return null;
        }

        // Build sql query string
        $query = "SELECT * FROM " . self::TABLE_NAME . " WHERE " . self::COLUMN_NAME . " = '" . $name . "'";

        return self::getUserFromDb($db_helper, $query);
    }

    /**
     * Get a user with the given email
     * @param int $email Email of the user
     * @return User|null The requested user if exists. If not, return null
     */
    public static function getByEmail($email)
    {
        $db_helper = DbHelper::instance();

        // Sanitize $email and filter it as an email
        $email = $db_helper->real_escape_string($email);
        if (!$email = filter_var($email, FILTER_SANITIZE_EMAIL)) {
            return null;
        }

        // Build sql query string
        $query = "SELECT * FROM " . self::TABLE_NAME . " WHERE " . self::COLUMN_EMAIL . " = '" . $email . "'";

        return self::getUserFromDb($db_helper, $query);
    }

    /**
     * Get a user with the given session
     * @param int $session Session key of the user
     * @return User|null The requested user if exists. If not, return null
     */
    public static function getBySession($session)
    {
        $db_helper = DbHelper::instance();

        // Sanitize $session and filter it as a string
        $email = $db_helper->real_escape_string($session);
        if (!$email = filter_var($email, FILTER_SANITIZE_STRING)) {
            return null;
        }

        // Build sql query string
        $query = "SELECT * FROM " . self::TABLE_NAME . " WHERE " . self::COLUMN_SESSION . " = '" . $session . "'";

        return self::getUserFromDb($db_helper, $query);
    }

    /**
     * Insert a set of user in the database
     * @param $users array Array with users data to insert in database
     * @return bool Whether the insertion was successful
     */
    public static function insert($users)
    {
        $db_helper = DbHelper::instance();

        foreach ($users as $user) {
            // Build sql query string
            $query = "INSERT INTO " . self::TABLE_NAME . " (" .
                self::COLUMN_NAME . ", " .
                self::COLUMN_EMAIL . ", " .
                self::COLUMN_PASSWORD . ", " .
                self::COLUMN_SESSION .
                ") VALUES (" .
                "'" . $user[self::COLUMN_NAME] . "', " .
                "'" . $user[self::COLUMN_EMAIL] . "', " .
                "'" . $user[self::COLUMN_PASSWORD] . "', " .
                "'" . $user[self::COLUMN_SESSION] . "')";

            // Execute query
            if ($db_helper->connection->query($query) !== TRUE) {
                return false;
            }
        }

        return true;
    }

    /**
     * Updates user session key
     * @param string $name User name
     * @param string $session User session key
     * @return bool Whether the update was successful
     */
    public static function updateSession($name, $session)
    {
        $db_helper = DbHelper::instance();

        // Build sql query string
        $query = "UPDATE " . self::TABLE_NAME .
            " SET " . self::COLUMN_SESSION . " = '" . $session . "'" .
            " WHERE " . self::COLUMN_NAME . " = '" . $name . "'";

        // Execute query
        if ($db_helper->connection->query($query) !== TRUE) {
            return false;
        }

        return true;
    }
}
