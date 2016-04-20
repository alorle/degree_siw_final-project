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
namespace Core;


use Interfaces\ViewInterface;

abstract class AbstractView implements ViewInterface
{
    /**
     * @var string Page title
     */
    private $title;

    /**
     * @var string Name of the file containing the page head
     */
    private $file_head;

    /**
     * @var string Name of the file containing the page template
     */
    private $file_template;

    /**
     * AbstractView constructor.
     */
    public function __construct()
    {
        $this->title = PROJECT_NAME;
        $this->file_head = FOLDER_TEMPLATES . DIRECTORY_SEPARATOR . 'part_head.html';
    }

    /**
     * Renders the view
     * @return string
     * @throws \Exception
     */
    public function render()
    {
        // Check that the template file exists
        if (!file_exists($this->file_template)) {
            throw new \Exception('Template does not exist', 500);
        }

        // Get page template
        $template = file_get_contents($this->file_template);

        // Replace HEAD
        $template = str_replace(self::KEY_HEAD, $this->readHeadFile(), $template);

        // Set page title
        $template = str_replace(self::KEY_TITLE, $this->title, $template);

        // Return rendered page, as this class does not display anything
        return $template;
    }

    /**
     * Read head file
     * @return string Head file content or empty string if head file is not defined
     */
    private function readHeadFile()
    {
        if (isset($this->file_head) && file_exists($this->file_head)) {
            return file_get_contents($this->file_head);
        }
        return '';
    }

    /**
     * Set page title
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Set name of the file containing the page head
     * @param string $file
     */
    public function setHeadFile($file)
    {
        $this->file_head = $file;
    }

    /**
     * Set name of the file containing the page template
     * @param string $file
     */
    public function setTemplateFile($file)
    {
        $this->file_template = $file;
    }
}
