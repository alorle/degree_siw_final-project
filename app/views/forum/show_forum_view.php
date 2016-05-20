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

namespace App\Views\Forum;


use App\Core\AbstractView;
use App\Interfaces\ForumInterface;
use App\Models\Forum;
use App\Models\Thread;
use App\Models\User;
use App\Views\FooterPartial;
use App\Views\HeaderPartial;

class ShowForumView extends AbstractView implements ForumInterface
{
    private $forum;

    /**
     * ShowForumView constructor.
     * @param Forum $forum
     */
    public function __construct($forum = null)
    {
        parent::__construct(new HeaderPartial(), new FooterPartial());
        $this->setTemplateFile(FOLDER_TEMPLATES . DIRECTORY_SEPARATOR . 'forum' . DIRECTORY_SEPARATOR . 'show.html');

        if (isset($forum)) {
            $this->setTitle($forum->getName() . ' | ' . PROJECT_NAME);
        } else {
            $this->setTitle('Forum | ' . PROJECT_NAME);
        }

        $this->forum = $forum;
    }

    public function render()
    {
        $template = parent::render();

        if (is_null($this->forum)) {
            $forum_children = Forum::getAllParents();
            $forum_threads = array();
        } else {
            $forum_children = Forum::getAllChildren($this->forum->getId());
            $forum_threads = Thread::getAll($this->forum->getId());
        }

        $template_parts = explode(self::KEY_FORUM_CHILDREN, $template);
        if (empty($forum_children)) {
            $template = $template_parts[0] . $template_parts[2];
        } else {
            $template = $template_parts[0] . $template_parts[1] . $template_parts[2];
            $template_parts = explode(self::KEY_FORUM_CHILD, $template);
            $template_children = '';
            foreach ($forum_children as $child) {
                $template_children .= $this->replaceForumChild($template_parts[1], $child);
            }
            $template = $template_parts[0] . $template_children . $template_parts[2];
        }

        $template_parts = explode(self::KEY_FORUM_THREADS, $template);
        if (empty($forum_threads)) {
            $template = $template_parts[0] . $template_parts[2];
        } else {
            $template = $template_parts[0] . $template_parts[1] . $template_parts[2];
            $template_parts = explode(self::KEY_FORUM_THREAD, $template);
            $template_threads = '';
            foreach ($forum_threads as $thread) {
                $template_threads .= $this->replaceForumThread($template_parts[1], $thread);
            }
            $template = $template_parts[0] . $template_threads . $template_parts[2];
        }

        echo $template;
    }

    private function replaceForumChild($template, Forum $forum)
    {
        $template = str_replace(self::KEY_FORUM_CHILD_ID, $forum->getId(), $template);
        $template = str_replace(self::KEY_FORUM_CHILD_NAME, $forum->getName(), $template);
        $template = str_replace(self::KEY_FORUM_CHILD_DESCRIPTION, $forum->getDescription(), $template);
        $template = str_replace(self::KEY_FORUM_CHILD_COUNT_CHILDREN, count(Forum::getAllChildren($forum->getId())), $template);
        $template = str_replace(self::KEY_FORUM_CHILD_COUNT_THREADS, count(Thread::getAll($forum->getId())), $template);

        return $template;
    }

    private function replaceForumThread($template, Thread $thread)
    {
        $template = str_replace(self::KEY_FORUM_THREAD_ID, $thread->getId(), $template);
        $template = str_replace(self::KEY_FORUM_THREAD_NAME, $thread->getName(), $template);

        if (!is_null($author = User::getById($thread->getAuthorId()))) {
            $template = str_replace(self::KEY_FORUM_THREAD_AUTHOR_ID, $author->getId(), $template);
            $template = str_replace(self::KEY_FORUM_THREAD_AUTHOR_NAME, $author->getName(), $template);
        } else {
            $template = str_replace(self::KEY_FORUM_THREAD_AUTHOR_ID, $thread->getAuthorId(), $template);
            $template = str_replace(self::KEY_FORUM_THREAD_AUTHOR_NAME, $thread->getAuthorId(), $template);
        }
        $template = str_replace(self::KEY_FORUM_THREAD_COUNT_COMMENTS, $thread->getCommentsCount(), $template);

        return $template;
    }
}
