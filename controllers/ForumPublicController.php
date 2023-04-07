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
use CMW\Manager\Views\View;


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
        $view->addStyle("admin/resources/vendors/fontawesome-free/css/fa-all.min.css");
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
        $view->addStyle("admin/resources/vendors/fontawesome-free/css/fa-all.min.css","admin/resources/vendors/summernote/summernote-lite.css","admin/resources/assets/css/pages/summernote.css");
        $view->addScriptAfter("admin/resources/vendors/jquery/jquery.min.js","admin/resources/vendors/summernote/summernote-lite.min.js","admin/resources/assets/js/pages/summernote.js");
        $view->view();
    }

    #[Link("/f/:forumSlug/add", Link::POST, ['.*?'], "/forum")]
    public function publicForumAddTopicPost(string $forumSlug): void
    {
        [$name, $content, $disallowReplies, $important, $tags] = Utils::filterInput('name', 'content', 'disallow_replies', 'important', 'tags');

        $forum = $this->forumModel->getForumBySlug($forumSlug);

        if (is_null($forum) || Utils::hasOneNullValue($name, $content)) {
            Response::sendAlert("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.internalError"));
            Utils::refreshPage();
            return;
        }

        $res = $this->topicModel->createTopic($name, $content, UsersModel::getLoggedUser(), $forum->getId(),
            (is_null($disallowReplies) ? 0 : 1), (is_null($important) ? 0 : 1));

        if (is_null($res)) {
            Response::sendAlert("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.internalError"));
            Utils::refreshPage();
            return;
        }

        // Add tags

        $tags = explode(",", $tags);

        foreach ($tags as $tag) {
            //Clean tag
            $tag = mb_strtolower(trim($tag));

            if (empty($tag)) {
                continue;
            }

            $this->topicModel->addTag($tag, $res->getId());
        }

        Response::sendAlert("success", LangManager::translate("core.toaster.success"),
            LangManager::translate("forum.topic.add.success"));

        header("location: ../$forumSlug");
    }

    #[Link("/t/:topicSlug", Link::GET, ['.*?'], "/forum")]
    public function publicTopicView(string $topicSlug): void
    {
        $topic = $this->topicModel->getTopicBySlug($topicSlug);

        $view = new View("forum", "topic");
        $view->addVariableList(["topic" => $topic, "responseModel" => $this->responseModel]);
        $view->addStyle("admin/resources/vendors/fontawesome-free/css/fa-all.min.css","admin/resources/vendors/summernote/summernote-lite.css","admin/resources/assets/css/pages/summernote.css");
        $view->addScriptAfter("admin/resources/vendors/jquery/jquery.min.js","admin/resources/vendors/summernote/summernote-lite.min.js","admin/resources/assets/js/pages/summernote.js");
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

    #[Link("/t/:topicSlug/disallowreplies", Link::GET, ['.*?'], "/forum")]
    public function publicTopicDisallowReplies(string $topicSlug): void
    {
        $topic = $this->topicModel->getTopicBySlug($topicSlug);
        if (is_null($topic)) {
            Response::sendAlert("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.internalError"));
            return;
        }

        if ($this->topicModel->DisallowReplies($topic)) {

            Response::sendAlert("success", LangManager::translate("core.toaster.success"),
                $topic->isPinned() ?
                    LangManager::translate("forum.topic.unpinned.success") :
                    LangManager::translate("forum.topic.pinned.success"));

            header("location: ../../f/{$topic->getForum()->getSlug()}");
        }
    }

    #[Link("/t/:topicSlug", Link::POST, ['.*?'], "/forum")]
    public function publicTopicResponsePost(string $topicSlug): void
    {
        usersController::isAdminLogged(); //TODO Need to "Is User Logged" && Permissions

        $topic = $this->topicModel->getTopicBySlug($topicSlug);
        $responseModel = $this->responseModel;

        if (!$topic) {
            Response::sendAlert("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.internalError"));
            Utils::refreshPage();
            return;
        }

        if ($topic->isDisallowReplies()) {
            Response::sendAlert("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("forum.topic.replies.error.disallow_replies"));
            Utils::refreshPage();
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

    #[Link("/t/:topicSlug/delete/:replyId", Link::GET, ['.*?' => 'topicSlug', '[0-9]+' => 'replyId'], "/forum")]
    public function publicTopicReplyDelete(string $topicSlug, int $replyId): void
    {
        $topic = $this->topicModel->getTopicBySlug($topicSlug);
        if (is_null($topic)) {
            Response::sendAlert("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.internalError"));
            return;
        }

        $reply = $this->responseModel->getResponseById($replyId);

        if (!$reply?->isSelfReply()) {
            Response::sendAlert("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("forum.reply.delete.errors.no_access"));
            return;
        }

        if ($this->responseModel->deleteResponse($replyId)) {

            Response::sendAlert("success", LangManager::translate("core.toaster.success"),
                LangManager::translate("forum.reply.delete.success"));

            header("location: ../../{$topic->getSlug()}");
        }
    }


}