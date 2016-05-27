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

namespace App\Views\Thread;


use App\Core\AbstractView;
use App\Interfaces\ThreadInterface;
use App\Models\Comment;
use App\Models\Session;
use App\Models\Thread;
use App\Models\User;
use App\Views\FooterPartial;
use App\Views\HeaderPartial;

class NewThreadView extends AbstractView implements ThreadInterface
{
    const KEY_FORUM_ID = '##FORUM_ID##';
    const KEY_MESSAGE = '##MESSAGE##';

    private $forum_id;
    private $message;

    /**
     * NewThreadView constructor.
     * @param string $forum_id
     * @param string $msg
     */
    public function __construct($forum_id, $msg = '')
    {
        parent::__construct(new HeaderPartial(), new FooterPartial());
        $this->setTemplateFile(FOLDER_TEMPLATES . DIRECTORY_SEPARATOR . 'thread' . DIRECTORY_SEPARATOR . 'new.html');
        $this->setTitle('Nuevo hilo | ' . PROJECT_NAME);
        $this->forum_id = $forum_id;
        $this->message = $msg;
    }

    public function render()
    {
        $template = parent::render();
        $template = str_replace(self::KEY_FORUM_ID, $this->forum_id, $template);
        $template = str_replace(self::KEY_MESSAGE, $this->message, $template);
        echo $template;
    }
}
