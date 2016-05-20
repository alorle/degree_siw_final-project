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
use App\Models\User;
use App\Views\FooterPartial;
use App\Views\HeaderPartial;

class ShowArticleView extends AbstractView implements ArticleInterface
{
    private $article;

    /**
     * ShowArticleView constructor.
     * @param Article $article
     */
    public function __construct($article)
    {
        parent::__construct(new HeaderPartial(), new FooterPartial());
        $this->setTemplateFile(FOLDER_TEMPLATES . DIRECTORY_SEPARATOR . 'article' . DIRECTORY_SEPARATOR . 'show.html');
        $this->setTitle($article->getTitle() . ' | ' . PROJECT_NAME);
        $this->article = $article;
    }

    public function render()
    {
        $template = parent::render();

        $template = str_replace(self::KEY_ARTICLE_ID, $this->article->getId(), $template);
        $template = str_replace(self::KEY_ARTICLE_TITLE, $this->article->getTitle(), $template);
        $template = str_replace(self::KEY_ARTICLE_BODY, $this->article->getBody(), $template);
        $template = str_replace(self::KEY_ARTICLE_TIME, $this->article->getTime(), $template);

        if (is_null($user = User::getById($this->article->getAuthorId()))) {
            $template = str_replace(self::KEY_ARTICLE_AUTHOR, $this->article->getAuthorId(), $template);
            $template = str_replace(self::KEY_ARTICLE_AUTHOR_LINK, '', $template);
        } else {
            $template = str_replace(self::KEY_ARTICLE_AUTHOR, $user->getName(), $template);
            $link = 'href="' . PROJECT_BASE_URL . '/user/' . $user->getId() . '"';
            $template = str_replace(self::KEY_ARTICLE_AUTHOR_LINK, $link, $template);
        }

        echo $template;
    }
}
