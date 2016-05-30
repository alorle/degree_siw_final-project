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
use Exception;

class Forum
{
    const TABLE_NAME = 'final_forums';

    const COLUMN_ID = 'id';
    const COLUMN_NAME = 'name';
    const COLUMN_DESCRIPTION = 'description';
    const COLUMN_PARENT_FORUM_ID = 'parent_forum';

    private $id;
    private $name;
    private $description;
    private $parent_forum;

    public function __construct($row)
    {
        $this->id = $row[self::COLUMN_ID];
        $this->name = $row[self::COLUMN_NAME];
        $this->description = $row[self::COLUMN_DESCRIPTION];
        $this->parent_forum = $row[self::COLUMN_PARENT_FORUM_ID];
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getParentForum()
    {
        return $this->parent_forum;
    }

    /**
     * Get count all of forums from database
     * @return int Number of Forums
     * @throws \Exception
     */
    public static function countAll()
    {
        return count(self::getAll());
    }

    /**
     * Get count all parents of forums from database
     * @return int Number of Forums
     * @throws \Exception
     */
    public static function countAllParents()
    {
        return count(self::getAllParents());
    }

    /**
     * Get all forums from database
     * @param int $limit Number of Forums
     * @param int $offset First Forum you want
     * @return array Array containing all the Forums
     * @throws \Exception
     */
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
        $query = "SELECT * FROM " . self::TABLE_NAME;
        if (isset($limit) && $limit != 0) {
            $query .= " LIMIT " . $limit;
            if (isset($offset) && $offset != 0) {
                $query .= " OFFSET " . $offset;
            }
        }

        // Initialize array of Forums.
        $results_array = array();

        // For each query result we include a new forum in the array.
        foreach ($db_helper->query($query) as $index => $row) {
            $results_array[$index] = new Forum($row);
        }

        return $results_array;
    }

    /**
     * Get all parents forums from database
     * @param int $limit Number of Forums
     * @param int $offset First Forum you want
     * @return array Array containing all the Forums
     * @throws \Exception
     */
    public static function getAllParents($limit = 0, $offset = 0)
    {
        $db_helper = DbHelper::instance();

        if ($limit < 0) {
            $limit = 0;
        }

        if ($offset < 0) {
            $offset = 0;
        }

        // Build sql query string
        $query = "SELECT * FROM " . self::TABLE_NAME . " WHERE " . self::COLUMN_PARENT_FORUM_ID . " IS NULL";
        if (isset($limit) && $limit != 0) {
            $query .= " LIMIT " . $limit;
            if (isset($offset) && $offset != 0) {
                $query .= " OFFSET " . $offset;
            }
        }

        // Initialize array of Forums.
        $results_array = array();

        // For each query result we include a new forum in the array.
        foreach ($db_helper->query($query) as $index => $row) {
            $results_array[$index] = new Forum($row);
        }

        return $results_array;
    }

    /**
     * Get all parents forums from database
     * @param int $limit Number of Forums
     * @param int $offset First Forum you want
     * @return array Array containing all the Forums
     * @throws \Exception
     */
    public static function getAllParentsJSON($limit = 0, $offset = 0)
    {
        $parents = Forum::getAllParents($limit, $offset);
        $parentsJson = '[';
        foreach ($parents as $index => $parent) {
            if ($parentsJson != '[') {
                $parentsJson .= ', ';
            }
            $parentJson = '{';
            foreach ($parent as $key => $value) {
                if ($parentJson != '{') {
                    $parentJson .= ', ';
                }
                $parentJson .= '"' . $key . '": "' . $value . '"';
            }
            $parentJson .= '}';
            $parentsJson .= $parentJson;

        }
        $parentsJson .= ']';

        return $parentsJson;
    }

    public static function getAllChildren($parent_id, $limit = 0, $offset = 0)
    {
        $db_helper = DbHelper::instance();

        // Escape special characters from id
        $parent_id = $db_helper->connection->real_escape_string($parent_id);

        if ($limit < 0) {
            $limit = 0;
        }

        if ($offset < 0) {
            $offset = 0;
        }

        // Build sql query string
        $query = "SELECT * FROM " . self::TABLE_NAME .
            " WHERE " . self::COLUMN_PARENT_FORUM_ID . " = '" . $parent_id . "'";
        if (isset($limit) && $limit != 0) {
            $query .= " LIMIT " . $limit;
            if (isset($offset) && $offset != 0) {
                $query .= " OFFSET " . $offset;
            }
        }

        // Initialize array of Forums.
        $results_array = array();

        // For each query result we include a new forum in the array.
        foreach ($db_helper->query($query) as $index => $row) {
            $results_array[$index] = new Forum($row);
        }

        return $results_array;
    }

    public static function getAllChildrenJSON($parent_id, $limit = 0, $offset = 0)
    {
        $children = Forum::getAllChildren($parent_id, $limit, $offset);
        $childrenJson = '[';
        foreach ($children as $index => $child) {
            if ($childrenJson != '[') {
                $childrenJson .= ', ';
            }
            $childJson = '{';
            foreach ($child as $key => $value) {
                if ($childJson != '{') {
                    $childJson .= ', ';
                }
                $childJson .= '"' . $key . '": "' . $value . '"';
            }
            $childJson .= '}';
            $childrenJson .= $childJson;

        }
        $childrenJson .= ']';

        return $childrenJson;
    }

    /**
     * Get forum with given id from database
     * @param string $id Forum id
     * @return Forum|null The requested Forum if exists. If not, return null
     */
    public static function getById($id)
    {
        $db_helper = DbHelper::instance();

        // Escape special characters from id
        $id = $db_helper->connection->real_escape_string($id);

        // Build sql query string
        $query = "SELECT * FROM " . self::TABLE_NAME . " WHERE " .
            self::COLUMN_ID . " = '" . $id . "'";

        // Execute query
        $result = $db_helper->query($query);

        // We return an forum only if the result is unique
        return (count($result) == 1) ? new Forum($result[0]) : null;
    }

    public static function insert($array)
    {
        $db_helper = DbHelper::instance();

        // Build sql query string
        $query = "INSERT INTO " . self::TABLE_NAME . " (" .
            self::COLUMN_ID . ", " .
            self::COLUMN_NAME . ", " .
            self::COLUMN_DESCRIPTION;

        if (isset($array[self::COLUMN_PARENT_FORUM_ID])) {
            $query .= ", " . self::COLUMN_PARENT_FORUM_ID;
        }

        $query .= ") VALUES (" .
            "'" . $array[self::COLUMN_ID] . "', " .
            "'" . $array[self::COLUMN_NAME] . "', " .
            "'" . $array[self::COLUMN_DESCRIPTION];


        if (isset($array[self::COLUMN_PARENT_FORUM_ID])) {
            $query .= "', '" . $array[self::COLUMN_PARENT_FORUM_ID];
        }

        $query .= "')";

        // Execute query
        return ($db_helper->connection->query($query) === TRUE);
    }

    public static function existsId($id)
    {
        return !is_null(self::getById($id));
    }
}
