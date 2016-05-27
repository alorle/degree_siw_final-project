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

class ShowThreadView extends AbstractView implements ThreadInterface
{
    private $thread;
    private $comments;
    private $total_pages;
    private $current_page;
    private $logged_user;

    /**
     * ShowThreadView constructor.
     * @param Thread $thread
     * @param array $comments
     * @param int $total_pages
     * @param int $current_page
     */
    public function __construct($thread = null, $comments = null, $total_pages = null, $current_page = null)
    {
        parent::__construct(new HeaderPartial(), new FooterPartial());
        $this->setTemplateFile(FOLDER_TEMPLATES . DIRECTORY_SEPARATOR . 'thread' . DIRECTORY_SEPARATOR . 'show.html');
        $this->setTitle($thread->getName() . ' | ' . PROJECT_NAME);
        $this->thread = $thread;
        $this->comments = $comments;
        $this->total_pages = $total_pages;
        $this->current_page = $current_page;
        $this->logged_user = Session::getCurrentUser();
    }

    public function render()
    {
        $template = parent::render();

        $template = str_replace(self::KEY_THREAD_ID, $this->thread->getId(), $template);

        $template_parts = explode(self::KEY_THREAD_COMMENTS, $template);
        if (empty($this->comments)) {
            $template = $template_parts[0] . $template_parts[2];
        } else {
            $template = $template_parts[0] . $template_parts[1] . $template_parts[2];
            $template_parts = explode(self::KEY_THREAD_COMMENT, $template);
            $template_comments = '';
            foreach ($this->comments as $comment) {
                $template_comments .= $this->replaceThreadComment($template_parts[1], $comment);
            }
            $template = $template_parts[0] . $template_comments . $template_parts[2];
        }

        // Fill pagination section
        $template = $this->replacePagination($template);

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

        $template_parts = explode(self::KEY_THREAD_COMMENT_USER_ACTIONS, $template);
        if ($this->logged_user == $author) {
            $template = $template_parts[0] . $template_parts[1] . $template_parts[2];
        } else {
            $template = $template_parts[0] . $template_parts[2];
        }

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
