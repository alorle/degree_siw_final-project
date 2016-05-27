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

namespace App\Views\Comment;


use App\Core\AbstractView;
use App\Views\FooterPartial;
use App\Views\HeaderPartial;

class NewCommentView extends AbstractView
{
    const KEY_THREAD_ID = '##THREAD_ID##';
    const KEY_MESSAGE = '##MESSAGE##';

    private $thread_id;
    private $message;

    /**
     * NewCommentView constructor.
     * @param string $thread_id
     * @param string $msg
     */
    public function __construct($thread_id, $msg = '')
    {
        parent::__construct(new HeaderPartial(), new FooterPartial());
        $this->setTemplateFile(FOLDER_TEMPLATES . DIRECTORY_SEPARATOR . 'comment' . DIRECTORY_SEPARATOR . 'new.html');
        $this->setTitle('Nuevo comentario | ' . PROJECT_NAME);
        $this->thread_id = $thread_id;
        $this->message = $msg;
    }

    public function render()
    {
        $template = parent::render();
        $template = str_replace(self::KEY_THREAD_ID, $this->thread_id, $template);
        $template = str_replace(self::KEY_MESSAGE, $this->message, $template);
        echo $template;
    }
}
