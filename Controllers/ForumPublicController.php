<?php


namespace CMW\Controller\Forum;

use CMW\Controller\Core\CoreController;
use CMW\Controller\Users\UsersController;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Requests\Request;
use CMW\Model\Forum\ForumCategoryModel;
use CMW\Model\Forum\ForumDiscordModel;
use CMW\Model\Forum\ForumFeedbackModel;
use CMW\Model\Forum\ForumModel;
use CMW\Model\Forum\ForumPermissionModel;
use CMW\Model\Forum\ForumPermissionRoleModel;
use CMW\Model\Forum\ForumResponseModel;
use CMW\Model\Forum\ForumSettingsModel;
use CMW\Model\Forum\ForumTopicModel;
use CMW\Model\users\UsersModel;
use CMW\Manager\Router\Link;
use CMW\Manager\Flash\Flash;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use CMW\Manager\Views\View;
use CMW\Utils\Website;

$discordModel = ForumDiscordModel::getInstance();

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
        $visitorCanViewForum = ForumSettingsModel::getInstance()->getOptionValue("visitorCanViewForum");

        if ($visitorCanViewForum === "0") {
            ForumController::getInstance()->redirectIfNotHavePermissions("user_view_forum");
        }

        $view = new View("Forum", "main");
        $view->addVariableList(["forumModel" => forumModel::getInstance(), "categoryModel" => ForumCategoryModel::getInstance()]);
        $view->addStyle("Admin/Resources/Vendors/Fontawesome-free/Css/fa-all.min.css");
        $view->view();
    }

    #[Link("/c/:catSlug", Link::GET, ['.*?'], "/forum")]
    public function publicCatView(Request $request, string $catSlug): void
    {
        $visitorCanViewForum = ForumSettingsModel::getInstance()->getOptionValue("visitorCanViewForum");

        if ($visitorCanViewForum === "0") {
            ForumController::getInstance()->redirectIfNotHavePermissions("user_view_forum");
        }

        $category = ForumCategoryModel::getInstance()->getCatBySlug($catSlug);


        $view = new View("Forum", "cat");
        $view->addVariableList(["forumModel" => forumModel::getInstance(), "category" => $category]);
        $view->addStyle("Admin/Resources/Vendors/Fontawesome-free/Css/fa-all.min.css");
        $view->view();
    }

    #[Link("/c/:catSlug/f/:forumSlug", Link::GET, ['.*?'], "/forum")]
    public function publicForumView(Request $request, string $catSlug, string $forumSlug): void
    {
        $visitorCanViewForum = ForumSettingsModel::getInstance()->getOptionValue("visitorCanViewForum");

        if ($visitorCanViewForum === "0") {
            ForumController::getInstance()->redirectIfNotHavePermissions("user_view_forum");
        }

        $category = ForumCategoryModel::getInstance()->getCatBySlug($catSlug);
        $forum = forumModel::getInstance()->getForumBySlug($forumSlug);
        $forumModel = forumModel::getInstance();
        $categoryModel = ForumCategoryModel::getInstance();
        $iconNotRead = ForumSettingsModel::getInstance()->getOptionValue("IconNotRead");
        $iconImportant = ForumSettingsModel::getInstance()->getOptionValue("IconImportant");
        $iconPin = ForumSettingsModel::getInstance()->getOptionValue("IconPin");
        $iconClosed = ForumSettingsModel::getInstance()->getOptionValue("IconClosed");

        $view = new View("Forum", "forum");
        $view->addVariableList(["forumModel" => $forumModel, "categoryModel" => $categoryModel, "forum" => $forum, "topicModel" => ForumTopicModel::getInstance(), "responseModel" => ForumResponseModel::getInstance(), "iconNotRead" => $iconNotRead, "iconImportant" => $iconImportant, "iconPin" => $iconPin, "iconClosed" => $iconClosed, "category" => $category]);
        $view->view();
    }

    #[Link("/c/:catSlug/f/:forumSlug/add", Link::GET, ['.*?'], "/forum")]
    public function publicForumAddTopicView(Request $request, string $catSlug, string $forumSlug): void
    {
        ForumController::getInstance()->redirectIfNotHavePermissions("user_create_topic");

        $category = ForumCategoryModel::getInstance()->getCatBySlug($catSlug);
        $forum = forumModel::getInstance()->getForumBySlug($forumSlug);

        $iconNotRead = ForumSettingsModel::getInstance()->getOptionValue("IconNotRead");
        $iconImportant = ForumSettingsModel::getInstance()->getOptionValue("IconImportant");
        $iconPin = ForumSettingsModel::getInstance()->getOptionValue("IconPin");
        $iconClosed = ForumSettingsModel::getInstance()->getOptionValue("IconClosed");

        $view = new View("Forum", "addTopic");
        $view->addVariableList(["forum" => $forum, "iconNotRead" => $iconNotRead, "iconImportant" => $iconImportant, "iconPin" => $iconPin, "iconClosed" => $iconClosed, "category" => $category]);
        $view->addScriptBefore("Admin/Resources/Vendors/Tinymce/tinymce.min.js", "Admin/Resources/Vendors/Tinymce/Config/full.js");
        $view->addStyle("Admin/Resources/Vendors/Fontawesome-free/Css/fa-all.min.css");
        $view->view();
    }

    #[Link("/c/:catSlug/f/:forumSlug/add", Link::POST, ['.*?'], "/forum")]
    public function publicForumAddTopicPost(Request $request, string $catSlug, string $forumSlug): void
    {
        ForumController::getInstance()->redirectIfNotHavePermissions("user_create_topic");

        $userId = UsersModel::getCurrentUser()->getId();

        [$name, $content, $disallowReplies, $important, $pin, $tags] = Utils::filterInput('name', 'content', 'disallow_replies', 'important', 'pin', 'tags');

        if (!ForumPermissionModel::getInstance()->hasForumPermission($userId, "user_create_topic_tag") && $tags !== "") {
            ForumController::getInstance()->alertNotHavePermissions("user_create_topic_tag");
            $tags = "";
        }

        $forum = forumModel::getInstance()->getForumBySlug($forumSlug);

        if (is_null($forum) || Utils::containsNullValue($name, $content)) {
            Flash::send("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.internalError"));
            Website::refresh();
            return;
        }

        $res = ForumTopicModel::getInstance()->createTopic($name, $content, $userId, $forum->getId(),
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

            ForumTopicModel::getInstance()->addTag($tag, $res->getId());
        }

        Flash::send("success", LangManager::translate("core.toaster.success"),
            LangManager::translate("forum.topic.add.success"));

        ForumDiscordModel::getInstance()->sendDiscordMsgNewTopic($forum->getId(), $name, $forum->getName(), "test", UsersModel::getCurrentUser()->getUserPicture()->getImageName(), UsersModel::getCurrentUser()->getPseudo());

        header("location: ../$forumSlug");
    }

    #[Link("/c/:catSlug/f/:forumSlug/adminedit", Link::POST, ['.*?'], "/forum")]
    public function publicForumAdminEditTopicPost(Request $request, string $catSlug, string $forumSlug): void
    {

        if (UsersController::isAdminLogged()) {

            [$topicId, $name, $disallowReplies, $important, $pin, $tags, $prefix, $move] = Utils::filterInput('topicId', 'name', 'disallow_replies', 'important', 'pin', 'tags', 'prefix', 'move');

            $forum = forumModel::getInstance()->getForumBySlug($forumSlug);

            if (is_null($forum)) {
                Flash::send("error", LangManager::translate("core.toaster.error"),
                    LangManager::translate("core.toaster.internalError"));
                Website::refresh();
                return;
            }

            ForumTopicModel::getInstance()->adminEditTopic($topicId, $name, (is_null($disallowReplies) ? 0 : 1), (is_null($important) ? 0 : 1), (is_null($pin) ? 0 : 1), $prefix, $move);

            // Add tags


            $tags = explode(",", $tags);
            //Need to clear tag befor update
            ForumTopicModel::getInstance()->clearTag($topicId);
            foreach ($tags as $tag) {
                //Clean tag
                $tag = mb_strtolower(trim($tag));

                if (empty($tag)) {
                    continue;
                }

                ForumTopicModel::getInstance()->addTag($tag, $topicId);
            }

            //Flash::send("success", LangManager::translate("core.toaster.success"),LangManager::translate("forum.topic.add.success"));

            header("location: ../$forumSlug");
        } else {
            Flash::send(Alert::ERROR, "Erreur", "Vous n'êtes pas autoriser à faire ceci !");
            Redirect::redirect("forum");
        }


    }

    #[Link("/c/:catSlug/f/:forumSlug/t/:topicSlug", Link::GET, ['.*?'], "/forum")]
    public function publicTopicView(Request $request, string $catSlug, string $forumSlug, string $topicSlug): void
    {
        $visitorCanViewForum = ForumSettingsModel::getInstance()->getOptionValue("visitorCanViewForum");

        if ($visitorCanViewForum === "0") {
            ForumController::getInstance()->redirectIfNotHavePermissions("user_view_topic");
        }

        $category = ForumCategoryModel::getInstance()->getCatBySlug($catSlug);
        $forum = forumModel::getInstance()->getForumBySlug($forumSlug);

        $topic = ForumTopicModel::getInstance()->getTopicBySlug($topicSlug);
        $isViewed = ForumTopicModel::getInstance()->checkViews($topic->getId(), Website::getClientIp());
        $currentUser = usersModel::getInstance()::getCurrentUser();

        $iconNotRead = ForumSettingsModel::getInstance()->getOptionValue("IconNotRead");
        $iconImportant = ForumSettingsModel::getInstance()->getOptionValue("IconImportant");
        $iconPin = ForumSettingsModel::getInstance()->getOptionValue("IconPin");
        $iconClosed = ForumSettingsModel::getInstance()->getOptionValue("IconClosed");
        $feedbackModel = ForumFeedbackModel::getInstance();

        if (!$isViewed) {
            ForumTopicModel::getInstance()->addViews($topic->getId());
        }

        $view = new View("Forum", "topic");
        $view->addVariableList(["currentUser" => $currentUser, "topic" => $topic, "feedbackModel" => $feedbackModel, "responseModel" => ForumResponseModel::getInstance(), "iconNotRead" => $iconNotRead, "iconImportant" => $iconImportant, "iconPin" => $iconPin, "iconClosed" => $iconClosed, "forum" => $forum, "category" => $category]);
        $view->addScriptBefore("Admin/Resources/Vendors/Tinymce/tinymce.min.js", "Admin/Resources/Vendors/Tinymce/Config/full.js");
        $view->addStyle("Admin/Resources/Vendors/Fontawesome-free/Css/fa-all.min.css");
        $view->view();
    }

    #[Link("/c/:catSlug/f/:forumSlug/t/:topicSlug/react/:topicId/:feedbackId", Link::GET, ['.*?'], "/forum")]
    public function publicTopicAddFeedback(Request $request, string $catSlug, string $forumSlug, string $topicSlug, int $topicId, int $feedbackId): void
    {
        if (!UsersController::isUserLogged()) {
            Flash::send(Alert::ERROR, "Forum", "Connectez-vous avant de réagire.");
            Redirect::redirect('login');
        }

        ForumController::getInstance()->redirectIfNotHavePermissions("user_react_topic");

        $user = usersModel::getInstance()::getCurrentUser();
        ForumFeedbackModel::getInstance()->addFeedbackByFeedbackId($topicId, $feedbackId, $user?->getId());

        Redirect::redirectPreviousRoute();
    }

    #[Link("/c/:catSlug/f/:forumSlug/t/:topicSlug/un_react/:topicId/:feedbackId", Link::GET, ['.*?'], "/forum")]
    public function publicTopicDeleteFeedback(Request $request, string $catSlug, string $forumSlug, string $topicSlug, int $topicId, int $feedbackId): void
    {
        if (!UsersController::isUserLogged()) {
            Flash::send(Alert::ERROR, "Forum", "Connectez-vous avant de réagire.");
            Redirect::redirect('login');
        }

        ForumController::getInstance()->redirectIfNotHavePermissions("user_remove_react_topic");

        $user = usersModel::getInstance()::getCurrentUser();
        ForumFeedbackModel::getInstance()->removeFeedbackByFeedbackId($topicId, $feedbackId, $user?->getId());

        Redirect::redirectPreviousRoute();
    }

    #[Link("/c/:catSlug/f/:forumSlug/t/:topicSlug/change_react/:topicId/:feedbackId", Link::GET, ['.*?'], "/forum")]
    public function publicTopicChangeFeedback(Request $request, string $catSlug, string $forumSlug, string $topicSlug, int $topicId, int $feedbackId): void
    {
        if (!UsersController::isUserLogged()) {
            Flash::send(Alert::ERROR, "Forum", "Connectez-vous avant de réagire.");
            Redirect::redirect('login');
        }

        ForumController::getInstance()->redirectIfNotHavePermissions("user_change_react_topic");

        $user = usersModel::getInstance()::getCurrentUser();
        ForumFeedbackModel::getInstance()->changeFeedbackByFeedbackId($topicId, $feedbackId, $user?->getId());

        Redirect::redirectPreviousRoute();
    }

    #[Link("/c/:catSlug/f/:forumSlug/t/:topicSlug/response_react/:responseId/:feedbackId", Link::GET, ['.*?'], "/forum")]
    public function publicResponseAddFeedback(Request $request, string $catSlug, string $forumSlug, string $topicSlug, int $responseId, int $feedbackId): void
    {
        if (!UsersController::isUserLogged()) {
            Flash::send(Alert::ERROR, "Forum", "Connectez-vous avant de réagire.");
            Redirect::redirect('login');
        }

        ForumController::getInstance()->redirectIfNotHavePermissions("user_response_react");

        $user = usersModel::getInstance()::getCurrentUser();
        ForumFeedbackModel::getInstance()->addFeedbackResponseByFeedbackId($responseId, $feedbackId, $user?->getId());

        Redirect::redirectPreviousRoute();
    }

    #[Link("/c/:catSlug/f/:forumSlug/t/:topicSlug/response_un_react/:responseId/:feedbackId", Link::GET, ['.*?'], "/forum")]
    public function publicResponseDeleteFeedback(Request $request, string $catSlug, string $forumSlug, string $topicSlug, int $responseId, int $feedbackId): void
    {
        if (!UsersController::isUserLogged()) {
            Flash::send(Alert::ERROR, "Forum", "Connectez-vous avant de réagire.");
            Redirect::redirect('login');
        }

        ForumController::getInstance()->redirectIfNotHavePermissions("user_response_remove_react");

        $user = usersModel::getInstance()::getCurrentUser();
        ForumFeedbackModel::getInstance()->removeFeedbackResponseByFeedbackId($responseId, $feedbackId, $user?->getId());

        Redirect::redirectPreviousRoute();
    }

    #[Link("/c/:catSlug/f/:forumSlug/t/:topicSlug/response_change_react/:responseId/:feedbackId", Link::GET, ['.*?'], "/forum")]
    public function publicResponseChangeFeedback(Request $request, string $catSlug, string $forumSlug, string $topicSlug, int $responseId, int $feedbackId): void
    {
        if (!UsersController::isUserLogged()) {
            Flash::send(Alert::ERROR, "Forum", "Connectez-vous avant de réagire.");
            Redirect::redirect('login');
        }

        ForumController::getInstance()->redirectIfNotHavePermissions("user_response_change_react");

        $user = usersModel::getInstance()::getCurrentUser();
        ForumFeedbackModel::getInstance()->changeFeedbackResponseByFeedbackId($responseId, $feedbackId, $user?->getId());

        Redirect::redirectPreviousRoute();
    }

    #[Link("/c/:catSlug/f/:forumSlug/t/:topicSlug/pinned", Link::GET, ['.*?'], "/forum")]
    public function publicTopicPinned(Request $request, string $catSlug, string $forumSlug, string $topicSlug): void
    {
        if (UsersController::isAdminLogged()) {
            $topic = ForumTopicModel::getInstance()->getTopicBySlug($topicSlug);
            if (is_null($topic)) {
                Flash::send("error", LangManager::translate("core.toaster.error"),
                    LangManager::translate("core.toaster.internalError"));
                return;
            }

            if (ForumTopicModel::getInstance()->pinTopic($topic)) {

                Flash::send("success", LangManager::translate("core.toaster.success"),
                    $topic->isPinned() ?
                        LangManager::translate("forum.topic.unpinned.success") :
                        LangManager::translate("forum.topic.pinned.success"));

                header("location: ../../f/{$topic->getForum()->getSlug()}");
            }
        } else {
            Flash::send(Alert::ERROR, "Erreur", "Vous n'avez pas la permission de faire ceci !");
            Redirect::redirect("forum");
        }

    }

    #[Link("/c/:catSlug/f/:forumSlug/t/:topicSlug/disallowreplies", Link::GET, ['.*?'], "/forum")]
    public function publicTopicDisallowReplies(Request $request, string $catSlug, string $forumSlug, string $topicSlug): void
    {
        if (UsersController::isAdminLogged()) {
            $topic = ForumTopicModel::getInstance()->getTopicBySlug($topicSlug);
            if (is_null($topic)) {
                Flash::send("error", LangManager::translate("core.toaster.error"),
                    LangManager::translate("core.toaster.internalError"));
                return;
            }

            if (ForumTopicModel::getInstance()->DisallowReplies($topic)) {

                Flash::send("success", LangManager::translate("core.toaster.success"),
                    $topic->isPinned() ?
                        LangManager::translate("forum.topic.unpinned.success") :
                        LangManager::translate("forum.topic.pinned.success"));

                header("location: ../../f/{$topic->getForum()->getSlug()}");
            }
        } else {
            Flash::send(Alert::ERROR, "Erreur", "Vous n'avez pas la permission de faire ceci !");
            Redirect::redirect("forum");
        }
    }

    #[Link("/c/:catSlug/f/:forumSlug/t/:topicSlug/isimportant", Link::GET, ['.*?'], "/forum")]
    public function publicTopicIsImportant(Request $request, string $catSlug, string $forumSlug, string $topicSlug): void
    {
        if (UsersController::isAdminLogged()) {
            $topic = ForumTopicModel::getInstance()->getTopicBySlug($topicSlug);
            if (is_null($topic)) {
                Flash::send("error", LangManager::translate("core.toaster.error"),
                    LangManager::translate("core.toaster.internalError"));
                return;
            }

            if (ForumTopicModel::getInstance()->ImportantTopic($topic)) {

                Flash::send("success", LangManager::translate("core.toaster.success"),
                    $topic->isPinned() ?
                        LangManager::translate("forum.topic.unpinned.success") :
                        LangManager::translate("forum.topic.pinned.success"));

                header("location: ../../f/{$topic->getForum()->getSlug()}");
            }
        } else {
            Flash::send(Alert::ERROR, "Erreur", "Vous n'avez pas la permission de faire ceci !");
            Redirect::redirect("forum");
        }
    }

    #[Link("/c/:catSlug/f/:forumSlug/t/:topicSlug/trash", Link::GET, ['.*?'], "/forum")]
    public function publicTopicIsTrash(Request $request, string $catSlug, string $forumSlug, string $topicSlug): void
    {
        if (UsersController::isAdminLogged()) {
            $topic = ForumTopicModel::getInstance()->getTopicBySlug($topicSlug);
            if (is_null($topic)) {
                Flash::send("error", LangManager::translate("core.toaster.error"),
                    LangManager::translate("core.toaster.internalError"));
                return;
            }

            if (ForumTopicModel::getInstance()->trashTopic($topic)) {

                Flash::send("success", LangManager::translate("core.toaster.success"), "Topic mis à la poubelle !");

                header("location: ../../");
            }
        } else {
            Flash::send(Alert::ERROR, "Erreur", "Vous n'avez pas la permission de faire ceci !");
            Redirect::redirect("forum");
        }
    }

    #[Link("/c/:catSlug/f/:forumSlug/t/:topicSlug", Link::POST, ['.*?'], "/forum")]
    public function publicTopicResponsePost(Request $request, string $catSlug, string $forumSlug, string $topicSlug): void
    {
        if (!UsersController::isUserLogged()) {
            Flash::send(Alert::ERROR, "Forum", "Connectez-vous avant de répondre.");
            Redirect::redirect('login');
        }

        $userId = UsersModel::getCurrentUser()->getId();

        if (!ForumPermissionModel::getInstance()->hasForumPermission($userId, "user_response_topic")) {
            ForumController::getInstance()->alertNotHavePermissions("user_response_topic");
            Redirect::redirectPreviousRoute();
        }


        $topic = ForumTopicModel::getInstance()->getTopicBySlug($topicSlug);
        $responseModel = ForumResponseModel::getInstance();

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

    #[Link("/c/:catSlug/f/:forumSlug/t/:topicSlug/trash/:replyId/:reason", Link::GET, ['.*?' => 'topicSlug', '[0-9]+' => 'replyId'], "/forum")]
    public function publicTopicReplyDelete(Request $request, string $catSlug, string $forumSlug, string $topicSlug, int $replyId, int $reason): void
    {
        $topic = ForumTopicModel::getInstance()->getTopicBySlug($topicSlug);

        if (UsersModel::getCurrentUser()->getId() !== $topic->getUser()->getId()) {
            Flash::send(Alert::ERROR, "Erreur", "Vous n'avez pas la permission de faire ceci !");
            Redirect::redirect("forum");
        }

        if (is_null($topic)) {
            Flash::send("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.internalError"));
            return;
        }

        $reply = ForumResponseModel::getInstance()->getResponseById($replyId);

        if (!$reply?->isSelfReply()) {//Rajouter ici si on as la permission de supprimer (staff)
            Flash::send("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("forum.reply.delete.errors.no_access"));
            return;
        }

        if (ForumResponseModel::getInstance()->trashResponse($replyId, $reason)) {

            Flash::send("success", LangManager::translate("core.toaster.success"),
                LangManager::translate("forum.reply.delete.success"));

            Redirect::redirectPreviousRoute();
        }
    }


    #[Link("/c/:catSlug/f/:forumSlug/t/:topicSlug/edit", Link::GET, ['.*?'], "/forum")]
    public function publicTopicEdit(Request $request, string $catSlug, string $forumSlug, string $topicSlug): void
    {
        if (!UsersController::isUserLogged()) {
            Flash::send(Alert::ERROR, "Forum", "Connectez-vous avant de modifier ce topic.");
            Redirect::redirect('login');
        }

        $category = ForumCategoryModel::getInstance()->getCatBySlug($catSlug);
        $forum = forumModel::getInstance()->getForumBySlug($forumSlug);

        $topic = ForumTopicModel::getInstance()->getTopicBySlug($topicSlug);

        if (UsersModel::getCurrentUser()->getId() !== $topic->getUser()->getId()) {
            Flash::send(Alert::ERROR, "Erreur", "Vous n'avez pas la permission de faire ceci !");
            Redirect::redirect("forum");
        }

        $view = new View("Forum", "editTopic");
        $view->addVariableList(["topic" => $topic,"category" => $category,"forum" => $forum]);
        $view->addScriptBefore("Admin/Resources/Vendors/Tinymce/tinymce.min.js", "Admin/Resources/Vendors/Tinymce/Config/full.js");
        $view->addStyle("Admin/Resources/Vendors/Fontawesome-free/Css/fa-all.min.css");
        $view->view();
    }

    #[Link("/c/:catSlug/f/:forumSlug/t/:topicSlug/edit", Link::POST, ['.*?'], "/forum")]
    public function publicTopicEditPost(Request $request, string $catSlug, string $forumSlug, string $topicSlug): void
    {
        [$topicId, $name, $content, $tags] = Utils::filterInput('topicId', 'name', 'content', 'tags');

        $topic = ForumTopicModel::getInstance()->getTopicBySlug($topicSlug);

        if (UsersModel::getCurrentUser()->getId() !== $topic->getUser()->getId()) {
            Flash::send(Alert::ERROR, "Erreur", "Vous n'avez pas la permission de faire ceci !");
            Redirect::redirect("forum");
        }

        if (is_null($topic) || Utils::containsNullValue($name, $content)) {
            Flash::send("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.internalError"));
            Website::refresh();
            return;
        }

        $res = ForumTopicModel::getInstance()->authorEditTopic($topicId, $name, $content);

        // Add tags

        $tags = explode(",", $tags);
        //Need to clear tag befor update
        ForumTopicModel::getInstance()->clearTag($topicId);
        foreach ($tags as $tag) {
            //Clean tag
            $tag = mb_strtolower(trim($tag));

            if (empty($tag)) {
                continue;
            }

            ForumTopicModel::getInstance()->addTag($tag, $topicId);
        }

        Flash::send("success", LangManager::translate("core.toaster.success"),
            "Topic mis à jour");

        header("location: ../../t/{$topic->getSlug()}");
    }


}