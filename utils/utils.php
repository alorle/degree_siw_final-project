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

$request = "http://" . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
$base_url = substr($request, 0, strrpos($request, 'trabajofinal')) . 'trabajofinal';

// Define PROJECT constants
define('PROJECT_PATH', dirname(__DIR__));
define('PROJECT_NAME', 'Filosofía Joven');
define('PROJECT_BASE_URL', $base_url);

// Define PROJECT folders
define('FOLDER_UTIL', PROJECT_PATH . DIRECTORY_SEPARATOR . 'utils');
define('FOLDER_TEMPLATES', PROJECT_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'templates');
define('FOLDER_CONFIG', PROJECT_PATH . DIRECTORY_SEPARATOR . 'config');

// Include util files
include_once FOLDER_UTIL . DIRECTORY_SEPARATOR . 'functions.php';
include_once FOLDER_UTIL . DIRECTORY_SEPARATOR . 'loader.php';
include_once FOLDER_UTIL . DIRECTORY_SEPARATOR . 'error_handler.php';
