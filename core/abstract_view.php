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
     * @var AbstractPartialView Partial view for header
     */
    private $headerView;

    /**
     * @var AbstractPartialView Partial view for footer
     */
    private $footerView;

    /**
     * AbstractView constructor.
     * @param $headerView AbstractPartialView
     * @param $footerView AbstractPartialView
     */
    public function __construct($headerView = null, $footerView = null)
    {
        $this->title = PROJECT_NAME;
        $this->file_head = PROJECT_TEMPLATES_PARTS_PATH . DIRECTORY_SEPARATOR . 'part_head.html';
        $this->headerView = $headerView;
        $this->footerView = $footerView;
    }

    /**
     * Renders the page
     * @return string
     * @throws \Exception
     */
    public function render()
    {
        // Check that the template file exists
        if (!file_exists($this->file_template)) {
            throw new \Exception("Internal server error", 500);
        }

        // Get page template
        $template = file_get_contents($this->file_template);

        // Replace HEAD, HEADER and FOOTER
        $template = str_replace(self::KEY_HEAD, $this->readHeadFile(), $template);
        $template = str_replace(self::KEY_HEADER, $this->renderHeader(), $template);
        $template = str_replace(self::KEY_FOOTER, $this->renderFooter(), $template);

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
     * Render header view
     * @return string Page header string or empty string if headerView is not defined
     */
    private function renderHeader()
    {
        if (isset($this->headerView)) {
            return $this->headerView->render();
        }

        return '';
    }

    /**
     * Render footer view
     * @return string Page footer string or empty string if headerView is not defined
     */
    private function renderFooter()
    {
        if (isset($this->footerView)) {
            return $this->footerView->render();
        }

        return '';
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setHeadFile($file)
    {
        $this->file_head = $file;
    }

    public function setTemplateFile($file)
    {
        $this->file_template = $file;
    }
}
