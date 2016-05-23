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
    const USERS_PER_PAGE = 9;

    const KEY_POST_WRITER = 'writer';
    const KEY_POST_MODERATOR = 'moderator';
    const KEY_POST_ADMIN = 'admin';

    const KEY_POST_UPDATE = 'update';
    const KEY_POST_DELETE = 'delete';
    const KEY_POST_PROFILE_IMAGE = 'profile-image';

    const KEY_POST_UPDATE_FORM_NAME = 'name';
    const KEY_POST_UPDATE_FORM_EMAIL = 'email';
    const KEY_POST_UPDATE_FORM_PASSWORD = 'password';
    const KEY_POST_UPDATE_FORM_PASSWORD_NEW = 'password_new';

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
                        $this->adminUser($user, $params[1]);
                    } else {
                        $this->adminUser($user, 1);
                    }
                    break;
                default:
                    $this->profile($user);
            }
        } else {
            redirect(PROJECT_BASE_URL . '/session/login');
        }
    }

    private function profile(User $user)
    {
        if (isset($_POST[self::KEY_POST_UPDATE])) {
            // We have received the update form, so check fields
            $name = filter_var($_POST[self::KEY_POST_UPDATE_FORM_NAME], FILTER_SANITIZE_STRING);
            $email = filter_var($_POST[self::KEY_POST_UPDATE_FORM_EMAIL], FILTER_SANITIZE_EMAIL);
            $password = filter_var($_POST[self::KEY_POST_UPDATE_FORM_PASSWORD], FILTER_SANITIZE_STRING);
            $password_new = filter_var($_POST[self::KEY_POST_UPDATE_FORM_PASSWORD_NEW], FILTER_SANITIZE_STRING);

            $new_data = array();
            if (!empty($name)) {
                $new_data[User::COLUMN_NAME] = $name;
            }
            if (!empty($email)) {
                $new_data[User::COLUMN_EMAIL] = $email;
            }
            if (!empty($password_new)) {
                if (!empty($password) && strcmp($user->getPassword(), md5($password)) == 0) {
                    $new_data[User::COLUMN_PASSWORD] = md5($password_new);
                } else {
                    $this->setView(new MainProfileView($user, '', 'Contraseña incorrecta'));
                    return;
                }
            }

            $updated = User::update($user->getId(), $new_data);

            if ($updated === true) {
                $this->setView(new MainProfileView($user, '', 'Datos actualizados correctamente'));
            } else {
                throw new \Exception('Data could not be stored', 500);
            }
        } elseif (isset($_POST[self::KEY_POST_PROFILE_IMAGE])) {
            if (isset($_FILES[self::KEY_POST_PROFILE_IMAGE])) {
                $file = $_FILES[self::KEY_POST_PROFILE_IMAGE];

                $result = 5;
                if (empty($file['tmp_name'])) {
                    $result = 1;
                } elseif (getimagesize($file['tmp_name']) === false) {
                    $result = 2;
                } else {
                    $tmp = explode('.', $file['name']);
                    $target = FOLDER_PROFILE_IMAGES . DIRECTORY_SEPARATOR . $user->getId() . '.' . end($tmp);
                    if (move_uploaded_file($file['tmp_name'], $target)) {
                        if (User::updateProfileImage($user->getId(), $user->getId() . '.' . end($tmp))) {
                            $result = 0;
                        } else {
                            $result = 3;
                        }
                    } else {
                        $result = 4;
                    }
                }
                $image_msg = array('Imagen actualizada',
                    'Ningún fichero seleccionado. Pincha en la imagen superior para seleccionar una nueva imagen.',
                    'El fichero no es una imagen. Seleccione un fichero válido.',
                    'Hubo un problema al asociar al usaurio con su nueva imagen.',
                    'Hubo un problema al copiar la imagen al servidor.',
                    'Hubo un error desconocido.');
                $this->setView(new MainProfileView($user, $image_msg[$result], ''));
            } else {
                throw new \Exception('Internal server error', 500);
            }
        } else {
            $this->setView(new MainProfileView($user));
        }
    }

    private function blog(User $user, $requested_page)
    {
        $articles = Article::getByAuthorId($user->getId());

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

    private function adminUser(User $user, $param)
    {
        $param = filter_var($param, FILTER_SANITIZE_STRING);
        if (is_null(User::getById($param))) {
            if (!filter_var($param, FILTER_VALIDATE_INT) === false) {
                $requested_page = $param;

                $users = User::getAll();

                $total_users = count($users);

                // Calculate the number of pages needed
                $total_pages = ceil($total_users / self::USERS_PER_PAGE);

                // Get the requested page
                $current_page = 1;
                if (isset($requested_page) && !filter_var($requested_page, FILTER_VALIDATE_INT) === false) {
                    $current_page = min($total_pages, $requested_page);
                }

                // Get articles to show
                $users = User::getAll(self::USERS_PER_PAGE, ($current_page - 1) * self::USERS_PER_PAGE);

                $this->setView(new AdminProfileView($user, $users, $total_pages, $current_page));
            } else {
                $this->setView(new ErrorView(404, 'Not found', 'El usuario "' . $param . '" no existe.'));
            }
        } else {
            $id = $param;
            if (isset($_POST[self::KEY_POST_UPDATE])) {
                $writer = isset($_POST[self::KEY_POST_WRITER]) && $_POST[self::KEY_POST_WRITER] ? "1" : "0";
                $moderator = isset($_POST[self::KEY_POST_MODERATOR]) && $_POST[self::KEY_POST_MODERATOR] ? "1" : "0";
                $admin = isset($_POST[self::KEY_POST_ADMIN]) && $_POST[self::KEY_POST_ADMIN] ? "1" : "0";

                $updated = User::updatePermissions($id, array(
                    User::COLUMN_WRITER => $writer,
                    User::COLUMN_MODERATOR => $moderator,
                    User::COLUMN_ADMIN => $admin));

                if ($updated) {
                    redirect(PROJECT_BASE_URL . '/profile/admin');
                } else {
                    throw new \Exception('User permissions could not be updated', 500);
                }
            } elseif (isset($_POST[self::KEY_POST_DELETE])) {
                $deleted = User::delete($id);

                if ($deleted) {
                    redirect(PROJECT_BASE_URL . '/profile/admin');
                } else {
                    throw new \Exception('User could not be deleted', 500);
                }
            } else {
                redirect(PROJECT_BASE_URL . '/profile/admin');
            }
        }
    }
}
