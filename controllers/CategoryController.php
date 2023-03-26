<?php


namespace CMW\Controller\Forum;

use CMW\Controller\Core\CoreController;
use CMW\Controller\Users\UsersController;
use CMW\Manager\Lang\LangManager;
use CMW\Model\Forum\CategoryModel;
use CMW\Model\Forum\ForumModel;
use CMW\Model\Forum\ResponseModel;
use CMW\Model\Forum\TopicModel;
use CMW\Model\users\UsersModel;
use CMW\Router\Link;
use CMW\Utils\Response;
use CMW\Utils\Utils;
use CMW\Utils\View;


/**
 * Class: @CategoryController
 * @package Forum
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class CategoryController extends CoreController
{

    private ForumModel $forumModel;
    private CategoryModel $categoryModel;

    public function __construct($theme_path = null)
    {
        parent::__construct($theme_path);
        $this->forumModel = new ForumModel();
        $this->categoryModel = new CategoryModel();
    }


    #[Link("/add", Link::GET, [], "/cmw-admin/forum/categories")]
    public function adminAddCategoryView(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.categories.add");

        View::createAdminView("forum", "categories/addCategory")
            ->view();
    }

    #[Link("/add", Link::POST, [], "/cmw-admin/forum/categories")]
    public function adminAddCategoryPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.categories.add");

        if (Utils::isValuesEmpty($_POST, "name", "description")) {
            Response::sendAlert("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("forum.category.toaster.error.empty_input"));
            Utils::refreshPage();
            return;
        }

        [$name, $description] = Utils::filterInput("name", "description");

        $this->categoryModel->createCategory($name, $description);

        Response::sendAlert("success", LangManager::translate("core.toaster.success"),
            LangManager::translate("forum.category.toaster.success"));

        header("location: list");
    }

    #[Link("/list", Link::GET, [], "/cmw-admin/forum/categories")]
    public function adminListCategoryView(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.categories.list");

        View::createAdminView("forum", "categories/listCategory")
            ->addVariableList(["categoryModel" => $this->categoryModel])
            ->addStyle("admin/resources/vendors/simple-datatables/css/simple-datatables.css")
            ->addScriptBefore("admin/resources/vendors/simple-datatables/js/simple-datatables.js")
            ->view();
    }

    #[Link("/delete/:id", Link::GET, ['[0-9]+'], "/cmw-admin/forum/categories")]
    public function adminDeleteCategory(int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.categories.delete");

        $category = $this->categoryModel->getCategoryById($id);

        if (is_null($category)) {
            Response::sendAlert("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.internalError"));

            header("location: ../list/");
            return;
        }

        $this->categoryModel->deleteCategory($id);

        Response::sendAlert("success", LangManager::translate("core.toaster.success"),
            LangManager::translate("forum.category.delete.success"));

        header("location: ../list/");
    }
}