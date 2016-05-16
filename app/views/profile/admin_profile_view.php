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

namespace App\Views\Profile;


use App\Interfaces\UserInterface;
use App\Models\User;
use App\Views\FooterPartial;
use App\Views\HeaderPartial;

class AdminProfileView extends AbstractProfileView implements UserInterface
{
    const KEY_CONFIRM_DELETE = '##CONFIRM_DELETE##';

    const KEY_CHECKBOX_CHECKED = "checked";

    const KEY_USER_IS_WRITER = "##USER_IS_WRITER##";
    const KEY_USER_IS_MODERATOR = "##USER_IS_MODERATOR##";
    const KEY_USER_IS_ADMIN = "##USER_IS_ADMIN##";

    private $users;
    private $total_pages;
    private $current_page;

    /**
     * AdminProfileView constructor.
     * @param User $user
     * @param $users array Array of users to show
     * @param $total_pages int Count of pages
     * @param $current_page int Current page number
     */
    public function __construct($user, $users, $total_pages = null, $current_page = null)
    {
        parent::__construct(self::ACTIVE_ADMIN, $user, new HeaderPartial(), new FooterPartial());
        $this->setTemplateFile(FOLDER_TEMPLATES . DIRECTORY_SEPARATOR . 'profile' . DIRECTORY_SEPARATOR . 'admin.html');
        $this->setTitle($user->getName() . ' | ' . PROJECT_NAME);
        $this->users = $users;
        $this->total_pages = $total_pages;
        $this->current_page = $current_page;
    }

    public function render()
    {
        $template = parent::render();

        $template_parts = explode(self::KEY_ADMIN_TABLE, $template);
        if (!is_null($this->users) && count($this->users) > 0) {
            $template = $template_parts[0] . $template_parts[1] . $template_parts[2];

            $template_parts = explode(self::KEY_ADMIN_TABLE_ROW, $template);
            $template_users = '';
            foreach ($this->users as $user) {
                $template_users .= $this->replaceUser($template_parts[1], $user);
            }
            $template = $template_parts[0] . $template_users . $template_parts[2];
        } else {
            $template = $template_parts[0] . $template_parts[2];
        }

        // Fill pagination section
        $template = $this->replacePagination($template);

        echo $template;
    }

    private function replaceUser($template, User $user)
    {
        $template = str_replace(self::KEY_USER_NAME, $user->getName(), $template);
        $template = str_replace(self::KEY_USER_USERNAME, $user->getUsername(), $template);
        if ($user->isWriter()) {
            $template = str_replace(self::KEY_USER_IS_WRITER, self::KEY_CHECKBOX_CHECKED, $template);
        } else {
            $template = str_replace(self::KEY_USER_IS_WRITER, '', $template);
        }
        if ($user->isModerator()) {
            $template = str_replace(self::KEY_USER_IS_MODERATOR, self::KEY_CHECKBOX_CHECKED, $template);
        } else {
            $template = str_replace(self::KEY_USER_IS_MODERATOR, '', $template);
        }
        if ($user->isAdmin()) {
            $template = str_replace(self::KEY_USER_IS_ADMIN, self::KEY_CHECKBOX_CHECKED, $template);
        } else {
            $template = str_replace(self::KEY_USER_IS_ADMIN, '', $template);
        }

        $confirm_delete_question = '¿Quieres eliminar a ' . $user->getName() . '?';
        $template = str_replace(self::KEY_CONFIRM_DELETE, $confirm_delete_question, $template);

        return $template;
    }

    private function replacePagination($template)
    {
        $template_parts = explode(self::KEY_PAGINATION, $template);
        if (isset($this->current_page) && isset($this->total_pages)) {
            // Pagination must be rendered
            $template = $template_parts[0] . $template_parts[1] . $template_parts[2];

            // Show first page if necessary
            $template_parts = explode(self::KEY_PAGINATION_FIRST, $template);
            if ($this->current_page <= 2) {
                $template = $template_parts[0] . $template_parts[2];
            } else {
                $template = $template_parts[0] . $template_parts[1] . $template_parts[2];
                $template = str_replace(self::KEY_PAGINATION_FIRST_ID, 1, $template);
            }

            // Show previous page if necessary
            $template_parts = explode(self::KEY_PAGINATION_PREV, $template);
            if ($this->current_page <= 1) {
                $template = $template_parts[0] . $template_parts[2];
            } else {
                $template = $template_parts[0] . $template_parts[1] . $template_parts[2];
                $template = str_replace(self::KEY_PAGINATION_PREV_ID, $this->current_page - 1, $template);
            }

            // Show current page
            $template = str_replace(self::KEY_PAGINATION_CURRENT_ID, $this->current_page, $template);

            // Show next page if necessary
            $template_parts = explode(self::KEY_PAGINATION_NEXT, $template);
            if ($this->current_page > $this->total_pages - 1) {
                $template = $template_parts[0] . $template_parts[2];
            } else {
                $template = $template_parts[0] . $template_parts[1] . $template_parts[2];
                $template = str_replace(self::KEY_PAGINATION_NEXT_ID, $this->current_page + 1, $template);
            }

            // Show last page if necessary
            $template_parts = explode(self::KEY_PAGINATION_LAST, $template);
            if ($this->current_page > $this->total_pages - 2) {
                $template = $template_parts[0] . $template_parts[2];
            } else {
                $template = $template_parts[0] . $template_parts[1] . $template_parts[2];
                $template = str_replace(self::KEY_PAGINATION_LAST_ID, $this->total_pages, $template);
            }
        } else {
            // Pagination is not necessary
            $template = $template_parts[0] . $template_parts[2];
        }

        return $template;
    }
}
