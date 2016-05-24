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
use App\Models\Session;
use App\Models\Thread;
use App\Views\ErrorView;
use App\Views\Thread\ShowThreadView;

class ThreadController extends AbstractController
{
    const COMMENTS_PER_PAGE = 10;

    /**
     * ThreadController constructor.
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
                $this->newThread();
                break;
            case 'delete':
                $this->deleteThread($params[1]);
                break;
            default:
                $this->showThread($params);
                break;
        }
    }

    private function newThread()
    {
        if (is_null($user = Session::getCurrentUser())) {
            // User is not identified
            redirect(PROJECT_BASE_URL . '/session/login');
        } else {
            if (isset($_POST['forum'])) {
                $this->setView(new ErrorView(501, 'New thread view not implemented (' . $_POST['forum'] . ')'));
            } else {
                $this->setView(new ErrorView(404, 'Not found'));
            }
        }
    }

    private function deleteThread($id)
    {
        $thread = Thread::getById($id);
        if (!is_null($thread)) {
            if (is_null($user = Session::getCurrentUser())) {
                // User is not identified
                redirect(PROJECT_BASE_URL . '/session/login');
            } else {
                if ($thread->getAuthorId() == $user->getId()) {
                    $deleted = Thread::delete($id);

                    // If the update was successful, return to profile/blog.
                    // In other case, show an error.
                    if ($deleted) {
                        redirect(PROJECT_BASE_URL . '/profile/forum');
                    } else {
                        throw new \Exception('Data could not be updated', 500);
                    }
                } else {
                    // Logged user is not the author of the thread
                    $this->setView(new ErrorView(403, 'Forbidden', 'No puedes eliminar un foro que no has creado.'));
                }
            }
        } else {
            $this->setView(new ErrorView(404, 'Not found'));
        }
    }

    private function showThread($params)
    {
        if (isset($params[0])) {
            $id = filter_var($params[0], FILTER_SANITIZE_STRING);
            if (is_null($thread = Thread::getById($id))) {
                $this->setView(new ErrorView(404, 'Not found', 'El hilo "' . $id . '" no existe.'));
            } else {
                $total_comments = Comment::count($id);

                if ($total_comments <= self::COMMENTS_PER_PAGE) {
                    // Show all comments available, as they are less than the comments that fit on one page.
                    $this->setView(new ShowThreadView($thread, Comment::getAll($id)));
                } else {
                    // Calculate the number of pages needed
                    $total_pages = ceil($total_comments / self::COMMENTS_PER_PAGE);

                    // Get the requested page
                    $current_page = 1;
                    if (isset($params[1]) && !filter_var($params[1], FILTER_VALIDATE_INT) === false) {
                        $current_page = min($total_pages, $params[1]);
                    }

                    // Get comments to show
                    $comments = Comment::getAll($id, '', self::COMMENTS_PER_PAGE, ($current_page - 1) * self::COMMENTS_PER_PAGE);

                    // Set the view that will be rendered
                    $this->setView(new ShowThreadView($thread, $comments, $total_pages, $current_page));
                }
            }
        } else {
            $this->setView(new ShowThreadView());
        }
    }
}
