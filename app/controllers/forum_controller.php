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
use App\Models\Forum;
use App\Models\Session;
use App\Views\ErrorView;
use App\Views\Forum\NewForumView;
use App\Views\Forum\ShowForumView;

class ForumController extends AbstractController
{
    const STR_INVALID_FORM = 'Formulario invalido';

    /**
     * ForumController constructor.
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
                $this->newForum();
                break;
            default:
                $this->showForums($params);
                break;
        }
    }

    private function newForum()
    {
        if (is_null($user = Session::getCurrentUser())) {
            // User is not identified
            redirect(PROJECT_BASE_URL . '/session/login');
        } elseif ($user->isModerator()) {
            $forum = null;
            if (isset($_POST['parent'])) {
                $forum = $_POST['parent'];

                if ($forum == 'none') {
                    $forum = null;
                }
            }

            if (isset($_POST['new'])) {
                $name = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
                $id = filter_var($_POST['id'], FILTER_SANITIZE_STRING);
                $forum = filter_var($_POST['forum'], FILTER_SANITIZE_STRING);
                $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);

                if (empty($name) || empty($id) || empty($description)) {
                    // Fields are empty (after sanitize),
                    // so display same view with error message
                    $this->setView(new NewForumView($forum, self::STR_INVALID_FORM));
                } else {
                    if ($forum == '') {
                        $forum = null;
                    };

                    // Check if id is unique
                    if (Forum::existsId($id)) {
                        $this->setView(new NewForumView($forum, 'La URL ya existe. Prueba con otra diferente.'));
                        return;
                    }

                    // Insert the new article in the database
                    $inserted = Forum::insert(array(
                        Forum::COLUMN_ID => $id,
                        Forum::COLUMN_NAME => $name,
                        Forum::COLUMN_DESCRIPTION => $description,
                        Forum::COLUMN_PARENT_FORUM_ID => $forum));

                    // If the insertion was successful, return to forum.
                    // In other case, show an error.
                    if ($inserted) {
                        redirect(PROJECT_BASE_URL . '/forum/' . $id);
                    } else {
                        throw new \Exception('Data could not be stored', 500);
                    }
                }
            } else {
                $this->setView(new NewForumView($forum));
            }
        } else {
            // Logged user can not add a new forum
            $this->setView(new ErrorView(403, 'Forbidden', 'No está autorizado a crear nuevos foros.'));
        }
    }

    private function showForums($params)
    {
        if (isset($params[0])) {
            $id = filter_var($params[0], FILTER_SANITIZE_STRING);
            if (is_null($forum = Forum::getById($id))) {
                $this->setView(new ErrorView(404, 'Not found', 'El foro "' . $id . '" no existe.'));
            } else {
                $this->setView(new ShowForumView($forum));
            }
        } else {
            $this->setView(new ShowForumView());
        }
    }
}
