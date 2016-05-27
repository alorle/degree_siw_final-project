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
    const COLUMN_AUTHOR_ID = 'author_id';
    const COLUMN_TIME = 'time';

    private $id;
    private $title;
    private $summary;
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

        $this->summary = substr($this->body, 0, stripos($this->body, PHP_EOL, stripos($this->body, PHP_EOL) + 1) - 3) .
            ' ...';
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

    public function getAuthorId()
    {
        return $this->author_id;
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
        $query = "SELECT * FROM " . self::TABLE_NAME . " ORDER BY " . self::COLUMN_TIME . " DESC";
        if (isset($limit) && $limit != 0) {
            $query .= " LIMIT " . $limit;
            if (isset($offset) && $offset != 0) {
                $query .= " OFFSET " . $offset;
            }
        }

        // Initialize array of articles.
        $results_array = array();

        // For each query result we include a new article in the array.
        foreach ($db_helper->query($query) as $index => $row) {
            $results_array[$index] = new Article($row);
        }

        return $results_array;
    }

    /**
     * Get article with given id from database
     * @param string $id Article id
     * @return Article|null The requested article if exists. If not, return null
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

        // We return an article only if the result is unique
        return (count($result) == 1) ? new Article($result[0]) : null;
    }

    /**
     * Get all articles written by given author
     * @param string $author_id Author id
     * @param int $limit Number of articles
     * @param int $offset First article you want
     * @return array Array containing all the articles written by given author
     */
    public static function getByAuthorId($author_id, $limit = 0, $offset = 0)
    {
        $db_helper = DbHelper::instance();

        // Escape special characters from author_id
        $author_id = $db_helper->connection->real_escape_string($author_id);

        if ($limit < 0) {
            $limit = 0;
        }

        if ($offset < 0) {
            $offset = 0;
        }

        // Build sql query string
        $query = "SELECT * FROM " . self::TABLE_NAME .
            " WHERE " . self::COLUMN_AUTHOR_ID . " = '" . $author_id . "'" .
            " ORDER BY " . self::COLUMN_TIME . " DESC";
        if (isset($limit) && $limit != 0) {
            $query .= " LIMIT " . $limit;
            if (isset($offset) && $offset != 0) {
                $query .= " OFFSET " . $offset;
            }
        }

        // Initialize array of articles.
        $results_array = array();

        // For each query result we include a new article in the array.
        foreach ($db_helper->query($query) as $index => $row) {
            $results_array[$index] = new Article($row);
        }

        return $results_array;
    }

    /**
     * Insert a new article in the database
     * @param array $article New article data to insert in database
     * @return bool Whether the insertion was successful
     */
    public static function insert($article)
    {
        $db_helper = DbHelper::instance();

        // Build sql query string
        $query = "INSERT INTO " . self::TABLE_NAME . " (" .
            self::COLUMN_ID . ", " .
            self::COLUMN_TITLE . ", " .
            self::COLUMN_BODY . ", " .
            self::COLUMN_AUTHOR_ID .
            ") VALUES (" .
            "'" . $article[self::COLUMN_ID] . "', " .
            "'" . $article[self::COLUMN_TITLE] . "', " .
            "'" . $article[self::COLUMN_BODY] . "', " .
            "'" . $article[self::COLUMN_AUTHOR_ID] . "')";

        // Execute query
        return ($db_helper->connection->query($query) === TRUE);
    }

    /**
     * Update an article in the database
     * @param string $id Article ID
     * @param string $title New article's title
     * @param string $body New article's body
     * @return bool Whether the update was successful
     */
    public static function updateTitleAndBody($id, $title, $body)
    {
        $db_helper = DbHelper::instance();

        // Escape special characters from article_id
        $id = $db_helper->connection->real_escape_string($id);
        $title = $db_helper->connection->real_escape_string($title);
        $body = $db_helper->connection->real_escape_string($body);

        // Build sql query string
        $query = "UPDATE " . self::TABLE_NAME .
            " SET " .
            self::COLUMN_TITLE . " = '" . $title . "', " .
            self::COLUMN_BODY . " = '" . $body . "'" .
            " WHERE " . self::COLUMN_ID . " = '" . $id . "'";

        // Execute query
        return ($db_helper->connection->query($query) === TRUE);
    }

    /**
     * Delete an article in the database
     * @param string $id Article ID
     * @return bool Whether the insertion was successful
     */
    public static function delete($id)
    {
        $db_helper = DbHelper::instance();

        // Escape special characters from article_id
        $id = $db_helper->connection->real_escape_string($id);

        // Build sql query string
        $query = "DELETE FROM " . self::TABLE_NAME . " WHERE " . self::COLUMN_ID . " = '" . $id . "'";

        // Execute query
        return ($db_helper->connection->query($query) === TRUE);
    }

    public static function existsId($id)
    {
        return !is_null(self::getById($id));
    }

}
