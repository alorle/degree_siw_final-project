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

use Core\AbstractView;

class LoginView extends AbstractView
{
    const KEY_MESSAGE = '##MESSAGE##';

    private $message;

    /**
     * LoginView constructor.
     * @param string $msg
     * @throws \Exception
     */
    public function __construct($msg = '')
    {
        parent::__construct();

        $this->setFileTemplate(PROJECT_TEMPLATES_PATH . DIRECTORY_SEPARATOR . 'login.html');
        $this->setTitle('Login | ' . PROJECT_NAME);

        $this->message = $msg;
    }

    public function render()
    {
        if (!file_exists($this->getFileTemplate())) {
            throw new \Exception("Internal server error", 500);
        }

        $template = file_get_contents($this->getFileTemplate());
        $template = str_replace(self::KEY_TITLE, $this->getTitle(), $template);
        $template = str_replace(self::KEY_HEAD, $this->readHead(), $template);
        $template = str_replace(self::KEY_MESSAGE, $this->message, $template);

        echo $template;
    }
}
