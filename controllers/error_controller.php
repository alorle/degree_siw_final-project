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

namespace Controllers;


use Core\AbstractController;
use Views\ErrorView;

class ErrorController extends AbstractController
{

    /**
     * ErrorController constructor.
     * @param int $code
     * @param string $extra_msg
     * @throws \Exception
     */
    public function __construct($code = 500, $extra_msg = null)
    {
        $this->http_response_code($code);
        $this->setView(new ErrorView($code, $this->http_status_code_string($code), $extra_msg));
    }

    /**
     * Sets $new_code as the result of the HTTP request
     * @param int $new_code
     */
    private function http_response_code($new_code = null)
    {
        if (!function_exists('http_response_code')) {
            function http_response_code($new_code = NULL)
            {
                static $code = 200;
                if ($new_code !== NULL) {
                    header('X-PHP-Response-Code: ' . $new_code, true, $new_code);
                    if (!headers_sent()) {
                        $code = $new_code;
                    }
                }
                return $code;
            }
        } else {
            http_response_code($new_code);
        }
    }

    /**
     * Select the appropriate string for the given HTTP code
     * @param int $code HTTP code
     * @param bool $include_code Whether to include code number in the returned string
     * @return string
     */
    private function http_status_code_string($code, $include_code = false)
    {
        switch ($code) {
            // 1xx Informational
            case 100:
                $string = 'Continue';
                break;
            case 101:
                $string = 'Switching Protocols';
                break;
            case 102:
                $string = 'Processing';
                break; // WebDAV
            case 122:
                $string = 'Request-URI too long';
                break; // Microsoft

            // 2xx Success
            case 200:
                $string = 'OK';
                break;
            case 201:
                $string = 'Created';
                break;
            case 202:
                $string = 'Accepted';
                break;
            case 203:
                $string = 'Non-Authoritative Information';
                break; // HTTP/1.1
            case 204:
                $string = 'No Content';
                break;
            case 205:
                $string = 'Reset Content';
                break;
            case 206:
                $string = 'Partial Content';
                break;
            case 207:
                $string = 'Multi-Status';
                break; // WebDAV

            // 3xx Redirection
            case 300:
                $string = 'Multiple Choices';
                break;
            case 301:
                $string = 'Moved Permanently';
                break;
            case 302:
                $string = 'Found';
                break;
            case 303:
                $string = 'See Other';
                break; //HTTP/1.1
            case 304:
                $string = 'Not Modified';
                break;
            case 305:
                $string = 'Use Proxy';
                break; // HTTP/1.1
            case 306:
                $string = 'Switch Proxy';
                break; // Depreciated
            case 307:
                $string = 'Temporary Redirect';
                break; // HTTP/1.1

            // 4xx Client Error
            case 400:
                $string = 'Bad Request';
                break;
            case 401:
                $string = 'Unauthorized';
                break;
            case 402:
                $string = 'Payment Required';
                break;
            case 403:
                $string = 'Forbidden';
                break;
            case 404:
                $string = 'Not Found';
                break;
            case 405:
                $string = 'Method Not Allowed';
                break;
            case 406:
                $string = 'Not Acceptable';
                break;
            case 407:
                $string = 'Proxy Authentication Required';
                break;
            case 408:
                $string = 'Request Timeout';
                break;
            case 409:
                $string = 'Conflict';
                break;
            case 410:
                $string = 'Gone';
                break;
            case 411:
                $string = 'Length Required';
                break;
            case 412:
                $string = 'Precondition Failed';
                break;
            case 413:
                $string = 'Request Entity Too Large';
                break;
            case 414:
                $string = 'Request-URI Too Long';
                break;
            case 415:
                $string = 'Unsupported Media Type';
                break;
            case 416:
                $string = 'Requested Range Not Satisfiable';
                break;
            case 417:
                $string = 'Expectation Failed';
                break;
            case 422:
                $string = 'Unprocessable Entity';
                break; // WebDAV
            case 423:
                $string = 'Locked';
                break; // WebDAV
            case 424:
                $string = 'Failed Dependency';
                break; // WebDAV
            case 425:
                $string = 'Unordered Collection';
                break; // WebDAV
            case 426:
                $string = 'Upgrade Required';
                break;
            case 449:
                $string = 'Retry With';
                break; // Microsoft
            case 450:
                $string = 'Blocked';
                break; // Microsoft

            // 5xx Server Error
            case 500:
                $string = 'Internal Server Error';
                break;
            case 501:
                $string = 'Not Implemented';
                break;
            case 502:
                $string = 'Bad Gateway';
                break;
            case 503:
                $string = 'Service Unavailable';
                break;
            case 504:
                $string = 'Gateway Timeout';
                break;
            case 505:
                $string = 'HTTP Version Not Supported';
                break;
            case 506:
                $string = 'Variant Also Negotiates';
                break;
            case 507:
                $string = 'Insufficient Storage';
                break; // WebDAV
            case 509:
                $string = 'Bandwidth Limit Exceeded';
                break; // Apache
            case 510:
                $string = 'Not Extended';
                break;

            // Unknown code:
            default:
                $string = 'Unknown';
                break;
        }
        if ($include_code)
            return $code . ' ' . $string;
        return $string;
    }
}
