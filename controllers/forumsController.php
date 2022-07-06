<?php


namespace CMW\Controller\Forums;

use CMW\Controller\coreController;
use CMW\Controller\Users\usersController;
use CMW\Model\Forums\forumsModel;
use CMW\Utils\Utils;

/**
 * Class: @ForumsController
 * @package Forums
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class forumsController extends coreController
{

    private forumsModel $forumsModel;

    public function __construct($theme_path = null)
    {
        parent::__construct($theme_path);
        $this->forumsModel = new forumsModel();
    }

    public function adminAddCategoryView(): void
    {
        view('forums', 'categories/addCategory.admin', [], 'admin');
    }

    public function adminListCategoryView(): void
    {
        view('forums', 'categories/listCategory.admin', ["forum" => $this->forumsModel], 'admin');
    }

    public function adminAddCategoryPost(): void
    {
        if (Utils::isValuesEmpty($_POST, "name", "description")) {
            echo -1;
            return;
        }

        $name = filter_input(INPUT_POST, "name");
        $description = filter_input(INPUT_POST, "description");

        $res = $this->forumsModel->createCategory($name, $description);

        echo is_null($res) ? -2 : $res->getId();
    }

    public function adminDeleteCategoryPost(int $id): void
    {

        $category = $this->forumsModel->getCategoryById($id);

        if (is_null($category)) {
            return;
        }

        $res = $this->forumsModel->deleteCategory($category);

        echo $res ? 1 : -1;

        header("location: ../list/");
        die();
    }

    public function adminListForumView(): void
    {
        view('forums', 'forums/listForum.admin', ["forum" => $this->forumsModel], 'admin');
    }

    public function publicBaseView(): void
    {
        $forum = $this->forumsModel;

        view('forums', 'index', ["forum" => $forum], 'public');
    }

    public function publicForumView($forumSlug): void
    {
        $forum = $this->forumsModel->getForumBySlug($forumSlug);
        $forumModel = $this->forumsModel;

        view('forums', 'forum', ["forum" => $forum, "forumModel" => $forumModel], 'public');
    }

    public function publicTopicView($topicSlug): void
    {
        $topic = $this->forumsModel->getTopicBySlug($topicSlug);
        $forumModel = $this->forumsModel;
        view('forums', 'topic', ["topic" => $topic, "forumModel" => $forumModel], 'public');
    }

    public function publicTopicPost($topicSlug): void
    {
        usersController::isAdminLogged(); //TODO Need to "Is User Logged" && Permissions

        $topic = $this->forumsModel->getTopicBySlug($topicSlug);
        $forumModel = $this->forumsModel;

        if(!$topic) {
            return;
        }

        if(Utils::isValuesEmpty($_POST, "topicResponse")) {
            return;
        }

        $content = filter_input(INPUT_POST, "topicResponse");

        $forumModel->createResponse($content, $_SESSION["cmwUserId"], $topic->getId());
        header("refresh: 0");
    }
}