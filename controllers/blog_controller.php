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
    /**
     * BlogController constructor.
     */
    public function __construct()
    {
        if (!isset($_GET['article'])) {
            // It has not been specifically requested any article, so we show everyone.
            $this->setView(new BlogView(Article::getAll()));
        } else {
            // It has been specifically requested an article, so we show only that.
            $this->setView(new ArticleView(Article::getById($_GET['article'])));
        }
    }
}
