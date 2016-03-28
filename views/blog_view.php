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
use Interfaces\BlogInterface;

class BlogView extends AbstractView implements BlogInterface
{
    private $articles;

    /**
     * BlogView constructor.
     * @param $articles array
     */
    public function __construct($articles)
    {
        parent::__construct();

        $this->setFileTemplate(PROJECT_TEMPLATES_PATH . DIRECTORY_SEPARATOR . 'blog.html');
        $this->setTitle('Blog | ' . PROJECT_NAME);

        $this->articles = $articles;
    }

    public function render()
    {
        $template = parent::render();

        $template_parts = explode(self::KEY_ARTICLE_EXPLODE, $template);
        $template_articles = '';
        foreach ($this->articles as $article) {
            $template_articles .= $this->replaceArticleData($template_parts[1], $article);
        }

        echo $template_parts[0] . $template_articles . $template_parts[2];
    }

    private static function replaceArticleData($template, $article)
    {
        // TODO: replace template's tags with article data
        return "";
    }
}
