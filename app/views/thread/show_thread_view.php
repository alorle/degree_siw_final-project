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
use App\Models\Thread;
use App\Models\User;
use App\Views\FooterPartial;
use App\Views\HeaderPartial;

class ShowThreadView extends AbstractView implements ThreadInterface
{
    private $thread;

    /**
     * ShowThreadView constructor.
     * @param Thread $thread
     */
    public function __construct($thread = null)
    {
        parent::__construct(new HeaderPartial(), new FooterPartial());
        $this->setTemplateFile(FOLDER_TEMPLATES . DIRECTORY_SEPARATOR . 'thread' . DIRECTORY_SEPARATOR . 'show.html');
        $this->setTitle($thread->getName() . ' | ' . PROJECT_NAME);
        $this->thread = $thread;
    }

    public function render()
    {
        $template = parent::render();

        $thread_comments = Comment::getAll($this->thread->getId());

        $template_parts = explode(self::KEY_THREAD_COMMENTS, $template);
        if (empty($thread_comments)) {
            $template = $template_parts[0] . $template_parts[2];
        } else {
            $template = $template_parts[0] . $template_parts[1] . $template_parts[2];
            $template_parts = explode(self::KEY_THREAD_COMMENT, $template);
            $template_comments = '';
            foreach ($thread_comments as $comment) {
                $template_comments .= $this->replaceThreadComment($template_parts[1], $comment);
            }
            $template = $template_parts[0] . $template_comments . $template_parts[2];
        }

        echo $template;
    }

    private function replaceThreadComment($template, Comment $comment)
    {
        $template = str_replace(self::KEY_THREAD_COMMENT_ID, $comment->getId(), $template);
        $template = str_replace(self::KEY_THREAD_COMMENT_TITLE, $comment->getTitle(), $template);
        $template = str_replace(self::KEY_THREAD_COMMENT_BODY, $comment->getBody(), $template);
        $template = str_replace(self::KEY_THREAD_COMMENT_TIME, $comment->getTime(), $template);

        if (!is_null($author = User::getById($comment->getAuthorId()))) {
            $template = str_replace(self::KEY_THREAD_COMMENT_AUTHOR_ID, $author->getId(), $template);
            $template = str_replace(self::KEY_THREAD_COMMENT_AUTHOR_NAME, $author->getName(), $template);
            $template = str_replace(self::KEY_THREAD_COMMENT_AUTHOR_IMAGE, $author->getImageSrc(), $template);
        } else {
            $template = str_replace(self::KEY_THREAD_COMMENT_AUTHOR_ID, $comment->getAuthorId(), $template);
            $template = str_replace(self::KEY_THREAD_COMMENT_AUTHOR_NAME, $comment->getAuthorId(), $template);
            $template = str_replace(self::KEY_THREAD_COMMENT_AUTHOR_IMAGE, User::getDefaultImageSrc(), $template);
        }

        return $template;
    }
}
