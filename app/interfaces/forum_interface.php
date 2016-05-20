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


interface ForumInterface
{
    const KEY_FORUM_CHILDREN = "##FORUM_CHILDREN##";
    const KEY_FORUM_CHILD = "##FORUM_CHILD##";
    const KEY_FORUM_CHILD_ID = "##FORUM_CHILD_ID##";
    const KEY_FORUM_CHILD_NAME = "##FORUM_CHILD_NAME##";
    const KEY_FORUM_CHILD_DESCRIPTION = "##FORUM_CHILD_DESCRIPTION##";
    const KEY_FORUM_CHILD_COUNT_CHILDREN = "##FORUM_CHILD_COUNT_CHILDREN##";
    const KEY_FORUM_CHILD_COUNT_THREADS = "##FORUM_CHILD_COUNT_THREADS##";
    const KEY_FORUM_THREADS = "##FORUM_THREADS##";
    const KEY_FORUM_THREAD = "##FORUM_THREAD##";
    const KEY_FORUM_THREAD_ID = "##FORUM_THREAD_ID##";
    const KEY_FORUM_THREAD_NAME = "##FORUM_THREAD_NAME##";
    const KEY_FORUM_THREAD_AUTHOR_ID = "##FORUM_THREAD_AUTHOR_ID##";
    const KEY_FORUM_THREAD_AUTHOR_NAME = "##FORUM_THREAD_AUTHOR_NAME##";
    const KEY_FORUM_THREAD_COUNT_COMMENTS = "##FORUM_THREAD_COUNT_COMMENTS##";
}
