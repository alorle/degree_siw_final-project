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


use App\Database\DbHelper;

class User
{
    const TABLE_NAME = 'users';
    const COLUMN_USERNAME = 'username';
    const COLUMN_NAME = 'name';
    const COLUMN_EMAIL = 'email';
    const COLUMN_PASSWORD = 'password';
    const COLUMN_SESSION = 'session';
    const COLUMN_SIGN_UP_TIME = 'sign_up_time';
    const COLUMN_WRITER = 'writer';
    const COLUMN_MODERATOR = 'moderator';
    const COLUMN_ADMIN = 'admin';

    private $username;
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
        $this->username = $row[self::COLUMN_USERNAME];
        $this->name = $row[self::COLUMN_NAME];
        $this->email = $row[self::COLUMN_EMAIL];
        $this->password = $row[self::COLUMN_PASSWORD];
        $this->session = $row[self::COLUMN_SESSION];
        $this->sign_up_time = $row[self::COLUMN_SIGN_UP_TIME];
        $this->is_writer = $row[self::COLUMN_WRITER];
        $this->is_moderator = $row[self::COLUMN_MODERATOR];
        $this->is_admin = $row[self::COLUMN_ADMIN];
    }

    public function getUsername()
    {
        return $this->username;
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

    public static function getAll($limit = 0, $offset = 0)
    {
        $db_helper = DbHelper::instance();

        if ($limit < 0) {
            $limit = 0;
        }

        if ($offset < 0) {
            $offset = 0;
        }

        // Build sql query string
        $sql = "SELECT * FROM " . self::TABLE_NAME . " ORDER BY " . self::COLUMN_NAME . " ASC";
        if (isset($limit) && $limit != 0) {
            $sql .= " LIMIT " . $limit;
            if (isset($offset) && $offset != 0) {
                $sql .= " OFFSET " . $offset;
            }
        }

        // Initialize array of users.
        $results_array = array();

        // For each query result we include a new user in the array.
        foreach ($db_helper->query($sql) as $index => $row) {
            $results_array[$index] = new User($row);
        }

        return $results_array;
    }

    /**
     * Get a user from database with the given SQL statement
     * @param DbHelper $db_helper
     * @param string $query SQL statement
     * @return User|null The requested user if exists. If not, return null
     */
    private static function getUserFromDb($db_helper, $query)
    {
        // Execute query
        $result = $db_helper->query($query);

        // We return a user only if the result is unique
        return (count($result) == 1) ? new User($result[0]) : null;
    }


    /**
     * Get a user with the given username
     * @param string $username
     * @return User|null The requested user if exists. If not, return null
     */
    public static function getByUsername($username)
    {
        $db_helper = DbHelper::instance();

        // Escape special characters from username
        $username = $db_helper->connection->real_escape_string($username);

        // Build sql query string
        $query = "SELECT * FROM " . self::TABLE_NAME . " WHERE " .
            self::COLUMN_USERNAME . " = '" . $username . "'";

        return self::getUserFromDb($db_helper, $query);
    }

    /**
     * Get a user with the given username and password
     * @param string $username
     * @param string $password MD5 sum of the user password
     * @return User|null The requested user if exists. If not, return null
     */
    public static function getByUsernameAndPassword($username, $password)
    {
        $db_helper = DbHelper::instance();

        // Escape special characters from username and password
        $username = $db_helper->connection->real_escape_string($username);
        $password = $db_helper->connection->real_escape_string($password);

        // Build sql query string
        $query = "SELECT * FROM " . self::TABLE_NAME . " WHERE " .
            self::COLUMN_USERNAME . " = '" . $username . "' AND " .
            self::COLUMN_PASSWORD . " = '" . $password . "'";

        return self::getUserFromDb($db_helper, $query);
    }

    /**
     * Get a user with the given session key
     * @param string $session_key Session key of the user
     * @return User|null The requested user if exists. If not, return null
     */
    public static function getBySession($session_key)
    {
        $db_helper = DbHelper::instance();

        // Escape special characters from session_key
        $session_key = $db_helper->connection->real_escape_string($session_key);

        // Build sql query string
        $query = "SELECT * FROM " . self::TABLE_NAME . " WHERE " . self::COLUMN_SESSION . " = '" . $session_key . "'";
        return self::getUserFromDb($db_helper, $query);
    }

    /**
     * Insert a new user in the database
     * @param array $user New user data to insert in database
     * @return bool Whether the insertion was successful
     */
    public static function insert($user)
    {
        $db_helper = DbHelper::instance();

        // Build sql query string
        $query = "INSERT INTO " . self::TABLE_NAME . " (" .
            self::COLUMN_USERNAME . ", " .
            self::COLUMN_NAME . ", " .
            self::COLUMN_EMAIL . ", " .
            self::COLUMN_PASSWORD . ", " .
            self::COLUMN_SESSION .
            ") VALUES (" .
            "'" . $user[self::COLUMN_USERNAME] . "', " .
            "'" . $user[self::COLUMN_NAME] . "', " .
            "'" . $user[self::COLUMN_EMAIL] . "', " .
            "'" . $user[self::COLUMN_PASSWORD] . "', " .
            "'" . $user[self::COLUMN_SESSION] . "')";

        // Execute query
        return ($db_helper->connection->query($query) !== TRUE) ? false : true;
    }

    /**
     * Updates user session key
     * @param string $username User name
     * @param string $session_key User session key
     * @return bool Whether the update was successful
     */
    public static function updateSession($username, $session_key)
    {
        $db_helper = DbHelper::instance();

        // Escape special characters from name and session_key
        $username = $db_helper->connection->real_escape_string($username);
        $session_key = $db_helper->connection->real_escape_string($session_key);

        // Build sql query string
        $query = "UPDATE " . self::TABLE_NAME .
            " SET " . self::COLUMN_SESSION . " = '" . $session_key . "'" .
            " WHERE " . self::COLUMN_USERNAME . " = '" . $username . "'";

        // Execute query
        return ($db_helper->connection->query($query) !== TRUE) ? false : true;
    }

    /**
     * Update users permissions
     * @param string $username User's username
     * @param array $permissions Array containing new permissions
     * @return bool Whether the update process was successful
     */
    public static function updatePermissions($username, $permissions)
    {
        $db_helper = DbHelper::instance();

        // Escape special characters from username
        $username = $db_helper->connection->real_escape_string($username);

        // Build sql query string
        $query = "UPDATE " . self::TABLE_NAME .
            " SET " .
            self::COLUMN_WRITER . " = " . $permissions[self::COLUMN_WRITER] . ", " .
            self::COLUMN_MODERATOR . " = " . $permissions[self::COLUMN_MODERATOR] . ", " .
            self::COLUMN_ADMIN . " = " . $permissions[self::COLUMN_ADMIN] .
            " WHERE " . self::COLUMN_USERNAME . " = '" . $username . "'";

        // Execute query
        return ($db_helper->connection->query($query) !== TRUE) ? false : true;
    }

    /**
     * Update user data
     * @param string $username User's username
     * @param array $data Array containing new data
     * @return bool Whether the update process was successful
     */
    public static function update($username, $data)
    {
        if (empty($data)) {
            return true;
        }

        $db_helper = DbHelper::instance();

        // Escape special characters from username
        $username = $db_helper->connection->real_escape_string($username);

        // Build sql query string
        $query = "UPDATE " . self::TABLE_NAME . " SET ";

        foreach ($data as $key => $value) {
            $value = $db_helper->connection->real_escape_string($value);
            $query .= $key . " = '" . $value . "', ";
        }
        $query = rtrim($query, ', ');
        $query .= " WHERE " . self::COLUMN_USERNAME . " = '" . $username . "'";

        // Execute query
        return ($db_helper->connection->query($query) !== TRUE) ? false : true;
    }

    /**
     * Delete a user fromn the database
     * @param string $username User's username
     * @return bool Whether the deletion was successful
     */
    public static function delete($username)
    {
        $db_helper = DbHelper::instance();

        // Escape special characters from username
        $username = $db_helper->connection->real_escape_string($username);

        // Build sql query string
        $query = "DELETE FROM " . self::TABLE_NAME . " WHERE " . self::COLUMN_USERNAME . " = '" . $username . "'";

        // Execute query
        return ($db_helper->connection->query($query) !== TRUE) ? false : true;
    }

    /**
     * Check if exists a user with the given username
     * @param $username
     * @return bool
     */
    public static function existsUsername($username)
    {
        $db_helper = DbHelper::instance();

        // Escape special characters from username
        $username = $db_helper->connection->real_escape_string($username);

        // Build sql query string
        $query = "SELECT * FROM " . self::TABLE_NAME . " WHERE " . self::COLUMN_USERNAME . " = '" . $username . "'";

        return !is_null(self::getUserFromDb($db_helper, $query));
    }

    /**
     * Check if exists a user with the given email
     * @param $email
     * @return bool
     */
    public static function existsEmail($email)
    {
        $db_helper = DbHelper::instance();

        // Escape special characters from email
        $email = $db_helper->connection->real_escape_string($email);

        // Build sql query string
        $query = "SELECT * FROM " . self::TABLE_NAME . " WHERE " . self::COLUMN_EMAIL . " = '" . $email . "'";

        return !is_null(self::getUserFromDb($db_helper, $query));
    }
}
