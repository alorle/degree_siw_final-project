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

namespace Interfaces;

interface BlogInterface
{
    const KEY_ARTICLE_EXPLODE = "##ARTICLES_LIST##";
    const KEY_ARTICLE_ID = "##ARTICLE_ID##";
    const KEY_ARTICLE_TITLE = "##ARTICLE_TITLE##";
    const KEY_ARTICLE_BODY = "##ARTICLE_BODY##";
    const KEY_ARTICLE_AUTHOR = "##ARTICLE_AUTHOR##";
    const KEY_ARTICLE_TIME = "##ARTICLE_TIME##";

    const KEY_PAGINATION = "##PAGINATION##";
    const KEY_PAGINATION_FIRST = "##PAGINATION_FIRST##";
    const KEY_PAGINATION_PREV = "##PAGINATION_PREV##";
    const KEY_PAGINATION_NEXT = "##PAGINATION_NEXT##";
    const KEY_PAGINATION_LAST = "##PAGINATION_LAST##";
    const KEY_PAGINATION_FIRST_ID = "##PAGINATION_FIRST_ID##";
    const KEY_PAGINATION_PREV_ID = "##PAGINATION_PREV_ID##";
    const KEY_PAGINATION_CURRENT_ID = "##PAGINATION_CURRENT_ID##";
    const KEY_PAGINATION_NEXT_ID = "##PAGINATION_NEXT_ID##";
    const KEY_PAGINATION_LAST_ID = "##PAGINATION_LAST_ID##";
}
