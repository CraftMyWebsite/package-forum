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
 * Class: @ForumController
 * @package Forum
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ForumController extends CoreController
{

    private ForumModel $forumModel;
    private CategoryModel $categoryModel;
    private ResponseModel $responseModel;
    private TopicModel $topicModel;
    private UsersModel $usersModel;

    public function __construct($theme_path = null)
    {
        parent::__construct($theme_path);
        $this->forumModel = new ForumModel();
        $this->categoryModel = new CategoryModel();
        $this->responseModel = new ResponseModel();
        $this->topicModel = new TopicModel();

        $this->usersModel = new UsersModel();
    }

    #[Link("/list", Link::GET, [], "/cmw-admin/forum/forums")]
    public function adminListForumView(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.list");

        View::createAdminView("forum", "forums/listForum")
            ->addVariableList(["forum" => $this->forumModel])
            ->addStyle("admin/resources/vendors/simple-datatables/css/simple-datatables.css")
            ->addScriptBefore("admin/resources/vendors/simple-datatables/js/simple-datatables.js")
            ->view();
    }

    #[Link("/add", Link::GET, [], "/cmw-admin/forum/forums")]
    public function adminAddForumView(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.add");

        View::createAdminView("forum", "forums/addForum")
            ->addVariableList(["categories" => $this->categoryModel->getCategories()])
            ->view();
    }

    #[Link("/add", Link::POST, [], "/cmw-admin/forum/forums")]
    public function adminAddForumPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.add");

        [$name, $description, $categoryId] = Utils::filterInput("name", "description", "category_id");

        $this->forumModel->createForum($name, $description, $categoryId);

        Response::sendAlert("success", LangManager::translate("core.toaster.success"),
            LangManager::translate("forum.forum.add.toaster.success"));

        header("location: list");
    }

    #[Link("/delete/:id", Link::GET, ['[0-9]+'], "/cmw-admin/forum/forums")]
    public function adminDeleteForum(int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.forum.delete");

        $forum = $this->forumModel->getForumById($id);

        if (is_null($forum)) {
            Response::sendAlert("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.internalError"));

            header("location: ../list/");
            return;
        }

        $this->forumModel->deleteForum($id);

        Response::sendAlert("success", LangManager::translate("core.toaster.success"),
            LangManager::translate("forum.forum.delete.success"));

        header("location: ../list/");
    }

}