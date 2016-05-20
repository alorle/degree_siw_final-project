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
use App\Models\User;
use App\Views\User\UserView;

class UserController extends AbstractController
{

    /**
     * UserController constructor.
     * @param $params
     * @throws \Exception
     */
    public function __construct($params)
    {
        $user_id = '';
        if (isset($params[0])) {
            $user_id = $params[0];
        }

        if (isset($user_id) && !is_null($user = User::getById($user_id))) {
            $this->setView(new UserView($user));
        } else {
            redirect();
        }
    }
}
