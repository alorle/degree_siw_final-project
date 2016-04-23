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

namespace App\Views\Article;


use App\Core\AbstractView;
use App\Interfaces\ArticleInterface;
use App\Views\FooterPartial;
use App\Views\HeaderPartial;

class NewArticleView extends AbstractView implements ArticleInterface
{
    const KEY_MESSAGE = '##MESSAGE##';

    private $message;

    /**
     * NewArticleView constructor.
     * @param string $msg
     */
    public function __construct($msg = '')
    {
        parent::__construct(new HeaderPartial(), new FooterPartial());
        $this->setTemplateFile(FOLDER_TEMPLATES . DIRECTORY_SEPARATOR . 'article' . DIRECTORY_SEPARATOR . 'new.html');
        $this->setTitle('Nuevo artículo | ' . PROJECT_NAME);
        $this->message = $msg;
    }

    public function render()
    {
        $template = parent::render();
        $template = str_replace(self::KEY_MESSAGE, $this->message, $template);
        echo $template;
    }
}
