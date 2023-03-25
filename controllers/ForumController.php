<?php


namespace CMW\Controller\Forum;

use CMW\Controller\Core\CoreController;
use CMW\Controller\Users\UsersController;
use CMW\Manager\Lang\LangManager;
use CMW\Model\Forum\ForumModel;
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
    private UsersModel $usersModel;

    public function __construct($theme_path = null)
    {
        parent::__construct($theme_path);
        $this->forumModel = new ForumModel();
        $this->usersModel = new UsersModel();
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

        $this->forumModel->createCategory($name, $description);

        Response::sendAlert("success", LangManager::translate("core.toaster.success"),
            LangManager::translate("forum.category.toaster.success"));

        header("location: list");
    }

    #[Link("/list", Link::GET, [], "/cmw-admin/forum/categories")]
    public function adminListCategoryView(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.categories.list");

        View::createAdminView("forum", "categories/listCategory")
            ->addVariableList(["forum" => $this->forumModel])
            ->addStyle("admin/resources/vendors/simple-datatables/css/simple-datatables.css")
            ->addScriptBefore("admin/resources/vendors/simple-datatables/js/simple-datatables.js")
            ->view();
    }

    #[Link("/delete/:id", Link::GET, ['[0-9]+'], "/cmw-admin/forum/categories")]
    public function adminDeleteCategoryPost(int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.categories.list");

        $category = $this->forumModel->getCategoryById($id);

        if (is_null($category)) {
            Response::sendAlert("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.internalError"));

            header("location: ../list/");
            return;
        }

        $this->forumModel->deleteCategory($category);

        Response::sendAlert("success", LangManager::translate("core.toaster.success"),
            LangManager::translate("forum.category.delete.success"));

        header("location: ../list/");
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
            ->addVariableList(["categories" => $this->forumModel->getCategories()])
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


    /***
     *
     *
     *  PUBLIC AREA
     *
     */


    #[Link("/", Link::GET, [], "/forum")]
    public function publicBaseView(): void
    {
        $view = new View("forum", "main");
        $view->addVariableList(["forum" => $this->forumModel]);
        $view->view();
    }

    #[Link("/f/:forumSlug", Link::GET, ['.*?'], "/forum")]
    public function publicForumView(string $forumSlug): void
    {
        $forum = $this->forumModel->getForumBySlug($forumSlug);
        $forumModel = $this->forumModel;

        $view = new View("forum", "forum");
        $view->addVariableList(["forum" => $forum, "forumModel" => $forumModel]);
        $view->view();
    }

    #[Link("/f/:forumSlug/add", Link::GET, ['.*?'], "/forum")]
    public function publicForumAddTopicView(string $forumSlug): void
    {
        $forum = $this->forumModel->getForumBySlug($forumSlug);

        $view = new View("forum", "addTopic");
        $view->addVariableList(["forum" => $forum]);
        $view->view();
    }

    #[Link("/f/:forumSlug/add", Link::POST, ['.*?'], "/forum")]
    public function publicForumAddTopicPost(string $forumSlug): void
    {
        [$name, $content] = Utils::filterInput('name', 'content');

        $forum = $this->forumModel->getForumBySlug($forumSlug);

        if (is_null($forum) || Utils::hasOneNullValue($name, $content)) {
            Response::sendAlert("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.internalError"));
            Utils::refreshPage();
            return;
        }

        $res = $this->forumModel->createTopic($name, $content, UsersModel::getLoggedUser(), $forum?->getId());

        if (is_null($res)) { //TODO Fix this error ?
            Response::sendAlert("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.internalError"));
            Utils::refreshPage();
            return;
        }

        Response::sendAlert("success", LangManager::translate("core.toaster.success"),
            LangManager::translate("forum.topic.add.success"));

        header("location: ../");
    }

    #[Link("/t/:topicSlug", Link::GET, ['.*?'], "/forum")]
    public function publicTopicView(string $topicSlug): void
    {
        $topic = $this->forumModel->getTopicBySlug($topicSlug);
        $forumModel = $this->forumModel;

        $view = new View("forum", "topic");
        $view->addVariableList(["topic" => $topic, "forumModel" => $forumModel]);
        $view->view();
    }

    #[Link("/t/:topicSlug/pinned", Link::GET, ['.*?'], "/forum")]
    public function publicTopicPinned(string $topicSlug): void
    {
        $topic = $this->forumModel->getTopicBySlug($topicSlug);
        if (is_null($topic)) {
            Response::sendAlert("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.internalError"));
            return;
        }

        if ($this->forumModel->pinTopic($topic)) {

            Response::sendAlert("success", LangManager::translate("core.toaster.success"),
                $topic->isPinned() ?
                    LangManager::translate("forum.topic.unpinned.success") :
                    LangManager::translate("forum.topic.pinned.success"));

            header("location: ../../f/{$topic->getForum()->getSlug()}");
        }
    }

    #[Link("/t/:topicSlug", Link::POST, ['.*?'], "/forum")]
    public function publicTopicPost(string $topicSlug): void
    {
        usersController::isAdminLogged(); //TODO Need to "Is User Logged" && Permissions

        $topic = $this->forumModel->getTopicBySlug($topicSlug);
        $forumModel = $this->forumModel;

        if (!$topic) {
            Response::sendAlert("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.internalError"));
            return;
        }

        $userEntity = $this->usersModel->getUserById(UsersModel::getLoggedUser());
        $userId = $userEntity?->getId();
        [$topicId, $content] = Utils::filterInput('topicId', 'topicResponse');

        $forumModel->createResponse($content, $userId, $topicId);
        Utils::refreshPage();
    }
}