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

class Article
{
    const TABLE_NAME = 'articles';

    const COLUMN_ID = 'id';
    const COLUMN_TITLE = 'title';
    const COLUMN_BODY = 'body';
    const COLUMN_AUTHOR_ID = 'author_id';
    const COLUMN_TIME = 'time';

    private $id;
    private $title;
    private $body;
    private $author_id;
    private $time;

    public function __construct($row)
    {
        $this->id = $row[self::COLUMN_ID];
        $this->title = $row[self::COLUMN_TITLE];
        $this->body = $row[self::COLUMN_BODY];
        $this->author_id = $row[self::COLUMN_AUTHOR_ID];
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

    public function getBody()
    {
        return $this->body;
    }

    public function getAuthorId()
    {
        return $this->author_id;
    }

    public function getTime()
    {
        return $this->time;
    }

    /**
     * @return string Name of the author of this article
     */
    public function getAuthorName()
    {
        // TODO: Return the real name of the author based on $author_id
        return "Aristóteles";
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
     * @return array Array containing all the articles
     * @throws \Exception
     */
    public static function getAll()
    {
        $db_helper = DbHelper::instance();

        // Build sql query string
        $sql = "SELECT * FROM " . self::TABLE_NAME;

        // Initialize array of articles.
        $results_array = array();

        // For each query result we include a new article in the array.
        foreach ($db_helper->query($sql) as $index => $row) {
            $results_array[$index] = new Article($row);
        }

        return $results_array;
    }

    /**
     * Get a set of consecutive articles
     * @return array Array of consecutive articles
     * @throws \Exception
     */
    public static function getRangeById($first_id, $last_id)
    {
        $db_helper = DbHelper::instance();

        // Build sql query string
        $sql = "SELECT * FROM " . self::TABLE_NAME . " WHERE id >= " . $first_id . " AND id < " . $last_id;

        // Initialize array of articles.
        $results_array = array();

        // For each query result we include a new article in the array.
        foreach ($db_helper->query($sql) as $index => $row) {
            $results_array[$index] = new Article($row);
        }

        return $results_array;
    }

    /**
     * Get an article with the given id
     * @param int $id Id of the article
     * @return Article|null The requested article if exists. If not, return null
     * @throws \Exception
     */
    public static function getById($id)
    {
        $db_helper = DbHelper::instance();

        // Sanitize $id and filter it as an int
        $id = $db_helper->real_escape_string($id);
        if (!$id = filter_var($id, FILTER_VALIDATE_INT)) {
            return null;
        }

        // Build sql query string
        $sql = "SELECT * FROM " . self::TABLE_NAME . " WHERE " . self::COLUMN_ID . " = " . $id;

        // Execute query
        $results_array = $db_helper->query($sql);

        // We return an article only if the result is unique
        if (count($results_array) == 1) {
            return new Article($results_array[0]);
        }

        return null;
    }
}
