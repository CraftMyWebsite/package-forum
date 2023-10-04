<?php
namespace CMW\Controller\Forum;

use CMW\Controller\Core\CoreController;
use CMW\Controller\Users\UsersController;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Requests\Request;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Forum\ForumCategoryModel;
use CMW\Model\Forum\ForumFeedbackModel;
use CMW\Model\Forum\ForumModel;
use CMW\Model\Forum\ForumResponseModel;
use CMW\Model\Forum\ForumSettingsModel;
use CMW\Model\Forum\ForumTopicModel;
use CMW\Model\Forum\ForumUserBlockedModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use CMW\Utils\Website;
use JetBrains\PhpStorm\NoReturn;


/**
 * Class: @PublicForumTopicController
 * @package Forum
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class PublicForumTopicController extends CoreController
{
    #[Link("/c/:catSlug/f/:forumSlug/t/:topicSlug", Link::GET, ['.*?'], "/forum")]
    public function publicTopicView(Request $request, string $catSlug, string $forumSlug, string $topicSlug): void
    {
        $visitorCanViewForum = ForumSettingsModel::getInstance()->getOptionValue("visitorCanViewForum");

        if ($visitorCanViewForum === "0") {
            ForumPermissionController::getInstance()->redirectIfNotHavePermissions("user_view_topic");
        }

        $category = ForumCategoryModel::getInstance()->getCatBySlug($catSlug);
        $forum = forumModel::getInstance()->getForumBySlug($forumSlug);

        if (!$category->isUserAllowed()) {
            Flash::send(Alert::ERROR, "Forum", "Cette catégorie est privé !");
            Redirect::redirect("forum");
        }
        if (!$forum->isUserAllowed()) {
            Flash::send(Alert::ERROR, "Forum", "Ce forum est privé !");
            Redirect::redirect("forum");
        }

        $forumModel = forumModel::getInstance();
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
        $view->addVariableList(["forumModel" => $forumModel,"currentUser" => $currentUser, "topic" => $topic, "feedbackModel" => $feedbackModel, "responseModel" => ForumResponseModel::getInstance(), "iconNotRead" => $iconNotRead, "iconImportant" => $iconImportant, "iconPin" => $iconPin, "iconClosed" => $iconClosed, "forum" => $forum, "category" => $category]);
        $view->addScriptBefore("Admin/Resources/Vendors/Tinymce/tinymce.min.js", "Admin/Resources/Vendors/Tinymce/Config/full.js");
        $view->addStyle("Admin/Resources/Vendors/Fontawesome-free/Css/fa-all.min.css");
        $view->view();
    }

    #[Link("/c/:catSlug/f/:forumSlug/t/:topicSlug/edit", Link::GET, ['.*?'], "/forum")]
    public function publicTopicEdit(Request $request, string $catSlug, string $forumSlug, string $topicSlug): void
    {
        if (!UsersController::isUserLogged()) {
            Flash::send(Alert::ERROR, "Forum", "Connectez-vous avant de modifier ce topic.");
            Redirect::redirect('login');
        }
        $userBlocked = ForumUserBlockedModel::getInstance();
        $userId = UsersModel::getCurrentUser()->getId();
        if ($userBlocked->getUserBlockedByUserId($userId)->isBlocked()) {
            Flash::send(Alert::ERROR, "Forum", "Vous ne pouvez plus faire ceci, vous êtes bloqué pour la raison : " . $userBlocked->getUserBlockedByUserId($userId)->getReason());
            Redirect::redirectPreviousRoute();
        }

        $category = ForumCategoryModel::getInstance()->getCatBySlug($catSlug);
        $forum = forumModel::getInstance()->getForumBySlug($forumSlug);
        if (!$category->isUserAllowed()) {
            Flash::send(Alert::ERROR, "Forum", "Cette catégorie est privé !");
            Redirect::redirect("forum");
        }
        if (!$forum->isUserAllowed()) {
            Flash::send(Alert::ERROR, "Forum", "Ce forum est privé !");
            Redirect::redirect("forum");
        }

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
        $userBlocked = ForumUserBlockedModel::getInstance();
        $userId = UsersModel::getCurrentUser()->getId();
        if ($userBlocked->getUserBlockedByUserId($userId)->isBlocked()) {
            Flash::send(Alert::ERROR, "Forum", "Vous ne pouvez plus faire ceci, vous êtes bloqué pour la raison : " . $userBlocked->getUserBlockedByUserId($userId)->getReason());
            Redirect::redirectPreviousRoute();
        }
        [$topicId, $name, $content, $tags] = Utils::filterInput('topicId', 'name', 'content', 'tags');

        $category = ForumCategoryModel::getInstance()->getCatBySlug($catSlug);
        $forum = forumModel::getInstance()->getForumBySlug($forumSlug);
        if (!$category->isUserAllowed()) {
            Flash::send(Alert::ERROR, "Forum", "Cette catégorie est privé !");
            Redirect::redirect("forum");
        }
        if (!$forum->isUserAllowed()) {
            Flash::send(Alert::ERROR, "Forum", "Ce forum est privé !");
            Redirect::redirect("forum");
        }

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

    #[NoReturn] #[Link("/c/:catSlug/f/:forumSlug/t/:topicSlug/react/:topicId/:feedbackId", Link::GET, ['.*?'], "/forum")]
    public function publicTopicAddFeedback(Request $request, string $catSlug, string $forumSlug, string $topicSlug, int $topicId, int $feedbackId): void
    {
        if (!UsersController::isUserLogged()) {
            Flash::send(Alert::ERROR, "Forum", "Connectez-vous avant de réagire.");
            Redirect::redirect('login');
        }

        $category = ForumCategoryModel::getInstance()->getCatBySlug($catSlug);
        $forum = forumModel::getInstance()->getForumBySlug($forumSlug);
        if (!$category->isUserAllowed()) {
            Flash::send(Alert::ERROR, "Forum", "Cette catégorie est privé !");
            Redirect::redirect("forum");
        }
        if (!$forum->isUserAllowed()) {
            Flash::send(Alert::ERROR, "Forum", "Ce forum est privé !");
            Redirect::redirect("forum");
        }
        $userBlocked = ForumUserBlockedModel::getInstance();
        $userId = UsersModel::getCurrentUser()->getId();
        if ($userBlocked->getUserBlockedByUserId($userId)->isBlocked()) {
            Flash::send(Alert::ERROR, "Forum", "Vous ne pouvez plus faire ceci, vous êtes bloqué pour la raison : " . $userBlocked->getUserBlockedByUserId($userId)->getReason());
            Redirect::redirectPreviousRoute();
        }
        ForumPermissionController::getInstance()->redirectIfNotHavePermissions("user_react_topic");

        $user = usersModel::getInstance()::getCurrentUser();
        ForumFeedbackModel::getInstance()->addFeedbackByFeedbackId($topicId, $feedbackId, $user?->getId());

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link("/c/:catSlug/f/:forumSlug/t/:topicSlug/un_react/:topicId/:feedbackId", Link::GET, ['.*?'], "/forum")]
    public function publicTopicDeleteFeedback(Request $request, string $catSlug, string $forumSlug, string $topicSlug, int $topicId, int $feedbackId): void
    {
        if (!UsersController::isUserLogged()) {
            Flash::send(Alert::ERROR, "Forum", "Connectez-vous avant de réagire.");
            Redirect::redirect('login');
        }

        $category = ForumCategoryModel::getInstance()->getCatBySlug($catSlug);
        $forum = forumModel::getInstance()->getForumBySlug($forumSlug);
        if (!$category->isUserAllowed()) {
            Flash::send(Alert::ERROR, "Forum", "Cette catégorie est privé !");
            Redirect::redirect("forum");
        }
        if (!$forum->isUserAllowed()) {
            Flash::send(Alert::ERROR, "Forum", "Ce forum est privé !");
            Redirect::redirect("forum");
        }

        ForumPermissionController::getInstance()->redirectIfNotHavePermissions("user_remove_react_topic");
        $userBlocked = ForumUserBlockedModel::getInstance();
        $userId = UsersModel::getCurrentUser()->getId();
        if ($userBlocked->getUserBlockedByUserId($userId)->isBlocked()) {
            Flash::send(Alert::ERROR, "Forum", "Vous ne pouvez plus faire ceci, vous êtes bloqué pour la raison : " . $userBlocked->getUserBlockedByUserId($userId)->getReason());
            Redirect::redirectPreviousRoute();
        }
        $user = usersModel::getInstance()::getCurrentUser();
        ForumFeedbackModel::getInstance()->removeFeedbackByFeedbackId($topicId, $feedbackId, $user?->getId());

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link("/c/:catSlug/f/:forumSlug/t/:topicSlug/change_react/:topicId/:feedbackId", Link::GET, ['.*?'], "/forum")]
    public function publicTopicChangeFeedback(Request $request, string $catSlug, string $forumSlug, string $topicSlug, int $topicId, int $feedbackId): void
    {
        if (!UsersController::isUserLogged()) {
            Flash::send(Alert::ERROR, "Forum", "Connectez-vous avant de réagire.");
            Redirect::redirect('login');
        }

        $category = ForumCategoryModel::getInstance()->getCatBySlug($catSlug);
        $forum = forumModel::getInstance()->getForumBySlug($forumSlug);
        if (!$category->isUserAllowed()) {
            Flash::send(Alert::ERROR, "Forum", "Cette catégorie est privé !");
            Redirect::redirect("forum");
        }
        if (!$forum->isUserAllowed()) {
            Flash::send(Alert::ERROR, "Forum", "Ce forum est privé !");
            Redirect::redirect("forum");
        }

        ForumPermissionController::getInstance()->redirectIfNotHavePermissions("user_change_react_topic");
        $userBlocked = ForumUserBlockedModel::getInstance();
        $userId = UsersModel::getCurrentUser()->getId();
        if ($userBlocked->getUserBlockedByUserId($userId)->isBlocked()) {
            Flash::send(Alert::ERROR, "Forum", "Vous ne pouvez plus faire ceci, vous êtes bloqué pour la raison : " . $userBlocked->getUserBlockedByUserId($userId)->getReason());
            Redirect::redirectPreviousRoute();
        }
        $user = usersModel::getInstance()::getCurrentUser();
        ForumFeedbackModel::getInstance()->changeFeedbackByFeedbackId($topicId, $feedbackId, $user?->getId());

        Redirect::redirectPreviousRoute();
    }
}
