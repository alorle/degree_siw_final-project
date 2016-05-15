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


use App\Core\AbstractPartial;
use App\Core\AbstractView;
use App\Interfaces\ProfileInterface;
use App\Models\User;
use App\Views\FooterPartial;
use App\Views\HeaderPartial;

abstract class AbstractProfileView extends AbstractView implements ProfileInterface
{
    private $lateral_menu_template;

    private $user;

    /**
     * MainProfileView constructor.
     * @param User $user
     * @param AbstractPartial|null $header
     * @param AbstractPartial|null $footer
     */
    public function __construct($user, $header = null, $footer = null)
    {
        parent::__construct(new HeaderPartial(), new FooterPartial());
        $this->setLateralMenuTemplateFile(FOLDER_TEMPLATES . DIRECTORY_SEPARATOR . 'profile' . DIRECTORY_SEPARATOR .
            'menu.html');
        $this->user = $user;
    }

    public function render()
    {
        $template = parent::render();

        if (!$this->user->isWriter() && !$this->user->isModerator() && !$this->user->isAdmin()) {
            $template = str_replace(self::KEY_MENU, '', $template);
        } else {
            $template = str_replace(self::KEY_MENU, $this->readLateralMenuFile(), $template);

            $template_parts = explode(self::KEY_SECTION_WRITER, $template);
            if ($this->user->isWriter()) {
                $template = $template_parts[0] . $template_parts[1] . $template_parts[2];
            } else {
                $template = $template_parts[0] . $template_parts[2];
            }

            $template_parts = explode(self::KEY_SECTION_MODERATOR, $template);
            if ($this->user->isModerator()) {
                $template = $template_parts[0] . $template_parts[1] . $template_parts[2];
            } else {
                $template = $template_parts[0] . $template_parts[2];
            }

            $template_parts = explode(self::KEY_SECTION_ADMIN, $template);
            if ($this->user->isAdmin()) {
                $template = $template_parts[0] . $template_parts[1] . $template_parts[2];
            } else {
                $template = $template_parts[0] . $template_parts[2];
            }

            $template = str_replace(self::KEY_BASE_URL, PROJECT_BASE_URL, $template);
        }

        return $template;
    }

    /**
     * Set name of the file containing the lateral menu template
     * @param string $file
     */
    public function setLateralMenuTemplateFile($file)
    {
        $this->lateral_menu_template = $file;
    }

    /**
     * Read lateral menu file
     * @return string Lateral menu file content or empty string if file is not defined
     */
    private function readLateralMenuFile()
    {
        if (isset($this->lateral_menu_template) && file_exists($this->lateral_menu_template)) {
            return file_get_contents($this->lateral_menu_template);
        }
        return '';
    }
}
