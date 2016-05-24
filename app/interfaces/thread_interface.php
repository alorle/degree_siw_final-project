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

namespace App\Interfaces;


interface ThreadInterface
{
    const KEY_THREAD_ID = "##THREAD_ID##";
    const KEY_THREAD_TITLE = "##THREAD_TITLE##";
    const KEY_THREAD_COMMENTS = "##THREAD_COMMENTS##";
    const KEY_THREAD_COMMENT = "##THREAD_COMMENT##";
    const KEY_THREAD_COMMENT_ID = "##THREAD_COMMENT_ID##";
    const KEY_THREAD_COMMENT_TITLE = "##THREAD_COMMENT_TITLE##";
    const KEY_THREAD_COMMENT_BODY = "##THREAD_COMMENT_BODY##";
    const KEY_THREAD_COMMENT_TIME = "##THREAD_COMMENT_TIME##";
    const KEY_THREAD_COMMENT_AUTHOR_ID = "##THREAD_COMMENT_AUTHOR_ID##";
    const KEY_THREAD_COMMENT_AUTHOR_NAME = "##THREAD_COMMENT_AUTHOR_NAME##";
    const KEY_THREAD_COMMENT_AUTHOR_IMAGE = "##THREAD_COMMENT_AUTHOR_IMAGE##";

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
