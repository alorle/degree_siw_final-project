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
     * @var string Name of the file containing the page header
     */
    private $file_header;

    /**
     * @var string Name of the file containing the page footer
     */
    private $file_footer;

    /**
     * @var string Name of the file containing the page template
     */
    private $file_template;

    public function __construct()
    {
        $this->setFileHeader(PROJECT_TEMPLATES_PARTS_PATH . DIRECTORY_SEPARATOR . 'part_header.html');
        $this->setFileFooter(PROJECT_TEMPLATES_PARTS_PATH . DIRECTORY_SEPARATOR . 'part_footer.html');
    }

    /**
     * Renders the page
     * @return string
     * @throws \Exception
     */
    public function render()
    {
        if (!file_exists($this->getFileTemplate())) {
            throw new \Exception("Internal server error", 500);
        }

        $template = file_get_contents($this->getFileTemplate());
        $template = str_replace(self::KEY_TITLE, $this->getTitle(), $template);
        $template = str_replace(self::KEY_HEADER, $this->readHeader(), $template);
        $template = str_replace(self::KEY_FOOTER, $this->readFooter(), $template);

        return $template;
    }

    /**
     * Read header part file and returns it's content
     * @return string
     */
    private function readHeader()
    {
        return file_get_contents($this->getFileHeader());
    }

    /**
     * Read footer part file and returns it's content
     * @return string
     */
    private function readFooter()
    {
        return file_get_contents($this->getFileFooter());
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getFileTemplate()
    {
        return $this->file_template;
    }

    public function setFileTemplate($file)
    {
        if (!file_exists($file)) {
            throw new \Exception("Internal server error", 500);
        }
        $this->file_template = $file;
    }

    public function getFileHeader()
    {
        return $this->file_header;
    }

    public function setFileHeader($file)
    {
        if (!file_exists($file)) {
            throw new \Exception("Internal server error", 500);
        }
        $this->file_header = $file;
    }

    public function getFileFooter()
    {
        return $this->file_footer;
    }

    public function setFileFooter($file)
    {
        if (!file_exists($file)) {
            throw new \Exception("Internal server error", 500);
        }
        $this->file_footer = $file;
    }
}
