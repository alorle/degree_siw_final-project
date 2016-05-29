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

class Image
{
    const TABLE_NAME = 'images';

    const COLUMN_ID = 'id';
    const COLUMN_FILE_NAME = 'file_name';
    const COLUMN_ARTICLE_ID = 'article_id';

    private $id;
    private $file_name;
    private $article_id;

    private static $sizes = array('small' => 250, 'medium' => 500, 'large' => 1000);
    private static $valid_ext = array("jpeg", "jpg", "png");

    public function __construct($row)
    {
        $this->id = $row[self::COLUMN_ID];
        $this->file_name = $row[self::COLUMN_FILE_NAME];
        $this->article_id = $row[self::COLUMN_ARTICLE_ID];
    }

    public function getId()
    {
        return $this->id;
    }

    public function getFileName()
    {
        return $this->file_name;
    }

    public function getArticleId()
    {
        return $this->article_id;
    }

    public static function getAll($article_id)
    {
        $db_helper = DbHelper::instance();

        // Escape special characters from article_id
        $article_id = $db_helper->connection->real_escape_string($article_id);

        // Build sql query string
        $query = "SELECT * FROM " . self::TABLE_NAME .
            " WHERE " . self::COLUMN_ARTICLE_ID . " = '" . $article_id . "'" .
            " ORDER BY " . self::COLUMN_FILE_NAME . " ASC";

        // Initialize array of comments.
        $results_array = array();

        // For each query result we include a new iamge in the array.
        foreach ($db_helper->query($query) as $index => $row) {
            $results_array[$index] = new Image($row);
        }

        return $results_array;
    }

    public static function getAllUrlsJSON($article_id)
    {
        $images = Image::getAll($article_id);
        $imagesJson = '[';
        foreach ($images as $index => $image) {
            if ($imagesJson != '[') {
                $imagesJson .= ', ';
            }
            $imageJson = '{';
            $file_name = $image->getFileName();
            foreach (Image::$sizes as $size_name => $dimension) {
                if ($imageJson != '{') {
                    $imageJson .= ', ';
                }
                $value = PROJECT_BLOG_IMAGES . $size_name . '/' . $file_name;
                $imageJson .= '"' . $size_name . '": "' . $value . '"';
            }
            $imageJson .= '}';
            $imagesJson .= $imageJson;

        }
        $imagesJson .= ']';

        return $imagesJson;
    }

    public static function insert($array)
    {
        $db_helper = DbHelper::instance();

        // Build sql query string
        $query = "INSERT INTO " . self::TABLE_NAME . " (" .
            self::COLUMN_FILE_NAME . ", " .
            self::COLUMN_ARTICLE_ID .
            ") VALUES (" .
            "'" . $array[self::COLUMN_FILE_NAME] . "', " .
            "'" . $array[self::COLUMN_ARTICLE_ID] . "')";

        // Execute query
        return ($db_helper->connection->query($query) === TRUE);
    }

    public static function resizeSaveInsert($key, $file_tmp, $file_name, $article_id)
    {
        // Get image extension
        $ext = pathinfo($file_name, PATHINFO_EXTENSION);

        if (in_array($ext, Image::$valid_ext)) {
            // Define new file's name
            $new_file_name = $article_id . '_' . $key . '_' . time() . '.' . $ext;

            // Read image content
            if (in_array($ext, array("jpeg", "jpg"))) {
                $src = imagecreatefromjpeg($file_tmp);
            } else {
                $src = imagecreatefrompng($file_tmp);
            }

            // Get image ratio
            list($width, $height) = getimagesize($file_tmp);
            $ratio = $width / $height;

            $result = true;
            foreach (Image::$sizes as $size_name => $dimension) {
                if ($ratio > 1) {
                    $new_width = $dimension;
                    $new_height = $dimension / $ratio;
                } else {
                    $new_width = $dimension * $ratio;
                    $new_height = $dimension;
                }

                $target = FOLDER_BLOG_IMAGES . DIRECTORY_SEPARATOR . $size_name . DIRECTORY_SEPARATOR . $new_file_name;

                $dst = imagecreatetruecolor($new_width, $new_height);

                imagecopyresampled($dst, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

                if (in_array($ext, array("jpeg", "jpg"))) {
                    $result &= imagejpeg($dst, $target);
                } else {
                    $result &= imagepng($dst, $target);
                }

                imagedestroy($dst);
            }

            imagedestroy($src);

            $result &= Image::insert(array(
                Image::COLUMN_FILE_NAME => $new_file_name,
                Image::COLUMN_ARTICLE_ID => $article_id
            ));

            return $result;
        }

        return false;
    }
}
