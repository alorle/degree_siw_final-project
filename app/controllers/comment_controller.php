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

namespace app\controllers;


use App\Core\AbstractController;
use App\Models\Comment;
use App\Models\Forum;
use App\Models\Session;
use App\Models\Thread;
use App\Views\ErrorView;
use App\Views\Forum\ShowForumView;

class CommentController extends AbstractController
{
    /**
     * CommentController constructor.
     * @param $params
     * @throws \Exception
     */
    public function __construct($params)
    {
        $action = '';
        if (isset($params[0])) {
            $action = $params[0];
        }

        switch ($action) {
            case 'new':
                $this->newComment();
                break;
            case 'edit':
                $this->editComment($params[1]);
                break;
            case 'delete':
                $this->deleteComment($params[1]);
                break;
            default:
                $this->setView(new ErrorView(404, 'Not found'));
                break;
        }
    }

    private function newComment()
    {
        if (is_null($user = Session::getCurrentUser())) {
            // User is not identified
            redirect(PROJECT_BASE_URL . '/session/login');
        } else {
            if (isset($_POST['thread'])) {
                $threadId = $_POST['thread'];
                $thread = Thread::getById($threadId);
                if (!is_null($thread)) {
                    $this->setView(new ErrorView(501, 'New comment view not implemented (Thread Id: ' . $threadId . ')'));
                } else {
                    $this->setView(new ErrorView(404, 'Not found'));
                }
            } else {
                $this->setView(new ErrorView(404, 'Not found'));
            }
        }
    }

    private function editComment($id)
    {
        $comment_id = '';
        if (isset($id)) {
            $comment_id = $id;
        }

        if (is_null($user = Session::getCurrentUser())) {
            // User is not identified
            redirect(PROJECT_BASE_URL . '/session/login');
        } else {
            if (!is_null($comment = Comment::getById($comment_id))) {
                if ($user->getId() != $comment->getAuthorId()) {
                    $this->setView(new ErrorView(403, 'Forbidden', 'No puedes editar un comentario que no hayas escrito tu.'));
                } else {
                    $this->setView(new ErrorView(501, 'Edit comment view not implemented (Comment Id: ' . $comment_id . ')'));
                }
            } else {
                $this->setView(new ErrorView(404, 'Not found'));
            }
        }
    }

    private function deleteComment($id)
    {
        $comment_id = '';
        if (isset($id)) {
            $comment_id = $id;
        }

        if (is_null($user = Session::getCurrentUser())) {
            // User is not identified
            redirect(PROJECT_BASE_URL . '/session/login');
        } else {
            if (!is_null($comment = Comment::getById($comment_id))) {
                if ($user->getId() != $comment->getAuthorId()) {
                    $this->setView(new ErrorView(403, 'Forbidden', 'No puedes editar un comentario que no hayas escrito tu.'));
                } else {
                    $thread_comments = Comment::getAll($comment->getThreadId());

                    if ($thread_comments[0]->getId() == $comment_id) {
                        $deleted = Thread::delete($comment->getThreadId());
                        $redirectUrl = PROJECT_BASE_URL . '/forum';
                    } else {
                        $deleted = Comment::delete($comment_id);
                        $redirectUrl = PROJECT_BASE_URL . '/thread/' . $comment->getThreadId();
                    }

                    if ($deleted) {
                        redirect($redirectUrl);
                    } else {
                        throw new \Exception('Data could not be updated', 500);
                    }
                }
            } else {
                $this->setView(new ErrorView(404, 'Not found'));
            }
        }
    }
}
