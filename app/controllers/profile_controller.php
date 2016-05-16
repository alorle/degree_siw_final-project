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

namespace app\controllers;


use App\Core\AbstractController;
use App\Models\Article;
use App\Models\Session;
use App\Models\User;
use App\Views\ErrorView;
use App\Views\Profile\AdminProfileView;
use App\Views\Profile\BlogProfileView;
use App\Views\Profile\ForumProfileView;
use App\Views\Profile\MainProfileView;

class ProfileController extends AbstractController
{
    const ARTICLES_PER_PAGE = 9;

    const KEY_POST_WRITER = 'writer';
    const KEY_POST_MODERATOR = 'moderator';
    const KEY_POST_ADMIN = 'admin';

    const KEY_POST_UPDATE = 'update';
    const KEY_POST_DELETE = 'delete';

    /**
     * ProfileController constructor.
     * @param $params
     * @throws \Exception
     */
    public function __construct($params)
    {
        $action = '';
        if (isset($params[0])) {
            $action = $params[0];
        }

        if (!is_null($user = Session::getCurrentUser())) {
            switch ($action) {
                case 'blog':
                    $this->blog($user, $params[1]);
                    break;
                case 'forum':
                    $this->setView(new ForumProfileView($user));
                    break;
                case 'admin':
                    if (!empty($params[1])) {
                        $this->adminUser($params[1]);
                    } else {
                        $this->setView(new AdminProfileView($user));
                    }
                    break;
                default:
                    $this->setView(new MainProfileView($user));
            }
        } else {
            redirect(PROJECT_BASE_URL . '/session/login');
        }
    }

    private function blog(User $user, $requested_page)
    {
        $articles = Article::getByAuthorUsername($user->getUsername());

        $total_articles = count($articles);

        // Calculate the number of pages needed
        $total_pages = ceil($total_articles / self::ARTICLES_PER_PAGE);

        // Get the requested page
        $current_page = 1;
        if (isset($requested_page) && !filter_var($requested_page, FILTER_VALIDATE_INT) === false) {
            $current_page = min($total_pages, $requested_page);
        }

        // Get articles to show
        $articles = Article::getAll(self::ARTICLES_PER_PAGE, ($current_page - 1) * self::ARTICLES_PER_PAGE);

        $this->setView(new BlogProfileView($user, $articles, $total_pages, $current_page));
    }

    private function adminUser($username)
    {
        $username = filter_var($username, FILTER_SANITIZE_STRING);
        if (is_null(User::getByUsername($username))) {
            $this->setView(new ErrorView(404, 'Not found', 'El usuario "' . $username . '" no existe.'));
        } else {
            if (isset($_POST[self::KEY_POST_UPDATE])) {
                $writer = isset($_POST[self::KEY_POST_WRITER]) && $_POST[self::KEY_POST_WRITER] ? "1" : "0";
                $moderator = isset($_POST[self::KEY_POST_MODERATOR]) && $_POST[self::KEY_POST_MODERATOR] ? "1" : "0";
                $admin = isset($_POST[self::KEY_POST_ADMIN]) && $_POST[self::KEY_POST_ADMIN] ? "1" : "0";

                $updated = User::updatePermissions($username, array(
                    User::COLUMN_WRITER => $writer,
                    User::COLUMN_MODERATOR => $moderator,
                    User::COLUMN_ADMIN => $admin));

                if ($updated) {
                    redirect(PROJECT_BASE_URL . '/profile/admin');
                } else {
                    throw new \Exception('User permissions could not be updated', 500);
                }
            } elseif (isset($_POST[self::KEY_POST_DELETE])) {
                $deleted = User::delete($username);

                if ($deleted) {
                    redirect(PROJECT_BASE_URL . '/profile/admin');
                } else {
                    throw new \Exception('User permissions could not be updated', 500);
                }
            } else {
                redirect(PROJECT_BASE_URL . '/profile/admin');
            }
        }
    }
}
