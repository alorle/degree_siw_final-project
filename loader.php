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

include_once 'errors_handlers.php';

define('PROJECT_PATH', dirname(__FILE__));

define('PROJECT_VIEWS_PATH', PROJECT_PATH . DIRECTORY_SEPARATOR . 'views');
define('PROJECT_TEMPLATES_PATH', PROJECT_PATH . DIRECTORY_SEPARATOR . 'templates');
define('PROJECT_TEMPLATES_PARTS_PATH', PROJECT_TEMPLATES_PATH . DIRECTORY_SEPARATOR . 'parts');

define('PROJECT_NAME', 'Filosofía Joven');

/**
 * Given the namespace of a class, it finds the file name and includes it.
 * @param $class_name string Namespace of th class to include
 */
function autoload_classes($class_name)
{
    $namespace_parts = explode('\\', $class_name);

    $file_name = PROJECT_PATH;
    foreach ($namespace_parts as $namespace_part) {
        $file_name .= DIRECTORY_SEPARATOR . from_camel_to_snake($namespace_part);
    }
    $file_name .= '.php';

    if (is_file($file_name)) {
        include_once $file_name;
    }
}

/**
 * Given a string in CamelCase, it returns the same string in snake_case.
 * @param $input String CamelCase formatted string
 * @return string String Snake_case formatted string
 */
function from_camel_to_snake($input)
{
    preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
    $ret = $matches[0];
    foreach ($ret as &$match) {
        $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
    }
    return implode('_', $ret);
}

// Register 'autoload_classes' function as __autoload() implementation
spl_autoload_register('autoload_classes');
