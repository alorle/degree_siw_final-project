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
use App\Views\ErrorView;
use App\Views\Forum\ShowForumView;

class ForumController extends AbstractController
{
    /**
     * BlogController constructor.
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
            case 'edit':
                $this->editForum($params[1]);
                break;
            case 'delete':
                $this->deleteForum($params[1]);
                break;
            default:
                $this->showForums($params);
                break;
        }
    }

    private function newForum()
    {
    }

    private function editForum($int)
    {
    }

    private function deleteForum($int)
    {
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
