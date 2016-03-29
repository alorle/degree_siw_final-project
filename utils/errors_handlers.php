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

use Views\ErrorView;

/**
 * Error handler function
 * @param $code int Status code
 * @param $message string Message
 */
function error_handler($code, $message)
{
    $error = new ErrorView($code, $message);
    $error->render();
}

/**
 * Exception handler function
 * @param $ex Exception to handle
 */
function exception_handler($ex)
{
    if ($ex) {
        error_handler($ex->getCode(), $ex->getMessage());
    }
}

/**
 * Shutdown function
 */
function shutdown()
{
    $error = error_get_last();
    if ($error) error_handler(500, $error["message"]);
}

// Set handlers
set_error_handler("error_handler");
set_exception_handler('exception_handler');
register_shutdown_function("shutdown");

