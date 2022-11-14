<?php


namespace CMW\Controller\Forum;

use CMW\Controller\Core\CoreController;
use CMW\Controller\Users\UsersController;
use CMW\Model\Forum\ForumModel;
use CMW\Model\Users\UsersModel;
use CMW\Router\Link;
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

    public function __construct($theme_path = null)
    {
        parent::__construct($theme_path);
        $this->forumModel = new ForumModel();
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
            echo -1;
            return;
        }

        [$name, $description] = Utils::filterInput("name", "description");

        $res = $this->forumModel->createCategory($name, $description);

        echo is_null($res) ? -2 : $res->getId();
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

    #[Link("/list/:id", Link::GET, ['[0-9]+'], "/cmw-admin/forum/categories")]
    public function adminDeleteCategoryPost(int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.categories.delete");

        $category = $this->forumModel->getCategoryById($id);

        if (is_null($category)) {
            return;
        }

        $res = $this->forumModel->deleteCategory($category);

        echo $res ? 1 : -1;

        header("location: ../list/");
        die();
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


    /***
     *
     *
     *  PUBLIC AREA
     *
     */


    #[Link("/", Link::POST, [], "/forum")]
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

    #[Link("/t/:topicSlug", Link::GET, ['.*?'], "/forum")]
    public function publicTopicView(string $topicSlug): void
    {
        $topic = $this->forumModel->getTopicBySlug($topicSlug);
        $forumModel = $this->forumModel;

        $view = new View("forum", "topic");
        $view->addVariableList(["topic" => $topic, "forumModel" => $forumModel]);
        $view->view();
    }

    #[Link("/t/:topicSlug", Link::POST, ['.*?'], "/forum")]
    public function publicTopicPost(string $topicSlug): void
    {
        usersController::isAdminLogged(); //TODO Need to "Is User Logged" && Permissions

        $topic = $this->forumModel->getTopicBySlug($topicSlug);
        $forumModel = $this->forumModel;

        if (!$topic) {
            return;
        }

        if (Utils::isValuesEmpty($_POST, "topicResponse")) {
            return;
        }

        $content = Utils::filterInput("topicResponse");

        $forumModel->createResponse($content, UsersModel::getCurrentUser(), $topic->getId());
        header("refresh: 0");
    }
}