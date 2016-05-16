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

    /**
     * AdminProfileView constructor.
     * @param User $user
     */
    public function __construct($user)
    {
        parent::__construct(self::ACTIVE_ADMIN, $user, new HeaderPartial(), new FooterPartial());
        $this->setTemplateFile(FOLDER_TEMPLATES . DIRECTORY_SEPARATOR . 'profile' . DIRECTORY_SEPARATOR . 'admin.html');
        $this->setTitle($user->getName() . ' | ' . PROJECT_NAME);
    }

    public function render()
    {
        $template = parent::render();

        $users = User::getAll();
        $template_parts = explode(self::KEY_ADMIN_TABLE, $template);
        if (!is_null($users) && count($users) > 0) {
            $template = $template_parts[0] . $template_parts[1] . $template_parts[2];

            $template_parts = explode(self::KEY_ADMIN_TABLE_ROW, $template);
            $template_users = '';
            foreach ($users as $user) {
                $template_users .= $this->replaceUser($template_parts[1], $user);
            }
            $template = $template_parts[0] . $template_users . $template_parts[2];
        } else {
            $template = $template_parts[0] . $template_parts[2];
        }

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
}
