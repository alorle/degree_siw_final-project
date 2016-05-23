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
use App\Views\Article\EditArticleView;
use App\Views\Article\NewArticleView;
use App\Views\Article\ShowArticleView;
use App\Views\ErrorView;
use lib\FPDF;

class ArticleController extends AbstractController
{
    const KEY_POST_NEW = 'new';
    const KEY_POST_NEW_TITLE = 'title';
    const KEY_POST_NEW_ID = 'id';
    const KEY_POST_NEW_BODY = 'body';

    const KEY_POST_EDIT = 'edit';
    const KEY_POST_EDIT_TITLE = 'title';
    const KEY_POST_EDIT_BODY = 'body';

    const KEY_POST_DELETE = 'delete';

    const STR_INVALID_FORM = 'Formulario invalido';

    /**
     * ArticleController constructor.
     * @param $params
     * @throws \Exception
     */
    public function __construct($params)
    {
        $action = '';
        if (isset($params[0])) {
            $action = $params[0];
        }

        switch ($action) {
            case 'new':
                $this->newArticle();
                break;
            case 'edit':
                $this->editArticle($params[1]);
                break;
            case 'delete':
                $this->deleteArticle($params[1]);
                break;
            case 'print':
                $this->printArticle($params[1]);
                break;
            default:
                $this->showArticle($params[0]);
                break;
        }
    }

    private function newArticle()
    {
        if (is_null($user = Session::getCurrentUser())) {
            // User is not identified
            redirect(PROJECT_BASE_URL . '/session/login');
        } elseif ($user->isWriter()) {
            // Logged user can write a new article
            if (isset($_POST[self::KEY_POST_NEW])) {
                // Form has been completed and submitted
                $title = filter_var($_POST[self::KEY_POST_NEW_TITLE], FILTER_SANITIZE_STRING);
                $id = filter_var($_POST[self::KEY_POST_NEW_ID], FILTER_SANITIZE_STRING);
                $body = filter_var($_POST[self::KEY_POST_NEW_BODY], FILTER_SANITIZE_STRING);

                if (empty($title) || empty($id) || empty($body)) {
                    // Fields are empty (after sanitize),
                    // so display same view with error message
                    $this->setView(new NewArticleView(self::STR_INVALID_FORM));
                } else {
                    // Check if id is unique
                    if (Article::existsId($id)) {
                        $this->setView(new NewArticleView('La URL ya existe. Prueba con otra diferente.'));
                        return;
                    }

                    // Insert the new article in the database
                    $inserted = Article::insert(array(
                        Article::COLUMN_ID => $id,
                        Article::COLUMN_TITLE => $title,
                        Article::COLUMN_BODY => $body,
                        Article::COLUMN_AUTHOR_ID => $user->getId()));

                    // If the insertion was successful, return to blog.
                    // In other case, show an error.
                    if ($inserted) {
                        redirect(PROJECT_BASE_URL . '/blog');
                    } else {
                        throw new \Exception('Data could not be stored', 500);
                    }
                }
            } else {
                $this->setView(new NewArticleView());
            }
        } else {
            // Logged user can not write a new article
            $this->setView(new ErrorView(403, 'Forbidden', 'No está autorizado a escribir nuevos artículos.'));
        }
    }

    private function editArticle($id)
    {
        $id = filter_var($id, FILTER_SANITIZE_STRING);
        if (is_null($article = Article::getById($id))) {
            $this->setView(new ErrorView(404, 'Not found', 'El articulo "' . $id . '" no existe.'));
        } else {
            if (is_null($user = Session::getCurrentUser())) {
                // User is not identified
                redirect(PROJECT_BASE_URL . '/session/login');
            } elseif ($user->getId() == $article->getAuthorId()) {
                // Logged user can modify the article
                if (isset($_POST[self::KEY_POST_DELETE])) {
                    $deleted = Article::delete($id);

                    // If the update was successful, return to blog.
                    // In other case, show an error.
                    if ($deleted) {
                        redirect(PROJECT_BASE_URL . '/blog');
                    } else {
                        throw new \Exception('Data could not be updated', 500);
                    }
                } elseif (isset($_POST[self::KEY_POST_EDIT])) {
                    // Form has been completed and submitted
                    $title = filter_var($_POST[self::KEY_POST_EDIT_TITLE], FILTER_SANITIZE_STRING);
                    $body = filter_var($_POST[self::KEY_POST_EDIT_BODY], FILTER_SANITIZE_STRING);

                    if (empty($title) || empty($body)) {
                        // Fields are empty (after sanitize),
                        // so display same view with error message
                        $this->setView(new EditArticleView($article, self::STR_INVALID_FORM));
                    } else {
                        // Update the article
                        $updated = Article::updateTitleAndBody($id, $title, $body);

                        // If the update was successful, return to blog.
                        // In other case, show an error.
                        if ($updated) {
                            redirect(PROJECT_BASE_URL . '/blog');
                        } else {
                            throw new \Exception('Data could not be updated', 500);
                        }
                    }
                } else {
                    $this->setView(new EditArticleView($article));
                }
            } else {
                // Logged user can not modify the article
                $this->setView(new ErrorView(403, 'Forbidden', 'No está autorizado a modificar este artículo.'));
            }
        }
    }

    private function deleteArticle($id)
    {
        $id = filter_var($id, FILTER_SANITIZE_STRING);
        if (is_null($article = Article::getById($id))) {
            $this->setView(new ErrorView(404, 'Not found', 'El articulo "' . $id . '" no existe.'));
        } else {
            if (is_null($user = Session::getCurrentUser())) {
                // User is not identified
                redirect(PROJECT_BASE_URL . '/session/login');
            } elseif ($user->getId() == $article->getAuthorId()) {
                // Logged user can modify the article
                $deleted = Article::delete($id);

                // If the update was successful, return to profile/blog.
                // In other case, show an error.
                if ($deleted) {
                    redirect(PROJECT_BASE_URL . '/profile/blog');
                } else {
                    throw new \Exception('Data could not be updated', 500);
                }
            } else {
                // Logged user can not modify the article
                $this->setView(new ErrorView(403, 'Forbidden', 'No está autorizado a modificar este artículo.'));
            }
        }
    }

    private function showArticle($id)
    {
        $id = filter_var($id, FILTER_SANITIZE_STRING);
        if (is_null($article = Article::getById($id))) {
            $this->setView(new ErrorView(404, 'Not found', 'El articulo "' . $id . '" no existe.'));
        } else {
            $this->setView(new ShowArticleView($article));
        }
    }

    private function printArticle($id)
    {
        if (is_null($article = Article::getById($id))) {
            $this->setView(new ErrorView(404, 'Not found', 'El articulo "' . $id . '" no existe.'));
        } else {
            $author = $article->getAuthorId();
            if (!is_null($user = User::getById($author))) {
                $author = $user->getName();
            }
            $pdf = new FPDF();
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->Cell(190, 10, utf8_decode(PROJECT_NAME . ' | ' . $article->getTitle()));
            $pdf->Ln();
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->Cell(190, 10, 'Autor: ' . utf8_decode($author));
            $pdf->Ln();
            $pdf->Cell(190, 10, 'Fecha: ' . $article->getTime());
            $pdf->Ln();
            $pdf->SetFont('Arial', '', 12);
            $pdf->MultiCell(190, 10, utf8_decode($article->getBody()));
            $pdf->Output();
            die();
        }
    }
}
