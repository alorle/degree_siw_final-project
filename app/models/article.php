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

class Article
{
    const TABLE_NAME = 'articles';

    const COLUMN_ID = 'id';
    const COLUMN_TITLE = 'title';
    const COLUMN_BODY = 'body';
    const COLUMN_AUTHOR_NAME = 'author_name';
    const COLUMN_AUTHOR_USERNAME = 'author_username';
    const COLUMN_TIME = 'time';

    private $id;
    private $title;
    private $summary;
    private $body;
    private $author_name;
    private $author_username;
    private $time;

    public function __construct($row)
    {
        $this->id = $row[self::COLUMN_ID];
        $this->title = $row[self::COLUMN_TITLE];
        $this->summary = substr($row[self::COLUMN_BODY], 0, 500) . ' ...';
        $this->body = $row[self::COLUMN_BODY];
        $this->author_name = $row[self::COLUMN_AUTHOR_NAME];
        $this->author_username = $row[self::COLUMN_AUTHOR_USERNAME];
        $this->time = $row[self::COLUMN_TIME];
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getSummary()
    {
        return $this->summary;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getAuthorName()
    {
        return $this->author_name;
    }

    public function getAuthorUsername()
    {
        return $this->author_username;
    }

    public function getTime()
    {
        return $this->time;
    }

    /**
     * Get count of articles from database
     * @return int Number of articles
     * @throws \Exception
     */
    public static function count()
    {
        return count(self::getAll());
    }

    /**
     * Get all articles from database
     * @param int $limit Number of articles
     * @param int $offset First article you want
     * @return array Array containing all the articles
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
        $sql = "SELECT * FROM " . self::TABLE_NAME . " ORDER BY " . self::COLUMN_TIME . " DESC";
        if (isset($limit) && $limit != 0) {
            $sql .= " LIMIT " . $limit;
            if (isset($offset) && $offset != 0) {
                $sql .= " OFFSET " . $offset;
            }
        }

        // Initialize array of articles.
        $results_array = array();

        // For each query result we include a new article in the array.
        foreach ($db_helper->query($sql) as $index => $row) {
            $results_array[$index] = new Article($row);
        }

        return $results_array;
    }
}
