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

/**
 * Get HTTP parameter
 * @param $method string Method (PUT or GET) by which want to obtain the parameter
 * @param $name string Parameter name
 * @return null|string
 */
function get_param($method, $name)
{
    switch ($method) {
        case 'GET':
            if (isset($_GET[$name])) {
                return $_GET[$name];
            }
            break;
        case 'POST':
            if (isset($_POST[$name])) {
                return $_POST[$name];
            }
            break;
    }

    return null;
}

/**
 * Redirects the user to the given location.
 * @param $location
 */
function redirect($location = '..')
{
    header('Location: ' . $location);
    die();
}
