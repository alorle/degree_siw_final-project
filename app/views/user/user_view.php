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

namespace App\Views\User;


use App\Core\AbstractView;
use App\Interfaces\ArticleInterface;
use App\Interfaces\UserInterface;
use App\Models\Article;
use App\Models\User;
use App\Views\FooterPartial;
use App\Views\HeaderPartial;

class UserView extends AbstractView implements UserInterface, ArticleInterface
{
    private $user;

    /**
     * UserView constructor.
     * @param User $user
     */
    public function __construct($user)
    {
        parent::__construct(new HeaderPartial(), new FooterPartial());
        $this->setTitle($user->getName() . ' profile | ' . PROJECT_NAME);
        $this->setTemplateFile(FOLDER_TEMPLATES . DIRECTORY_SEPARATOR . 'user' . DIRECTORY_SEPARATOR . 'user.html');
        $this->user = $user;
    }

    public function render()
    {
        $template = parent::render();

        $template = str_replace(self::KEY_USER_NAME, $this->user->getName(), $template);
        $template = str_replace(self::KEY_USER_IMAGE_SRC, $this->user->getImageSrc(), $template);

        $articles = Article::getByAuthorId($this->user->getId());
        $template_parts = explode(self::KEY_USER_BLOG_ARTICLES, $template);
        if (empty($articles)) {
            $template_articles = 'Ninguno';
        } else {
            $template_articles = '';
            foreach ($articles as $article) {
                $template_articles .= $this->replaceArticle($template_parts[1], $article);
            }
        }
        $template = $template_parts[0] . $template_articles . $template_parts[2];

        echo $template;
    }

    private function replaceArticle($template, Article $article)
    {
        $template = str_replace(self::KEY_ARTICLE_ID, $article->getId(), $template);
        $template = str_replace(self::KEY_ARTICLE_TITLE, $article->getTitle(), $template);
        return $template;
    }
}
