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
use App\Views\Comment\EditCommentView;
use App\Views\Comment\NewCommentView;
use App\Views\ErrorView;
use App\Views\Forum\ShowForumView;

class CommentController extends AbstractController
{
    const STR_INVALID_FORM = 'Formulario invalido';

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
            if (isset($_POST['new'])) {
                $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
                $body = filter_var($_POST['body'], FILTER_SANITIZE_STRING);
                $thread_id = filter_var($_POST['forum'], FILTER_SANITIZE_STRING);

                if (empty($title) || empty($body)) {
                    // Fields are empty (after sanitize),
                    // so display same view with error message
                    $this->setView(new NewCommentView($thread_id, self::STR_INVALID_FORM));
                } else {
                    $author_id = Session::getCurrentUser()->getId();

                    $inserted = Comment::insert(array(
                        Comment::COLUMN_TITLE => $title,
                        Comment::COLUMN_BODY => $body,
                        Comment::COLUMN_THREAD_ID => $thread_id,
                        Comment::COLUMN_AUTHOR_ID => $author_id));

                    // If the insertion was successful, redirect to created thread.
                    // In other case, show an error.
                    if ($inserted) {
                        redirect(PROJECT_BASE_URL . '/thread/' . $thread_id);
                    } else {
                        throw new \Exception('Data could not be stored', 500);
                    }
                }
            } else {
                if (isset($_POST['thread']) && Thread::existsId($_POST['thread'])) {
                    $this->setView(new NewCommentView($_POST['thread'], ''));
                } else {
                    $this->setView(new ErrorView(404, 'Not found'));
                }
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
                    if (isset($_POST['edit'])) {
                        $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
                        $body = filter_var($_POST['body'], FILTER_SANITIZE_STRING);

                        if (empty($title) || empty($body)) {
                            // Fields are empty (after sanitize),
                            // so display same view with error message
                            $this->setView(new EditCommentView($comment, self::STR_INVALID_FORM));
                        } else {
                            // Update the article
                            $updated = Comment::updateTitleAndBody($id, $title, $body);

                            // If the update was successful, return to blog.
                            // In other case, show an error.
                            if ($updated) {
                                redirect(PROJECT_BASE_URL . '/thread/' . $comment->getThreadId());
                            } else {
                                throw new \Exception('Data could not be updated', 500);
                            }
                        }
                    } else {
                        $this->setView(new EditCommentView($comment));
                    }
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
                    $this->setView(new ErrorView(403, 'Forbidden', 'No puedes eliminar un comentario que no hayas escrito tu.'));
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
