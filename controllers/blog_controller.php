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

namespace Controllers;

use Core\AbstractController;
use Models\Article;
use Views\ArticleView;
use Views\BlogView;


class BlogController extends AbstractController
{
    const ARTICLES_PER_PAGE = 10;

    /**
     * BlogController constructor.
     */
    public function __construct()
    {
        if (!isset($_GET['article'])) {
            // It has not been specifically requested any article, so we show show all that fit the page.
            $total_articles = Article::count();

            if ($total_articles <= self::ARTICLES_PER_PAGE) {
                // Show all articles available, as they are less than the articles that fit on one page.
                $this->setView(new BlogView(Article::getAll()));
            } else {
                // Calculate the number of pages needed
                $total_pages = ceil($total_articles / self::ARTICLES_PER_PAGE);

                // Get the requested page
                $current_page = min($total_pages, filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT,
                    array('options' => array('default' => 1, 'min_range' => 1,))));

                // Calculate the first and last article to query
                $first_article = 1 + ($current_page - 1) * self::ARTICLES_PER_PAGE;
                $last_article = min(($first_article + self::ARTICLES_PER_PAGE), $total_articles + 1);

                // Get articles to show
                $articles = Article::getRangeById($first_article, $last_article);

                // Set the view that will be rendered
                $this->setView(new BlogView($articles, $total_pages, $current_page));
            }
        } else {
            // It has been specifically requested an article, so we show only that.
            $this->setView(new ArticleView(Article::getById($_GET['article'])));
        }
    }
}
