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


interface ProfileInterface
{
    const KEY_MENU = "##PROFILE_MENU##";
    const KEY_MENU_ITEM_CLASS = '##MENU_ITEM_CLASS##';
    const KEY_SECTION_WRITER = '##SECTION_WRITER##';
    const KEY_SECTION_MODERATOR = '##SECTION_MODERATOR##';
    const KEY_SECTION_ADMIN = '##SECTION_ADMIN##';
    const KEY_WRITER_TABLE = '##PROFILE_WRITER_TABLE##';
    const KEY_WRITER_TABLE_ROW = '##PROFILE_WRITER_TABLE_ROW##';
    const KEY_ADMIN_TABLE = '##PROFILE_ADMIN_TABLE##';
    const KEY_ADMIN_TABLE_ROW = '##PROFILE_ADMIN_TABLE_ROW##';
}
