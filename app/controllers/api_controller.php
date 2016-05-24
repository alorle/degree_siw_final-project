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
use App\Models\Article;
use App\Models\Forum;
use App\Models\Session;
use App\Models\Thread;
use App\Models\User;
use App\Views\Article\EditArticleView;
use App\Views\Article\NewArticleView;
use App\Views\Article\ShowArticleView;
use App\Views\ErrorView;
use lib\FPDF;

class ApiController extends AbstractController
{
    /**
     * ApiController constructor.
     * @param $params
     * @throws \Exception
     */
    public function __construct($params)
    {
        header('Content-Type: application/json');

        $action = '';
        if (isset($params[0])) {
            $action = $params[0];
        }

        switch ($action) {
            case 'forum':
                $this->forum($params);
                break;
        }
    }

    private function forum($params)
    {
        if (isset($params[1])) {
            if ($params[1] == 'forums') {
                echo Forum::getAllParentsJSON();
            } elseif ($params[1] == 'subforums' && isset($_POST['parent'])) {
                echo Forum::getAllChildrenJSON($_POST['parent']);
            } elseif ($params[1] == 'threads' && isset($_POST['forum'])) {
                echo Thread::getAllJson($_POST['forum']);
            }
        }
    }
}
