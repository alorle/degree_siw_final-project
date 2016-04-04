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
use Models\Article;
use views\parts\FooterPartialView;
use views\parts\HeaderPartialView;

class BlogView extends AbstractView implements BlogInterface
{
    private $articles;
    private $total_pages;
    private $current_page;

    /**
     * BlogView constructor.
     * @param $articles array
     * @param $total_pages
     * @param $current_page
     */
    public function __construct($articles, $total_pages = null, $current_page = null)
    {
        parent::__construct(new HeaderPartialView(true), new FooterPartialView());

        $this->setTemplateFile(PROJECT_TEMPLATES_PATH . DIRECTORY_SEPARATOR . 'blog.html');
        $this->setTitle('Blog | ' . PROJECT_NAME);

        $this->articles = $articles;
        $this->total_pages = $total_pages;
        $this->current_page = $current_page;
    }

    public function render()
    {
        $template = parent::render();

        $template_parts = explode(self::KEY_ARTICLE_EXPLODE, $template);
        $template_articles = '';
        foreach ($this->articles as $article) {
            $template_articles .= $this->replaceArticleData($template_parts[1], $article);
        }

        $template = $template_parts[0] . $template_articles . $template_parts[2];

        $template_parts = explode(self::KEY_PAGINATION, $template);
        if (isset($this->current_page) && isset($this->total_pages)) {
            $template = $template_parts[0] . $template_parts[1] . $template_parts[2];

            $template_parts = explode(self::KEY_PAGINATION_FIRST, $template);
            if ($this->current_page <= 2) {
                $template = $template_parts[0] . $template_parts[2];
            } else {
                $template = $template_parts[0] . $template_parts[1] . $template_parts[2];
                $template = str_replace(self::KEY_PAGINATION_FIRST_ID, 1, $template);
            }

            $template_parts = explode(self::KEY_PAGINATION_PREV, $template);
            if ($this->current_page <= 1) {
                $template = $template_parts[0] . $template_parts[2];
            } else {
                $template = $template_parts[0] . $template_parts[1] . $template_parts[2];
                $template = str_replace(self::KEY_PAGINATION_PREV_ID, $this->current_page - 1, $template);
            }

            $template_parts = explode(self::KEY_PAGINATION_NEXT, $template);
            if ($this->current_page > $this->total_pages - 1) {
                $template = $template_parts[0] . $template_parts[2];
            } else {
                $template = $template_parts[0] . $template_parts[1] . $template_parts[2];
                $template = str_replace(self::KEY_PAGINATION_NEXT_ID, $this->current_page + 1, $template);
            }

            $template_parts = explode(self::KEY_PAGINATION_LAST, $template);
            if ($this->current_page > $this->total_pages - 2) {
                $template = $template_parts[0] . $template_parts[2];
            } else {
                $template = $template_parts[0] . $template_parts[1] . $template_parts[2];
                $template = str_replace(self::KEY_PAGINATION_LAST_ID, $this->total_pages, $template);
            }

            $template = str_replace(self::KEY_PAGINATION_CURRENT_ID, $this->current_page, $template);
        } else {
            $template = $template_parts[0] . $template_parts[2];
        }

        echo $template;
    }

    private static function replaceArticleData($template, Article $article)
    {
        $template = str_replace(self::KEY_ARTICLE_ID, $article->getId(), $template);
        $template = str_replace(self::KEY_ARTICLE_TITLE, $article->getTitle(), $template);
        $template = str_replace(self::KEY_ARTICLE_BODY, $article->getBody(), $template);
        $template = str_replace(self::KEY_ARTICLE_AUTHOR, $article->getAuthorName(), $template);
        $template = str_replace(self::KEY_ARTICLE_TIME, $article->getTime(), $template);
        return $template;
    }
}
