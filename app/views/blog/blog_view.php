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

namespace App\Views\Blog;


use App\Core\AbstractView;
use App\Interfaces\BlogInterface;
use App\Models\Article;
use App\Models\User;
use App\Views\FooterPartial;
use App\Views\HeaderPartial;

class BlogView extends AbstractView implements BlogInterface
{
    private $articles;
    private $total_pages;
    private $current_page;

    /**
     * BlogView constructor.
     * @param $articles array Array of articles to show
     * @param $total_pages int Count of pages
     * @param $current_page int Current page number
     */
    public function __construct($articles, $total_pages = null, $current_page = null)
    {
        parent::__construct(new HeaderPartial(), new FooterPartial());
        $this->setTemplateFile(FOLDER_TEMPLATES . DIRECTORY_SEPARATOR . 'blog' . DIRECTORY_SEPARATOR . 'blog.html');
        $this->setTitle('Blog | ' . PROJECT_NAME);
        $this->articles = $articles;
        $this->total_pages = $total_pages;
        $this->current_page = $current_page;
    }

    public function render()
    {
        $template = parent::render();

        // Fill template with articles data
        $template_parts = explode(self::KEY_ARTICLE_EXPLODE, $template);
        $template_articles = '';
        foreach ($this->articles as $article) {
            $template_articles .= $this->replaceArticle($template_parts[1], $article);
        }
        $template = $template_parts[0] . $template_articles . $template_parts[2];

        // Fill pagination section
        $template = $this->replacePagination($template);

        echo $template;
    }

    private function replaceArticle($template, Article $article)
    {
        $template = str_replace(self::KEY_ARTICLE_ID, $article->getId(), $template);
        $template = str_replace(self::KEY_ARTICLE_TITLE, $article->getTitle(), $template);
        $template = str_replace(self::KEY_ARTICLE_SUMMARY, nl2br($article->getSummary()), $template);
        $template = str_replace(self::KEY_ARTICLE_TIME, $article->getTime(), $template);

        if (is_null($user = User::getById($article->getAuthorId()))) {
            $template = str_replace(self::KEY_ARTICLE_AUTHOR, $article->getAuthorId(), $template);
            $template = str_replace(self::KEY_ARTICLE_AUTHOR_LINK, '', $template);
        } else {
            $template = str_replace(self::KEY_ARTICLE_AUTHOR, $user->getName(), $template);
            $link = 'href="' . PROJECT_BASE_URL . '/user/' . $user->getId() . '"';
            $template = str_replace(self::KEY_ARTICLE_AUTHOR_LINK, $link, $template);
        }

        return $template;
    }

    private function replacePagination($template)
    {
        $template_parts = explode(self::KEY_PAGINATION, $template);
        if (isset($this->current_page) && isset($this->total_pages)) {
            // Pagination must be rendered
            $template = $template_parts[0] . $template_parts[1] . $template_parts[2];

            // Show first page if necessary
            $template_parts = explode(self::KEY_PAGINATION_FIRST, $template);
            if ($this->current_page <= 2) {
                $template = $template_parts[0] . $template_parts[2];
            } else {
                $template = $template_parts[0] . $template_parts[1] . $template_parts[2];
                $template = str_replace(self::KEY_PAGINATION_FIRST_ID, 1, $template);
            }

            // Show previous page if necessary
            $template_parts = explode(self::KEY_PAGINATION_PREV, $template);
            if ($this->current_page <= 1) {
                $template = $template_parts[0] . $template_parts[2];
            } else {
                $template = $template_parts[0] . $template_parts[1] . $template_parts[2];
                $template = str_replace(self::KEY_PAGINATION_PREV_ID, $this->current_page - 1, $template);
            }

            // Show current page
            $template = str_replace(self::KEY_PAGINATION_CURRENT_ID, $this->current_page, $template);

            // Show next page if necessary
            $template_parts = explode(self::KEY_PAGINATION_NEXT, $template);
            if ($this->current_page > $this->total_pages - 1) {
                $template = $template_parts[0] . $template_parts[2];
            } else {
                $template = $template_parts[0] . $template_parts[1] . $template_parts[2];
                $template = str_replace(self::KEY_PAGINATION_NEXT_ID, $this->current_page + 1, $template);
            }

            // Show last page if necessary
            $template_parts = explode(self::KEY_PAGINATION_LAST, $template);
            if ($this->current_page > $this->total_pages - 2) {
                $template = $template_parts[0] . $template_parts[2];
            } else {
                $template = $template_parts[0] . $template_parts[1] . $template_parts[2];
                $template = str_replace(self::KEY_PAGINATION_LAST_ID, $this->total_pages, $template);
            }
        } else {
            // Pagination is not necessary
            $template = $template_parts[0] . $template_parts[2];
        }

        return $template;
    }
}
