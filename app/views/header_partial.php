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
namespace App\Views;

use App\Core\AbstractPartial;
use App\Models\Session;

class HeaderPartial extends AbstractPartial
{
    const KEY_SECTION_SESSION = "##SECTION_SESSION##";

    const KEY_USERNAME = "##USERNAME##";

    /**
     * @var string Name of the file containing the header template
     */
    private $file_template;

    public function __construct()
    {
        $this->file_template = FOLDER_TEMPLATES . DIRECTORY_SEPARATOR . 'part_header.html';
    }

    public function render()
    {
        // Check that the template file exists
        if (!file_exists($this->file_template)) {
            throw new \Exception('Template does not exist', 500);
        }

        // Get header template
        $template = file_get_contents($this->file_template);

        $template_parts = explode(self::KEY_SECTION_SESSION, $template);
        if (!is_null($user = Session::getCurrentUser())) {
            $template = $template_parts[0] . $template_parts[1] . $template_parts[3];
            $template = str_replace(self::KEY_USERNAME, $user->getName(), $template);
        } else {
            $template = $template_parts[0] . $template_parts[2] . $template_parts[3];
        }

        return $template;
    }
}
