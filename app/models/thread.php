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

class Thread
{
    const TABLE_NAME = 'threads';

    const COLUMN_ID = 'id';
    const COLUMN_NAME = 'name';
    const COLUMN_TIME = 'time';
    const COLUMN_COMMENTS_COUNT = 'comments_count';
    const COLUMN_PARENT_FORUM_ID = 'forum_id';
    const COLUMN_AUTHOR_ID = 'author_id';

    private $id;
    private $name;
    private $comments_count;
    private $parent_id;
    private $author_id;

    public function __construct($row)
    {
        $this->id = $row[self::COLUMN_ID];
        $this->name = $row[self::COLUMN_NAME];
        $this->time = $row[self::COLUMN_TIME];
        $this->comments_count = $row[self::COLUMN_COMMENTS_COUNT];
        $this->parent_id = $row[self::COLUMN_PARENT_FORUM_ID];
        $this->author_id = $row[self::COLUMN_AUTHOR_ID];
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getTime()
    {
        return $this->time;
    }

    public function getCommentsCount()
    {
        return $this->comments_count;
    }

    public function getParentId()
    {
        return $this->parent_id;
    }

    public function getAuthorId()
    {
        return $this->author_id;
    }

    /**
     * Get count all of threads from database
     * @param $parent_id
     * @return int Number of Forums
     */
    public static function countAll($parent_id)
    {
        return count(self::getAll($parent_id));
    }

    /**
     * Get all forums from database
     * @param $parent_id
     * @param int $limit Number of Forums
     * @param int $offset First Forum you want
     * @return array Array containing all the Forums
     * @throws \Exception
     */
    public static function getAll($parent_id, $limit = 0, $offset = 0)
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
        $sql = "SELECT * FROM " . self::TABLE_NAME .
            " WHERE " . self::COLUMN_PARENT_FORUM_ID . " = '" . $parent_id . "'" .
            " ORDER BY " . self::COLUMN_TIME . " DESC";
        if (isset($limit) && $limit != 0) {
            $sql .= " LIMIT " . $limit;
            if (isset($offset) && $offset != 0) {
                $sql .= " OFFSET " . $offset;
            }
        }

        // Initialize array of threads.
        $results_array = array();

        // For each query result we include a new thread in the array.
        foreach ($db_helper->query($sql) as $index => $row) {
            $results_array[$index] = new Thread($row);
        }

        return $results_array;
    }
}
