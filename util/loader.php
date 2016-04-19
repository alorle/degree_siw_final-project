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
 * Given the namespace of a class, it finds the file name and includes it.
 * @param $class_name string Namespace of the class to include
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

// Register 'autoload_classes' function as __autoload() implementation
spl_autoload_register('autoload_classes');
