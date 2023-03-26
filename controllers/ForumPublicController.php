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
 * Class: @ForumPublicController
 * @package Forum
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ForumPublicController extends CoreController
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


    #[Link("/", Link::GET, [], "/forum")]
    public function publicBaseView(): void
    {
        $view = new View("forum", "main");
        $view->addVariableList(["forumModel" => $this->forumModel, "categoryModel" => $this->categoryModel]);
        $view->view();
    }

    #[Link("/f/:forumSlug", Link::GET, ['.*?'], "/forum")]
    public function publicForumView(string $forumSlug): void
    {
        $forum = $this->forumModel->getForumBySlug($forumSlug);

        $view = new View("forum", "forum");
        $view->addVariableList(["forum" => $forum, "topicModel" => $this->topicModel, "forumModel" => $this->forumModel]);
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

        $res = $this->topicModel->createTopic($name, $content, UsersModel::getLoggedUser(), $forum?->getId());

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
        $topic = $this->topicModel->getTopicBySlug($topicSlug);

        $view = new View("forum", "topic");
        $view->addVariableList(["topic" => $topic, "responseModel" => $this->responseModel]);
        $view->view();
    }

    #[Link("/t/:topicSlug/pinned", Link::GET, ['.*?'], "/forum")]
    public function publicTopicPinned(string $topicSlug): void
    {
        $topic = $this->topicModel->getTopicBySlug($topicSlug);
        if (is_null($topic)) {
            Response::sendAlert("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.internalError"));
            return;
        }

        if ($this->topicModel->pinTopic($topic)) {

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

        $topic = $this->topicModel->getTopicBySlug($topicSlug);
        $responseModel = $this->responseModel;

        if (!$topic) {
            Response::sendAlert("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.internalError"));
            return;
        }

        $userEntity = $this->usersModel->getUserById(UsersModel::getLoggedUser());
        $userId = $userEntity?->getId();
        [$topicId, $content] = Utils::filterInput('topicId', 'topicResponse');

        if (Utils::hasOneNullValue($topicId, $content)) {
            Response::sendAlert("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("forum.category.toaster.error.empty_input"));
            Utils::refreshPage();
            return;
        }

        $responseEntity = $responseModel->createResponse($content, $userId, $topicId);

        if (is_null($responseEntity)) {
            Response::sendAlert("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.internalError"));
            Utils::refreshPage();
            return;
        }

        Response::sendAlert("success", LangManager::translate("core.toaster.success"),
            LangManager::translate("forum.topic.replies.success"));
        Utils::refreshPage();
    }


}