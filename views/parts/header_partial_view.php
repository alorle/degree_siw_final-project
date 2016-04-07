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
use utils\Session;

class HeaderPartialView extends AbstractPartialView
{
    const KEY_SECTION_ACCESS = '##ACCESS_SECTION##';
    const KEY_SECTION_USER = '##USER_SECTION##';
    const KEY_SECTION_SIGN_IN = '##SIGN_UP_SECTION##';
    const KEY_SECTION_LOGIN = '##LOGIN_SECTION##';

    const KEY_USER_NAME = '##USER_NAME##';

    /**
     * @var string Name of the file containing the header template
     */
    private $file_template;

    /**
     * @var boolean Indicates whether to display the access section in this header or not
     */
    private $show_section_access;

    /**
     * @var boolean Indicates whether to display the sign in section in this header or not
     */
    private $show_section_sign_in;

    /**
     * @var boolean Indicates whether to display the login section in this header or not
     */
    private $show_section_login;

    public function __construct($show_section_access = true,
                                $show_section_sign_in = true,
                                $show_section_login = true)
    {
        $this->file_template = PROJECT_TEMPLATES_PARTS_PATH . DIRECTORY_SEPARATOR . 'part_header.html';

        $this->show_section_access = $show_section_access;
        $this->show_section_sign_in = $show_section_sign_in;
        $this->show_section_login = $show_section_login;
    }

    public function render()
    {
        // Check that the template file exists
        if (!file_exists($this->file_template)) {
            throw new \Exception("Internal server error", 500);
        }

        // Get header template
        $template = file_get_contents($this->file_template);

        if (!$this->show_section_access) {
            $template_parts = explode(self::KEY_SECTION_ACCESS, $template);
            $template = $template_parts[0] . $template_parts[2];
        } else {
            $template = str_replace(self::KEY_SECTION_ACCESS, '', $template);

            if (Session::checkUserSession()) {
                // Delete user section key
                $template = str_replace(self::KEY_SECTION_USER, '', $template);

                // Set user data as user is logged in
                // TODO: replace KEY_USER_ID with real user id
                $template = str_replace(self::KEY_USER_NAME, Session::getUserName(), $template);

                // Disable sign up and login sections as user is logged in
                $this->show_section_sign_in = false;
                $this->show_section_login = false;
            } else {
                // No user is logged in, so delete user section
                $template_parts = explode(self::KEY_SECTION_USER, $template);
                $template = $template_parts[0] . $template_parts[2];
            }

            if (!$this->show_section_sign_in) {
                $template_parts = explode(self::KEY_SECTION_SIGN_IN, $template);
                $template = $template_parts[0] . $template_parts[2];
            } else {
                $template = str_replace(self::KEY_SECTION_SIGN_IN, '', $template);
            }

            if (!$this->show_section_login) {
                $template_parts = explode(self::KEY_SECTION_LOGIN, $template);
                $template = $template_parts[0] . $template_parts[2];
            } else {
                $template = str_replace(self::KEY_SECTION_LOGIN, '', $template);
            }
        }

        return $template;
    }
}
