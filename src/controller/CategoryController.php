<?php

namespace src\controller;

use src\Models\CategoryModel;
use src\Constants\ErrorMessage;

class CategoryController extends BaseController
{
    /**
     * Displays the list of categories with the count of associated posts in the admin interface.
     *
     * Retrieves all categories along with the count of associated posts from the database
     * and displays them in the corresponding view.
     */
    public function index()
    {
        $categoriesModel = new CategoryModel();

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 14;
        $offset = ($page - 1) * $limit;

        $sortColumn = $_GET['sortColumn'] ?? null; // Par défaut, tri par ID
        $sortOrder = $_GET['sortOrder'] ?? 'desc'; // Par défaut, ordre décroissant

        if ($sortColumn) {
            $indexCategories = $categoriesModel->findAllCategoryWithPagination($offset, $limit, $sortColumn, $sortOrder);
        } else {
            $indexCategories = $categoriesModel->findAllCategoryWithPagination($offset, $limit);
        }

        $totalCategories = $categoriesModel->countCategories();
        $totalPages = ceil($totalCategories / $limit);

        $this->twig->display('admin/categories/index.html.twig', [
            'indexCategories' => $indexCategories,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'sortColumn' => $sortColumn,
            'sortOrder' => $sortOrder,
        ]);
    }

    /**
     * Manages the creation of a new category in the admin interface.
     *
     * Processes the form submission, validates input, checks for uniqueness,
     * and creates a new category in the database if all conditions are met.
     * In case of error, displays error messages or redirects to an error page.
     *
     * @throws \Exception On error, redirects to error page 500.
     *
     * @return void
     */
    public function create()
    {
        $errors = [];

        try {
            if (isset($_POST['submit'])) {
                $nameCategory = trim(htmlspecialchars($_POST['name-category'], ENT_QUOTES, 'UTF-8'));
                $descriptionCategory = trim(htmlspecialchars($_POST['description-category'], ENT_QUOTES, 'UTF-8'));

                if (empty($nameCategory)) {
                    $errors['name-category'] = ErrorMessage::NAMECATEGORY_INVALID;
                } else {
                    $categoriesModel = new CategoryModel();
                    $existingCategories = $categoriesModel->findAll();

                    foreach ($existingCategories as $existingCategory) {
                        if (strtolower($existingCategory->name_category) === strtolower($nameCategory)) {
                            $errors['unique-category'] = ErrorMessage::UNIQUENAMECATEGORY_INVALID;
                            break;
                        }
                    }
                }

                if (empty($descriptionCategory)) {
                    $errors['description-category'] = ErrorMessage::DESCRIPTIONCATEGORY_INVALID;
                }

                if (empty($errors)) {

                    $posts = new CategoryModel();

                    $posts->setNameCategory($nameCategory)
                        ->setDescriptionCategory($descriptionCategory);

                    $posts->createCategory();
                    header('Location: index');
                }
            }

            $this->twig->display('admin/categories/create.html.twig', [
                'errors' => $errors,
            ]);
        } catch (\Exception $e) {
            header('Location: /error-page-500');
            exit;
        }
    }

    /**
     * Manages the editing of an existing category in the admin interface.
     *
     * Processes the form submission, validates input, and updates the category
     * in the database if all conditions are met. In case of error, displays
     * error messages or redirects to an error page.
     *
     * @param int $id The ID of the category to edit.
     *
     * @throws \Exception On error, redirects to error page 500.
     *
     * @return void
     */
    public function edit($id)
    {
        $errors = [];

        try {

            $categoriesModel = new CategoryModel();

            $category = $categoriesModel->find($id);

            if (isset($_POST['submit'])) {
                $nameCategory = trim(htmlspecialchars($_POST['name-category'], ENT_QUOTES, 'UTF-8'));
                $descriptionCategory = trim(htmlspecialchars($_POST['description-category'], ENT_QUOTES, 'UTF-8'));

                if (empty($nameCategory)) {
                    $errors['name-category'] = ErrorMessage::NAMECATEGORY_INVALID;
                }

                if (empty($descriptionCategory)) {
                    $errors['description-category'] = ErrorMessage::DESCRIPTIONCATEGORY_INVALID;
                }

                if (empty($errors)) {

                    $posts = new CategoryModel();

                    $posts->setNameCategory($nameCategory)
                        ->setDescriptionCategory($descriptionCategory);

                    $posts->update($id);
                    header('Location: ../index');
                }

            }

            $this->twig->display('admin/categories/edit.html.twig', [
                'errors' => $errors,
                'category' => $category,
            ]);

        } catch (\Exception $e) {
            header('Location: /error-page-500');
            exit;
        }
    }

    /**
     * Deletes a category in the admin interface.
     *
     * Finds the category by ID, checks if it exists, and deletes it from the database.
     * If the category doesn't exist, redirects to a 404 error page. In case of an error,
     * redirects to an error page 500.
     *
     * @param int $id The ID of the category to delete.
     *
     * @throws \Exception On error, redirects to error page 500.
     *
     * @return void
     */
    public function delete(int $id)
    {
        try {
            $categoriesModel = new CategoryModel();

            $categorie = $categoriesModel->find($id);

            if (!$categorie) {
                // La categorie n'existe pas, vous pouvez gérer cette situation
                // Redirection vers une page d'erreur ou autre
                header('Location: /error-page-404');
                exit;
            }

            $categoriesModel->delete($id);

            header('Location: ../index');

        } catch (\Exception $e) {
            header('Location: /error-page-500');
            exit;
        }
    }
}
