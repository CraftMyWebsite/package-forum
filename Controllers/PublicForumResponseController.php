<?php
namespace CMW\Controller\Forum;

use CMW\Controller\Core\CoreController;
use CMW\Controller\Users\UsersController;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Requests\Request;
use CMW\Manager\Router\Link;
use CMW\Model\Forum\ForumCategoryModel;
use CMW\Model\Forum\ForumFeedbackModel;
use CMW\Model\Forum\ForumModel;
use CMW\Model\Forum\ForumPermissionModel;
use CMW\Model\Forum\ForumResponseModel;
use CMW\Model\Forum\ForumTopicModel;
use CMW\Model\Forum\ForumUserBlockedModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use CMW\Utils\Website;
use JetBrains\PhpStorm\NoReturn;


/**
 * Class: @PublicForumResponseController
 * @package Forum
 * @author Zomb
 * @version 1.0
 */
class PublicForumResponseController extends CoreController
{
    #[Link("/c/:catSlug/f/:forumSlug/t/:topicSlug", Link::POST, ['.*?'], "/forum")]
    public function publicTopicResponsePost(Request $request, string $catSlug, string $forumSlug, string $topicSlug): void
    {
        if (!UsersController::isUserLogged()) {
            Flash::send(Alert::ERROR, "Forum", "Connectez-vous avant de répondre.");
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

        $userId = UsersModel::getCurrentUser()->getId();
        $userBlocked = ForumUserBlockedModel::getInstance();
        if ($userBlocked->getUserBlockedByUserId($userId)->isBlocked()) {
            Flash::send(Alert::ERROR, "Forum", "Vous ne pouvez plus faire ceci, vous êtes bloqué pour la raison : " . $userBlocked->getUserBlockedByUserId($userId)->getReason());
            Redirect::redirectPreviousRoute();
        }
        if (!ForumPermissionModel::getInstance()->hasForumPermission($userId, "user_response_topic")) {
            ForumPermissionController::getInstance()->alertNotHavePermissions("user_response_topic");
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

    #[NoReturn] #[Link("/c/:catSlug/f/:forumSlug/t/:topicSlug/response_react/:responseId/:feedbackId", Link::GET, ['.*?'], "/forum")]
    public function publicResponseAddFeedback(Request $request, string $catSlug, string $forumSlug, string $topicSlug, int $responseId, int $feedbackId): void
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

        ForumPermissionController::getInstance()->redirectIfNotHavePermissions("user_response_react");
        $userBlocked = ForumUserBlockedModel::getInstance();
        $userId = UsersModel::getCurrentUser()->getId();
        if ($userBlocked->getUserBlockedByUserId($userId)->isBlocked()) {
            Flash::send(Alert::ERROR, "Forum", "Vous ne pouvez plus faire ceci, vous êtes bloqué pour la raison : " . $userBlocked->getUserBlockedByUserId($userId)->getReason());
            Redirect::redirectPreviousRoute();
        }
        $user = usersModel::getInstance()::getCurrentUser();
        ForumFeedbackModel::getInstance()->addFeedbackResponseByFeedbackId($responseId, $feedbackId, $user?->getId());

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link("/c/:catSlug/f/:forumSlug/t/:topicSlug/response_un_react/:responseId/:feedbackId", Link::GET, ['.*?'], "/forum")]
    public function publicResponseDeleteFeedback(Request $request, string $catSlug, string $forumSlug, string $topicSlug, int $responseId, int $feedbackId): void
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

        ForumPermissionController::getInstance()->redirectIfNotHavePermissions("user_response_remove_react");
        $userBlocked = ForumUserBlockedModel::getInstance();
        $userId = UsersModel::getCurrentUser()->getId();
        if ($userBlocked->getUserBlockedByUserId($userId)->isBlocked()) {
            Flash::send(Alert::ERROR, "Forum", "Vous ne pouvez plus faire ceci, vous êtes bloqué pour la raison : " . $userBlocked->getUserBlockedByUserId($userId)->getReason());
            Redirect::redirectPreviousRoute();
        }
        $user = usersModel::getInstance()::getCurrentUser();
        ForumFeedbackModel::getInstance()->removeFeedbackResponseByFeedbackId($responseId, $feedbackId, $user?->getId());

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link("/c/:catSlug/f/:forumSlug/t/:topicSlug/response_change_react/:responseId/:feedbackId", Link::GET, ['.*?'], "/forum")]
    public function publicResponseChangeFeedback(Request $request, string $catSlug, string $forumSlug, string $topicSlug, int $responseId, int $feedbackId): void
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

        ForumPermissionController::getInstance()->redirectIfNotHavePermissions("user_response_change_react");
        $userBlocked = ForumUserBlockedModel::getInstance();
        $userId = UsersModel::getCurrentUser()->getId();
        if ($userBlocked->getUserBlockedByUserId($userId)->isBlocked()) {
            Flash::send(Alert::ERROR, "Forum", "Vous ne pouvez plus faire ceci, vous êtes bloqué pour la raison : " . $userBlocked->getUserBlockedByUserId($userId)->getReason());
            Redirect::redirectPreviousRoute();
        }
        $user = usersModel::getInstance()::getCurrentUser();
        ForumFeedbackModel::getInstance()->changeFeedbackResponseByFeedbackId($responseId, $feedbackId, $user?->getId());

        Redirect::redirectPreviousRoute();
    }
}