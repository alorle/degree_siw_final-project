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
use App\Models\Session;
use App\Views\Profile\AdminProfileView;
use App\Views\Profile\BlogProfileView;
use App\Views\Profile\ForumProfileView;
use App\Views\Profile\MainProfileView;

class ProfileController extends AbstractController
{

    /**
     * ProfileController constructor.
     * @param $params
     * @throws \Exception
     */
    public function __construct($params)
    {
        $action = '';
        if (isset($params[0])) {
            $action = $params[0];
        }

        if (!is_null($user = Session::getCurrentUser())) {
            switch ($action) {
                case 'blog':
                    $this->setView(new BlogProfileView($user));
                    break;
                case 'forum':
                    $this->setView(new ForumProfileView($user));
                    break;
                case 'admin':
                    $this->setView(new AdminProfileView($user));
                    break;
                default:
                    $this->setView(new MainProfileView($user));
            }
        } else {
            redirect(PROJECT_BASE_URL . '/session/login');
        }
    }
}
