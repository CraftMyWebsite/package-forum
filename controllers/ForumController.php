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

    #[Link("/add", Link::POST, [], "/cmw-admin/forum/forums")]
    public function adminAddForumPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.add");

        [$name, $icon, $description, $categoryId] = Utils::filterInput("name", "icon", "description", "category_id");

        $this->forumModel->createForum($name, $icon, $description, $categoryId);

        Response::sendAlert("success", LangManager::translate("core.toaster.success"),
            LangManager::translate("forum.forum.add.toaster.success"));

        header("location: ../manage");
    }

    #[Link("/delete/:id", Link::GET, ['[0-9]+'], "/cmw-admin/forum/forums")]
    public function adminDeleteForum(int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.forum.delete");

        $forum = $this->forumModel->getForumById($id);

        if (is_null($forum)) {
            Response::sendAlert("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.internalError"));

            header("location: ../../manage/");
            return;
        }

        $this->forumModel->deleteForum($id);

        Response::sendAlert("success", LangManager::translate("core.toaster.success"),
            LangManager::translate("forum.forum.delete.success"));

        header("location: ../../manage/");
    }

}