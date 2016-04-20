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

namespace App\Core;


abstract class AbstractController
{
    /**
     * @var AbstractView
     */
    private $view;

    /**
     * Renders the page associated with the controller
     * @throws \Exception
     */
    public function render()
    {
        if (isset($this->view)) {
            $this->view->render();
        } else {
            throw new \Exception('Controller has no view associated', 500);
        }
    }

    /**
     * @return AbstractView Controller's view
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @param $view AbstractView Controller's view
     * @throws \Exception
     */
    public function setView($view)
    {
        if ($view instanceof AbstractView) {
            $this->view = $view;
        } else {
            throw new \Exception('The view associated with the controller is not an instance of AbstractView', 500);
        }
    }
}
