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

include_once 'util' . DIRECTORY_SEPARATOR . 'utils.php';

$url = array_map('strtolower', explode('/', get_param('GET', 'url')));

$path = $url[0];
$params = array_slice($url, 1);

switch ($path) {
    case 'session':
        $controller = new Controllers\SessionController($params);
        break;
    default:
        $controller = new Controllers\ErrorController(404);
        break;
}

$controller->render();
