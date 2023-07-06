<?php


namespace CMW\Controller\Forum;

use CMW\Controller\Core\CoreController;
use CMW\Controller\Users\UsersController;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Requests\Request;
use CMW\Model\Forum\CategoryModel;
use CMW\Model\Forum\DiscordModel;
use CMW\Model\Forum\FeedbackModel;
use CMW\Model\Forum\ForumModel;
use CMW\Model\Forum\ResponseModel;
use CMW\Model\Forum\SettingsModel;
use CMW\Model\Forum\TopicModel;
use CMW\Model\users\UsersModel;
use CMW\Manager\Router\Link;
use CMW\Manager\Flash\Flash;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use CMW\Manager\Views\View;
use CMW\Utils\Website;

$discordModel = DiscordModel::getInstance();

/**
 * Class: @ForumPublicController
 * @package Forum
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ForumPublicController extends CoreController
{
    #[Link("/", Link::GET, [], "/forum")]
    public function publicBaseView(): void
    {
        $view = new View("Forum", "main");
        $view->addVariableList(["forumModel" => forumModel::getInstance(), "categoryModel" => categoryModel::getInstance()]);
        $view->addStyle("Admin/Resources/Vendors/Fontawesome-free/Css/fa-all.min.css");
        $view->view();
    }

    #[Link("/f/:forumSlug", Link::GET, ['.*?'], "/forum")]
    public function publicForumView(Request $request, string $forumSlug): void
    {
        $forum = forumModel::getInstance()->getForumBySlug($forumSlug);
        $forumModel = forumModel::getInstance();
        $categoryModel = categoryModel::getInstance();
        $iconNotRead = SettingsModel::getInstance()->getOptionValue("IconNotRead");
        $iconImportant = SettingsModel::getInstance()->getOptionValue("IconImportant");
        $iconPin = SettingsModel::getInstance()->getOptionValue("IconPin");
        $iconClosed = SettingsModel::getInstance()->getOptionValue("IconClosed");

        $view = new View("Forum", "forum");
        $view->addVariableList(["forumModel" => $forumModel, "categoryModel" => $categoryModel, "forum" => $forum, "topicModel" => topicModel::getInstance(), "forumModel" => forumModel::getInstance(), "responseModel" => responseModel::getInstance(),"iconNotRead" => $iconNotRead, "iconImportant" => $iconImportant, "iconPin" => $iconPin, "iconClosed" => $iconClosed]);
        $view->view();
    }

    #[Link("/f/:forumSlug/add", Link::GET, ['.*?'], "/forum")]
    public function publicForumAddTopicView(Request $request, string $forumSlug): void
    {
        $forum = forumModel::getInstance()->getForumBySlug($forumSlug);

        $iconNotRead = SettingsModel::getInstance()->getOptionValue("IconNotRead");
        $iconImportant = SettingsModel::getInstance()->getOptionValue("IconImportant");
        $iconPin = SettingsModel::getInstance()->getOptionValue("IconPin");
        $iconClosed = SettingsModel::getInstance()->getOptionValue("IconClosed");

        $view = new View("Forum", "addTopic");
        $view->addVariableList(["forum" => $forum,"iconNotRead" => $iconNotRead, "iconImportant" => $iconImportant, "iconPin" => $iconPin, "iconClosed" => $iconClosed]);
        $view->addScriptBefore("Admin/Resources/Vendors/Tinymce/tinymce.min.js","Admin/Resources/Vendors/Tinymce/Config/full.js");
        $view->addStyle("Admin/Resources/Vendors/Fontawesome-free/Css/fa-all.min.css");
        $view->view();
    }

    #[Link("/f/:forumSlug/add", Link::POST, ['.*?'], "/forum")]
    public function publicForumAddTopicPost(Request $request, string $forumSlug): void
    {
        [$name, $content, $disallowReplies, $important, $pin, $tags] = Utils::filterInput('name', 'content', 'disallow_replies', 'important', 'pin', 'tags');

        $forum = forumModel::getInstance()->getForumBySlug($forumSlug);

        if (is_null($forum) || Utils::containsNullValue($name, $content)) {
            Flash::send("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.internalError"));
            Website::refresh();
            return;
        }

        $res = topicModel::getInstance()->createTopic($name, $content, UsersModel::getCurrentUser()?->getId(), $forum->getId(),
            (is_null($disallowReplies) ? 0 : 1), (is_null($important) ? 0 : 1), (is_null($pin) ? 0 : 1));

        if (is_null($res)) {
            Flash::send("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.internalError"));
            Website::refresh();
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

            topicModel::getInstance()->addTag($tag, $res->getId());
        }

        Flash::send("success", LangManager::translate("core.toaster.success"),
            LangManager::translate("forum.topic.add.success"));

        DiscordModel::getInstance()->sendDiscordMsgNewTopic($forum->getId(),$name,$forum->getName(),"test",UsersModel::getCurrentUser()->getUserPicture()->getImageName(),UsersModel::getCurrentUser()->getPseudo());

        header("location: ../$forumSlug");
    }

    #[Link("/f/:forumSlug/adminedit", Link::POST, ['.*?'], "/forum")]
    public function publicForumAdminEditTopicPost(Request $request, string $forumSlug): void
    {
        [$topicId, $name, $disallowReplies, $important, $pin, $tags, $prefix, $move] = Utils::filterInput('topicId', 'name', 'disallow_replies', 'important', 'pin', 'tags', 'prefix', 'move');

        $forum = forumModel::getInstance()->getForumBySlug($forumSlug);

        if (is_null($forum)) {
            Flash::send("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.internalError"));
            Website::refresh();
            return;
        }

        topicModel::getInstance()->adminEditTopic($topicId, $name, (is_null($disallowReplies) ? 0 : 1), (is_null($important) ? 0 : 1), (is_null($pin) ? 0 : 1), $prefix, $move);

        // Add tags


        $tags = explode(",", $tags);
        //Need to clear tag befor update
        topicModel::getInstance()->clearTag($topicId);
        foreach ($tags as $tag) {
            //Clean tag
            $tag = mb_strtolower(trim($tag));

            if (empty($tag)) {
                continue;
            }
            
            topicModel::getInstance()->addTag($tag, $topicId);
        }

        //Flash::send("success", LangManager::translate("core.toaster.success"),LangManager::translate("forum.topic.add.success"));

        header("location: ../$forumSlug");
    }

    #[Link("/t/:topicSlug", Link::GET, ['.*?'], "/forum")]
    public function publicTopicView(Request $request, string $topicSlug): void
    {
        $topic = topicModel::getInstance()->getTopicBySlug($topicSlug);
        $isViewed = topicModel::getInstance()->checkViews($topic->getId(),Website::getClientIp());
        $currentUser = usersModel::getInstance()::getCurrentUser();

        $iconNotRead = SettingsModel::getInstance()->getOptionValue("IconNotRead");
        $iconImportant = SettingsModel::getInstance()->getOptionValue("IconImportant");
        $iconPin = SettingsModel::getInstance()->getOptionValue("IconPin");
        $iconClosed = SettingsModel::getInstance()->getOptionValue("IconClosed");
        $feedbackModel = feedbackModel::getInstance();

        if (!$isViewed) {
            topicModel::getInstance()->addViews($topic->getId());
        }

        $view = new View("Forum", "topic");
        $view->addVariableList(["currentUser" => $currentUser, "topic" => $topic, "feedbackModel" => $feedbackModel, "responseModel" => responseModel::getInstance(),"iconNotRead" => $iconNotRead, "iconImportant" => $iconImportant, "iconPin" => $iconPin, "iconClosed" => $iconClosed]);
        $view->addScriptBefore("Admin/Resources/Vendors/Tinymce/tinymce.min.js","Admin/Resources/Vendors/Tinymce/Config/full.js");
        $view->addStyle("Admin/Resources/Vendors/Fontawesome-free/Css/fa-all.min.css");
        $view->view();
    }

    #[Link("/t/:topicSlug/react/:topicId/:feedbackId", Link::GET, ['.*?'], "/forum")]
    public function publicTopicAddFeedback(Request $request, string $topicSlug, int $topicId, int $feedbackId): void
    {
        $user = usersModel::getInstance()::getCurrentUser();
        feedbackModel::getInstance()->addFeedbackByFeedbackId($topicId, $feedbackId, $user?->getId());

        Redirect::redirectPreviousRoute();
    }

    #[Link("/t/:topicSlug/un_react/:topicId/:feedbackId", Link::GET, ['.*?'], "/forum")]
    public function publicTopicDeleteFeedback(Request $request, string $topicSlug, int $topicId, int $feedbackId): void
    {
        $user = usersModel::getInstance()::getCurrentUser();
        feedbackModel::getInstance()->removeFeedbackByFeedbackId($topicId, $feedbackId, $user?->getId());

        Redirect::redirectPreviousRoute();
    }

    #[Link("/t/:topicSlug/change_react/:topicId/:feedbackId", Link::GET, ['.*?'], "/forum")]
    public function publicTopicChangeFeedback(Request $request, string $topicSlug, int $topicId, int $feedbackId): void
    {
        $user = usersModel::getInstance()::getCurrentUser();
        feedbackModel::getInstance()->changeFeedbackByFeedbackId($topicId, $feedbackId, $user?->getId());

        Redirect::redirectPreviousRoute();
    }





    #[Link("/t/:topicSlug/response_react/:responseId/:feedbackId", Link::GET, ['.*?'], "/forum")]
    public function publicResponseAddFeedback(Request $request, string $topicSlug, int $responseId, int $feedbackId): void
    {
        $user = usersModel::getInstance()::getCurrentUser();
        feedbackModel::getInstance()->addFeedbackResponseByFeedbackId($responseId, $feedbackId, $user?->getId());

        Redirect::redirectPreviousRoute();
    }

    #[Link("/t/:topicSlug/response_un_react/:responseId/:feedbackId", Link::GET, ['.*?'], "/forum")]
    public function publicResponseDeleteFeedback(Request $request, string $topicSlug, int $responseId, int $feedbackId): void
    {
        $user = usersModel::getInstance()::getCurrentUser();
        feedbackModel::getInstance()->removeFeedbackResponseByFeedbackId($responseId, $feedbackId, $user?->getId());

        Redirect::redirectPreviousRoute();
    }

    #[Link("/t/:topicSlug/response_change_react/:responseId/:feedbackId", Link::GET, ['.*?'], "/forum")]
    public function publicResponseChangeFeedback(Request $request, string $topicSlug, int $responseId, int $feedbackId): void
    {
        $user = usersModel::getInstance()::getCurrentUser();
        feedbackModel::getInstance()->changeFeedbackResponseByFeedbackId($responseId, $feedbackId, $user?->getId());

        Redirect::redirectPreviousRoute();
    }

    #[Link("/t/:topicSlug/pinned", Link::GET, ['.*?'], "/forum")]
    public function publicTopicPinned(Request $request, string $topicSlug): void
    {
        $topic = topicModel::getInstance()->getTopicBySlug($topicSlug);
        if (is_null($topic)) {
            Flash::send("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.internalError"));
            return;
        }

        if (topicModel::getInstance()->pinTopic($topic)) {

            Flash::send("success", LangManager::translate("core.toaster.success"),
                $topic->isPinned() ?
                    LangManager::translate("forum.topic.unpinned.success") :
                    LangManager::translate("forum.topic.pinned.success"));

            header("location: ../../f/{$topic->getForum()->getSlug()}");
        }
    }

    #[Link("/t/:topicSlug/disallowreplies", Link::GET, ['.*?'], "/forum")]
    public function publicTopicDisallowReplies(Request $request, string $topicSlug): void
    {
        $topic = topicModel::getInstance()->getTopicBySlug($topicSlug);
        if (is_null($topic)) {
            Flash::send("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.internalError"));
            return;
        }

        if (topicModel::getInstance()->DisallowReplies($topic)) {

            Flash::send("success", LangManager::translate("core.toaster.success"),
                $topic->isPinned() ?
                    LangManager::translate("forum.topic.unpinned.success") :
                    LangManager::translate("forum.topic.pinned.success"));

            header("location: ../../f/{$topic->getForum()->getSlug()}");
        }
    }

    #[Link("/t/:topicSlug/isimportant", Link::GET, ['.*?'], "/forum")]
    public function publicTopicIsImportant(Request $request, string $topicSlug): void
    {
        $topic = topicModel::getInstance()->getTopicBySlug($topicSlug);
        if (is_null($topic)) {
            Flash::send("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.internalError"));
            return;
        }

        if (topicModel::getInstance()->ImportantTopic($topic)) {

            Flash::send("success", LangManager::translate("core.toaster.success"),
                $topic->isPinned() ?
                    LangManager::translate("forum.topic.unpinned.success") :
                    LangManager::translate("forum.topic.pinned.success"));

            header("location: ../../f/{$topic->getForum()->getSlug()}");
        }
    }

    #[Link("/t/:topicSlug/trash", Link::GET, ['.*?'], "/forum")]
    public function publicTopicIsTrash(Request $request, string $topicSlug): void
    {
        $topic = topicModel::getInstance()->getTopicBySlug($topicSlug);
        if (is_null($topic)) {
            Flash::send("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.internalError"));
            return;
        }

        if (topicModel::getInstance()->trashTopic($topic)) {

            Flash::send("success", LangManager::translate("core.toaster.success"),"Topic mis à la poubelle !");

            header("location: ../../f/{$topic->getForum()->getSlug()}");
        }
    }

    #[Link("/t/:topicSlug", Link::POST, ['.*?'], "/forum")]
    public function publicTopicResponsePost(Request $request, string $topicSlug): void
    {
        usersController::isAdminLogged(); //TODO Need to "Is User Logged" && Permissions

        $topic = topicModel::getInstance()->getTopicBySlug($topicSlug);
        $responseModel = responseModel::getInstance();

        if (!$topic) {
            Flash::send("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.internalError"));
            Website::refresh();
            return;
        }

        if ($topic->isDisallowReplies()) {
            Flash::send("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("forum.topic.replies.error.disallow_replies"));
            Website::refresh();
            return;
        }

        $userEntity = usersModel::getInstance()->getUserById(UsersModel::getCurrentUser()?->getId());
        $userId = $userEntity?->getId();
        [$topicId, $content] = Utils::filterInput('topicId', 'topicResponse');

        if (Utils::containsNullValue($topicId, $content)) {
            Flash::send("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("forum.category.toaster.error.empty_input"));
            Website::refresh();
            return;
        }

        $responseEntity = $responseModel::getInstance()->createResponse($content, $userId, $topicId);

        if (is_null($responseEntity)) {
            Flash::send("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.internalError"));
            Website::refresh();
            return;
        }

        Flash::send("success", LangManager::translate("core.toaster.success"),
            LangManager::translate("forum.topic.replies.success"));
        Website::refresh();
    }

    #[Link("/t/:topicSlug/trash/:replyId/:reason", Link::GET, ['.*?' => 'topicSlug', '[0-9]+' => 'replyId'], "/forum")]
    public function publicTopicReplyDelete(Request $request, string $topicSlug, int $replyId, int $reason): void
    {
        $topic = topicModel::getInstance()->getTopicBySlug($topicSlug);
        if (is_null($topic)) {
            Flash::send("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.internalError"));
            return;
        }

        $reply = responseModel::getInstance()->getResponseById($replyId);

        if (!$reply?->isSelfReply()) {//Rajouter ici si on as la permission de supprimer (staff)
            Flash::send("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("forum.reply.delete.errors.no_access"));
            return;
        }

        if (responseModel::getInstance()->trashResponse($replyId, $reason)) {

            Flash::send("success", LangManager::translate("core.toaster.success"),
                LangManager::translate("forum.reply.delete.success"));

            header("location: ../../../{$topic->getSlug()}");
        }
    }


    #[Link("/t/:topicSlug/edit", Link::GET, ['.*?'], "/forum")]
    public function publicTopicEdit(Request $request, string $topicSlug): void
    {
        $topic = topicModel::getInstance()->getTopicBySlug($topicSlug);

        $view = new View("Forum", "editTopic");
        $view->addVariableList(["topic" => $topic]);
        $view->addScriptBefore("Admin/Resources/Vendors/Tinymce/tinymce.min.js","Admin/Resources/Vendors/Tinymce/Config/full.js");
        $view->addStyle("Admin/Resources/Vendors/Fontawesome-free/Css/fa-all.min.css");
        $view->view();
    }

    #[Link("/t/:topicSlug/edit", Link::POST, ['.*?'], "/forum")]
    public function publicTopicEditPost(Request $request, string $topicSlug): void
    {
        [$topicId, $name, $content, $tags] = Utils::filterInput('topicId', 'name', 'content', 'tags');

        $topic = topicModel::getInstance()->getTopicBySlug($topicSlug);

        if (is_null($topic) || Utils::containsNullValue($name, $content)) {
            Flash::send("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.internalError"));
            Website::refresh();
            return;
        }

        $res = topicModel::getInstance()->authorEditTopic($topicId, $name, $content);

        // Add tags

        $tags = explode(",", $tags);
        //Need to clear tag befor update
        topicModel::getInstance()->clearTag($topicId);
        foreach ($tags as $tag) {
            //Clean tag
            $tag = mb_strtolower(trim($tag));

            if (empty($tag)) {
                continue;
            }
            
            topicModel::getInstance()->addTag($tag, $topicId);
        }

        Flash::send("success", LangManager::translate("core.toaster.success"),
            LangManager::translate("forum.topic.add.success"));

        header("location: ../../f/{$topic->getForum()->getSlug()}");
    }


}