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

namespace Views;

class ErrorView
{
    const KEY_TITLE = '##TITLE##';
    const KEY_ERROR_CODE = '##ERROR_CODE##';
    const KEY_ERROR_MESSAGE = '##ERROR_MSG##';

    private $code;
    private $message;
    private $file_template;
    private $title;

    /**
     * ErrorView constructor.
     * @param $code
     * @param $message
     */
    public function __construct($code = 500, $message = 'Internal server error')
    {
        $this->code = $code;
        $this->message = $message;

        $this->file_template = PROJECT_TEMPLATES_PATH . DIRECTORY_SEPARATOR . 'error.html';
        $this->title = $code . ' ' . $message . ' | ' . PROJECT_NAME;
    }

    public function render()
    {
        if (!file_exists($this->file_template)) {
            echo 'We\'ve screwed up. We can not report errors.<br/>';
            echo 'If it worked for Microsoft, it will also work for us: <p style="font-size: 2em;">:(</p>';
            return;
        }

        ob_clean();

        $this->http_response_code($this->code);

        $template = file_get_contents($this->file_template);
        $template = str_replace(self::KEY_TITLE, $this->title, $template);
        $template = str_replace(self::KEY_ERROR_CODE, $this->code, $template);
        $template = str_replace(self::KEY_ERROR_MESSAGE, $this->message, $template);

        echo $template;
    }

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
}