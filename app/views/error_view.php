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

namespace App\Views;


use App\Core\AbstractView;

class ErrorView extends AbstractView
{
    const TAG_CODE = '##ERROR_CODE##';
    const TAG_MSG = '##ERROR_MSG##';
    const TAG_EXTRA_MSG = '##ERROR_EXTRA_MSG##';

    private $code;
    private $msg;
    private $extra_msg;

    /**
     * ErrorView constructor.
     * @param string $code Error code
     * @param string $msg Error message
     * @param string $extra_msg Error extra message
     */
    public function __construct($code, $msg, $extra_msg = null)
    {
        ob_clean();
        parent::__construct();
        $this->code = $code;
        $this->msg = $msg;
        $this->extra_msg = $extra_msg;
        $this->setTitle($code . ' ' . $msg . ' | ' . PROJECT_NAME);
        $this->setTemplateFile(FOLDER_TEMPLATES . DIRECTORY_SEPARATOR . 'error.html');
    }

    public function render()
    {
        $template = parent::render();
        $template = str_replace(self::TAG_CODE, $this->code, $template);
        $template = str_replace(self::TAG_MSG, $this->msg, $template);
        $template = str_replace(self::TAG_EXTRA_MSG, $this->extra_msg, $template);
        echo $template;
    }
}
