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

include_once 'utils' . DIRECTORY_SEPARATOR . 'utils.php';

$url = array_map('strtolower', explode('/', get_param('GET', 'url')));

$path = $url[0];
$params = array_slice($url, 1);

switch ($path) {
    case 'api':
        $controller = new App\Controllers\ApiController($params);
        break;
    case 'blog':
        $controller = new App\Controllers\BlogController($params);
        break;
    case 'article':
        $controller = new App\Controllers\ArticleController($params);
        break;
    case 'forum':
        $controller = new App\Controllers\ForumController($params);
        break;
    case 'thread':
        $controller = new App\Controllers\ThreadController($params);
        break;
    case 'profile':
        $controller = new App\Controllers\ProfileController($params);
        break;
    case 'user':
        $controller = new App\Controllers\UserController($params);
        break;
    case 'session':
        $controller = new App\Controllers\SessionController($params);
        break;
    case 'error':
        $controller = new App\Controllers\ErrorController($params[0]);
        break;
    default:
        $controller = new App\Controllers\ErrorController(404);
        break;
}

if (!($controller instanceof App\Controllers\ApiController)) {
    $controller->render();
}
