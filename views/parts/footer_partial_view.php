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

namespace views\parts;

use core\AbstractPartialView;

class FooterPartialView extends AbstractPartialView
{
    /**
     * @var string Name of the file containing the footer template
     */
    private $file_template;

    public function __construct()
    {
        $this->file_template = PROJECT_TEMPLATES_PARTS_PATH . DIRECTORY_SEPARATOR . 'part_footer.html';
    }

    public function render()
    {
        // Check that the template file exists
        if (!file_exists($this->file_template)) {
            throw new \Exception("Internal server error", 500);
        }

        // Get footer template
        $template = file_get_contents($this->file_template);

        return $template;
    }
}
