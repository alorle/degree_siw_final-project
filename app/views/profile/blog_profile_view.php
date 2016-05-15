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

namespace App\Views\Profile;


use App\Interfaces\ArticleInterface;
use App\Models\Article;
use App\Models\User;
use App\Views\FooterPartial;
use App\Views\HeaderPartial;

class BlogProfileView extends AbstractProfileView implements ArticleInterface
{
    const KEY_CONFIRM_DELETE = '##CONFIRM_DELETE##';

    /**
     * BlogProfileView constructor.
     * @param User $user
     */
    public function __construct($user)
    {
        parent::__construct($user, new HeaderPartial(), new FooterPartial());
        $this->setTemplateFile(FOLDER_TEMPLATES . DIRECTORY_SEPARATOR . 'profile' . DIRECTORY_SEPARATOR . 'blog.html');
        $this->setTitle($user->getName() . ' | ' . PROJECT_NAME);
    }

    public function render()
    {
        $template = parent::render();

        $writer_articles = Article::getByAuthorUsername($this->user->getUsername());
        $template_parts = explode(self::KEY_WRITER_TABLE, $template);
        if (!is_null($writer_articles) && count($writer_articles) > 0) {
            $template = $template_parts[0] . $template_parts[1] . $template_parts[2];

            $template_parts = explode(self::KEY_WRITER_TABLE_ROW, $template);
            $template_articles = '';
            foreach ($writer_articles as $article) {
                $template_articles .= $this->replaceArticle($template_parts[1], $article);
            }
            $template = $template_parts[0] . $template_articles . $template_parts[2];
        } else {
            $template = $template_parts[0] . $template_parts[2];
        }

        echo $template;
    }

    private function replaceArticle($template, Article $article)
    {
        $template = str_replace(self::KEY_ARTICLE_ID, $article->getId(), $template);
        $template = str_replace(self::KEY_ARTICLE_TITLE, $article->getTitle(), $template);
        $template = str_replace(self::KEY_ARTICLE_TIME, $article->getTime(), $template);

        $confirm_delete_question = '¿Quieres eliminar el artículo ' . $article->getId() . '?';
        $template = str_replace(self::KEY_CONFIRM_DELETE, $confirm_delete_question, $template);

        return $template;
    }
}
