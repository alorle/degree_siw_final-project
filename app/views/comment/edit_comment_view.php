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
use App\Interfaces\CommentInterface;
use App\Models\Comment;
use App\Views\FooterPartial;
use App\Views\HeaderPartial;

class EditCommentView extends AbstractView implements CommentInterface
{
    const KEY_MESSAGE = '##MESSAGE##';

    private $comment;
    private $message;

    /**
     * EditCommentView constructor.
     * @param Comment $comment
     * @param string $msg
     */
    public function __construct($comment, $msg = '')
    {
        parent::__construct(new HeaderPartial(), new FooterPartial());
        $this->setTemplateFile(FOLDER_TEMPLATES . DIRECTORY_SEPARATOR . 'comment' . DIRECTORY_SEPARATOR . 'edit.html');
        $this->setTitle('Editar comentario | ' . PROJECT_NAME);
        $this->comment = $comment;
        $this->message = $msg;
    }

    public function render()
    {
        $template = parent::render();
        $template = str_replace(self::KEY_COMMENT_ID, $this->comment->getId(), $template);
        $template = str_replace(self::KEY_COMMENT_TITLE, $this->comment->getTitle(), $template);
        $template = str_replace(self::KEY_COMMENT_BODY, $this->comment->getBody(), $template);
        $template = str_replace(self::KEY_MESSAGE, $this->message, $template);
        echo $template;
    }
}
