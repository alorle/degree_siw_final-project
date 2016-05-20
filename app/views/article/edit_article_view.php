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
use App\Models\Article;
use App\Views\FooterPartial;
use App\Views\HeaderPartial;

class EditArticleView extends AbstractView implements ArticleInterface
{
    const KEY_MESSAGE = '##MESSAGE##';
    const KEY_CONFIRM_DELETE = '##CONFIRM_DELETE##';

    private $article;
    private $message;

    /**
     * EditArticleView constructor.
     * @param Article $forum
     * @param string $msg
     */
    public function __construct($forum, $msg = '')
    {
        parent::__construct(new HeaderPartial(), new FooterPartial());
        $this->setTemplateFile(FOLDER_TEMPLATES . DIRECTORY_SEPARATOR . 'article' . DIRECTORY_SEPARATOR . 'edit.html');
        $this->setTitle('Editar "' . $forum->getTitle() . '"" | ' . PROJECT_NAME);
        $this->article = $forum;
    }

    public function render()
    {
        $template = parent::render();

        $template = str_replace(self::KEY_ARTICLE_ID, $this->article->getId(), $template);
        $template = str_replace(self::KEY_ARTICLE_TITLE, $this->article->getTitle(), $template);
        $template = str_replace(self::KEY_ARTICLE_BODY, $this->article->getBody(), $template);

        $template = str_replace(self::KEY_MESSAGE, $this->message, $template);

        $confirm_delete_question = '¿Quieres eliminar el artículo ' . $this->article->getId() . '?';
        $template = str_replace(self::KEY_CONFIRM_DELETE, $confirm_delete_question, $template);

        echo $template;
    }
}
