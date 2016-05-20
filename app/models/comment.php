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

class Comment
{
    const TABLE_NAME = 'comments';

    const COLUMN_ID = 'id';
    const COLUMN_TITLE = 'title';
    const COLUMN_BODY = 'body';
    const COLUMN_TIME = 'time';
    const COLUMN_THREAD_ID = 'thread_id';
    const COLUMN_AUTHOR_ID = 'author_id';

    private $id;
    private $title;
    private $body;
    private $time;
    private $thread_id;
    private $author_id;

    public function __construct($row)
    {
        $this->id = $row[self::COLUMN_ID];
        $this->title = $row[self::COLUMN_TITLE];
        $this->body = $row[self::COLUMN_BODY];
        $this->time = $row[self::COLUMN_TIME];
        $this->thread_id = $row[self::COLUMN_THREAD_ID];
        $this->author_id = $row[self::COLUMN_AUTHOR_ID];
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getTime()
    {
        return $this->time;
    }

    public function getThreadId()
    {
        return $this->thread_id;
    }

    public function getAuthorId()
    {
        return $this->author_id;
    }

    /**
     * Get count of comments from database
     * @param string $thread_id
     * @param string $author_id
     * @return int Number of comments
     */
    public static function count($thread_id = '', $author_id = '')
    {
        return count(self::getAll($thread_id, $author_id));
    }

    /**
     * Get all comments from database
     * @param string $thread_id
     * @param string $author_id
     * @param int $limit Number of articles
     * @param int $offset First article you want
     * @return array Array containing all the articles
     * @throws \Exception
     */
    public static function getAll($thread_id = '', $author_id = '', $limit = 0, $offset = 0)
    {
        $db_helper = DbHelper::instance();

        // Escape special characters from thread_id and author_id
        $thread_id = $db_helper->connection->real_escape_string($thread_id);
        $author_id = $db_helper->connection->real_escape_string($author_id);

        if ($limit < 0) {
            $limit = 0;
        }

        if ($offset < 0) {
            $offset = 0;
        }

        // Build sql query string
        $sql = "SELECT * FROM " . self::TABLE_NAME;

        if (!empty($thread_id) && !empty($author_id)) {
            $sql .= " WHERE " . self::COLUMN_THREAD_ID . " = '" . $thread_id . "'" .
                " AND " . self::COLUMN_AUTHOR_ID . " = '" . $author_id . "'";
        } elseif (!empty($thread_id)) {
            $sql .= " WHERE " . self::COLUMN_THREAD_ID . " = '" . $thread_id . "'";
        } elseif (!empty($author_id)) {
            $sql .= " WHERE " . self::COLUMN_AUTHOR_ID . " = '" . $author_id . "'";
        }

        $sql .= " ORDER BY " . self::COLUMN_TIME . " DESC";

        if (isset($limit) && $limit != 0) {
            $sql .= " LIMIT " . $limit;
            if (isset($offset) && $offset != 0) {
                $sql .= " OFFSET " . $offset;
            }
        }

        // Initialize array of comments.
        $results_array = array();

        // For each query result we include a new comment in the array.
        foreach ($db_helper->query($sql) as $index => $row) {
            $results_array[$index] = new Comment($row);
        }

        return $results_array;
    }
}
