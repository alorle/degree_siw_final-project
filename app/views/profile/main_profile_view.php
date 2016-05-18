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

class MainProfileView extends AbstractProfileView implements UserInterface
{
    const KEY_MESSAGE_IMAGE = '##MESSAGE_IMAGE##';
    const KEY_MESSAGE = '##MESSAGE##';

    private $message_image;
    private $message;

    /**
     * MainProfileView constructor.
     * @param User $user
     * @param string $msg_image
     * @param string $msg
     */
    public function __construct($user, $msg_image = '', $msg = '')
    {
        parent::__construct(self::ACTIVE_MAIN, $user, new HeaderPartial(), new FooterPartial());
        $this->setTemplateFile(FOLDER_TEMPLATES . DIRECTORY_SEPARATOR . 'profile' . DIRECTORY_SEPARATOR . 'main.html');
        $this->setTitle($user->getName() . ' | ' . PROJECT_NAME);
        $this->message_image = $msg_image;
        $this->message = $msg;
    }

    public function render()
    {
        $template = parent::render();
        $template = str_replace(self::KEY_MESSAGE_IMAGE, $this->message_image, $template);
        $template = str_replace(self::KEY_MESSAGE, $this->message, $template);
        $template = str_replace(self::KEY_USER_NAME, $this->user->getName(), $template);
        $template = str_replace(self::KEY_USER_EMAIL, $this->user->getEmail(), $template);
        if (!is_null($this->user->getImageSrc())) {
            $template = str_replace(self::KEY_USER_IMAGE_SRC, $this->user->getImageSrc(), $template);
        } else {
            $template = str_replace(self::KEY_USER_IMAGE_SRC, '', $template);
        }
        echo $template;
    }
}
