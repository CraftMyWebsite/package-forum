<?php

namespace CMW\Controller\Forum\Public;

use CMW\Controller\Forum\Admin\ForumFollowedController;
use CMW\Controller\Forum\Admin\ForumPermissionController;
use CMW\Controller\Users\UsersController;
use CMW\Controller\Users\UsersSessionsController;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Model\Core\MailModel;
use CMW\Model\Forum\ForumCategoryModel;
use CMW\Model\Forum\ForumFeedbackModel;
use CMW\Model\Forum\ForumFollowedModel;
use CMW\Model\Forum\ForumModel;
use CMW\Model\Forum\ForumPermissionModel;
use CMW\Model\Forum\ForumResponseModel;
use CMW\Model\Forum\ForumTopicModel;
use CMW\Model\Forum\ForumUserBlockedModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use CMW\Utils\Website;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class: @PublicForumResponseController
 * @package Forum
 * @author Zomb
 * @version 0.0.1
 */
class PublicForumResponseController extends AbstractController
{
    #[Link('/c/:catSlug/f/:forumSlug/t/:topicSlug/p:page', Link::POST, ['.*?'], '/forum')]
    private function publicTopicResponsePost(string $catSlug, string $forumSlug, string $topicSlug, int $page): void
    {
        if (!UsersController::isUserLogged()) {
            Flash::send(Alert::ERROR, 'Forum', 'Connectez-vous avant de répondre.');
            Redirect::redirect('login');
        }

        $category = ForumCategoryModel::getInstance()->getCatBySlug($catSlug);

        if (!$category) {
            Redirect::errorPage(404);
        }

        $forum = forumModel::getInstance()->getForumBySlug($forumSlug);

        if (!$forum) {
            Redirect::errorPage(404);
        }

        if (!$category->isUserAllowed()) {
            Flash::send(Alert::ERROR, 'Forum', 'Cette catégorie est privé !');
            Redirect::redirect('forum');
        }
        if (!$forum->isUserAllowed()) {
            Flash::send(Alert::ERROR, 'Forum', 'Ce forum est privé !');
            Redirect::redirect('forum');
        }

        $userId = UsersSessionsController::getInstance()->getCurrentUser()?->getId();
        $userBlocked = ForumUserBlockedModel::getInstance();
        if ($userBlocked->getUserBlockedByUserId($userId)?->isBlocked()) {
            Flash::send(Alert::ERROR, 'Forum', 'Vous ne pouvez plus faire ceci, vous êtes bloqué pour la raison : ' . $userBlocked->getUserBlockedByUserId($userId)?->getReason());
            Redirect::redirectPreviousRoute();
        }
        if (!ForumPermissionModel::getInstance()->hasForumPermission($userId, 'user_response_topic')) {
            ForumPermissionController::getInstance()->alertNotHavePermissions('user_response_topic');
            Redirect::redirectPreviousRoute();
        }

        $topic = ForumTopicModel::getInstance()->getTopicBySlug($topicSlug);
        $responseModel = ForumResponseModel::getInstance();

        if (!$topic) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('core.toaster.internalError'));
            Website::refresh();
            return;
        }

        if ($topic->isDisallowReplies()) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('forum.topic.replies.error.disallow_replies'));
            Website::refresh();
            return;
        }

        [$topicId, $content] = Utils::filterInput('topicId', 'topicResponse');

        if (Utils::containsNullValue($topicId, $content)) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('forum.category.toaster.error.empty_input'));
            Website::refresh();
            return;
        }

        $responseEntity = $responseModel::getInstance()->createResponse($content, $userId, $topicId);

        if (is_null($responseEntity)) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('core.toaster.internalError'));
            Website::refresh();
            return;
        }

        if (MailModel::getInstance()->getConfig() !== null && MailModel::getInstance()->getConfig()->isEnable()) {
            $followers = ForumFollowedModel::getInstance()->getFollowerByTopicId($topicId);
            foreach ($followers as $follower) {
                $mail = $follower->getUser()->getMail();
                $responseURL = Website::getProtocol() . '://' . $_SERVER['SERVER_NAME'] . EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . $_SERVER['REQUEST_URI'] . '#' . $responseEntity->getId();
                $topicName = $follower->getTopic()->getName();
                $responseUser = $follower->getTopic()->getLastResponse()?->getUser()->getPseudo();
                $responseContent = $follower->getTopic()->getLastResponse()?->getContent();
                ForumFollowedController::getInstance()->sendMailToFollower($mail, $responseURL, $topicName, $responseUser, $responseContent);
            }
        }

        Flash::send('success', LangManager::translate('core.toaster.success'),
            LangManager::translate('forum.topic.replies.success'));

        header('Location: ' . EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . "forum/c/$catSlug/f/$forumSlug/t/$topicSlug/p" . $responseEntity->getPageNumber() . '/#' . $responseEntity->getId());
    }

    #[NoReturn]
    #[Link('/c/:catSlug/f/:forumSlug/t/:topicSlug/p:page/response_react/:responseId/:feedbackId', Link::GET, ['.*?'], '/forum')]
    private function publicResponseAddFeedback(string $catSlug, string $forumSlug, string $topicSlug, int $page, int $responseId, int $feedbackId): void
    {
        if (!UsersController::isUserLogged()) {
            Flash::send(Alert::ERROR, 'Forum', 'Connectez-vous avant de réagire.');
            Redirect::redirect('login');
        }

        $category = ForumCategoryModel::getInstance()->getCatBySlug($catSlug);

        if (!$category) {
            Redirect::errorPage(404);
        }

        $forum = forumModel::getInstance()->getForumBySlug($forumSlug);

        if (!$forum) {
            Redirect::errorPage(404);
        }

        if (!$category->isUserAllowed()) {
            Flash::send(Alert::ERROR, 'Forum', 'Cette catégorie est privé !');
            Redirect::redirect('forum');
        }
        if (!$forum->isUserAllowed()) {
            Flash::send(Alert::ERROR, 'Forum', 'Ce forum est privé !');
            Redirect::redirect('forum');
        }

        ForumPermissionController::getInstance()->redirectIfNotHavePermissions('user_response_react');
        $userBlocked = ForumUserBlockedModel::getInstance();
        $userId = UsersSessionsController::getInstance()->getCurrentUser()?->getId();
        if ($userBlocked->getUserBlockedByUserId($userId)?->isBlocked()) {
            Flash::send(Alert::ERROR, 'Forum', 'Vous ne pouvez plus faire ceci, vous êtes bloqué pour la raison : ' . $userBlocked->getUserBlockedByUserId($userId)?->getReason());
            Redirect::redirectPreviousRoute();
        }
        ForumFeedbackModel::getInstance()->addFeedbackResponseByFeedbackId($responseId, $feedbackId, $userId);

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/c/:catSlug/f/:forumSlug/t/:topicSlug/p:page/response_un_react/:responseId/:feedbackId', Link::GET, ['.*?'], '/forum')]
    private function publicResponseDeleteFeedback(string $catSlug, string $forumSlug, string $topicSlug, int $page, int $responseId, int $feedbackId): void
    {
        if (!UsersController::isUserLogged()) {
            Flash::send(Alert::ERROR, 'Forum', 'Connectez-vous avant de réagire.');
            Redirect::redirect('login');
        }

        $category = ForumCategoryModel::getInstance()->getCatBySlug($catSlug);

        if (!$category) {
            Redirect::errorPage(404);
        }

        $forum = forumModel::getInstance()->getForumBySlug($forumSlug);

        if (!$forum) {
            Redirect::errorPage(404);
        }

        if (!$category->isUserAllowed()) {
            Flash::send(Alert::ERROR, 'Forum', 'Cette catégorie est privé !');
            Redirect::redirect('forum');
        }
        if (!$forum->isUserAllowed()) {
            Flash::send(Alert::ERROR, 'Forum', 'Ce forum est privé !');
            Redirect::redirect('forum');
        }

        ForumPermissionController::getInstance()->redirectIfNotHavePermissions('user_response_remove_react');
        $userBlocked = ForumUserBlockedModel::getInstance();
        $userId = UsersSessionsController::getInstance()->getCurrentUser()?->getId();
        if ($userBlocked->getUserBlockedByUserId($userId)?->isBlocked()) {
            Flash::send(Alert::ERROR, 'Forum', 'Vous ne pouvez plus faire ceci, vous êtes bloqué pour la raison : ' . $userBlocked->getUserBlockedByUserId($userId)?->getReason());
            Redirect::redirectPreviousRoute();
        }
        ForumFeedbackModel::getInstance()->removeFeedbackResponseByFeedbackId($responseId, $feedbackId, $userId);

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/c/:catSlug/f/:forumSlug/t/:topicSlug/p:page/response_change_react/:responseId/:feedbackId', Link::GET, ['.*?'], '/forum')]
    private function publicResponseChangeFeedback(string $catSlug, string $forumSlug, string $topicSlug, int $page, int $responseId, int $feedbackId): void
    {
        if (!UsersController::isUserLogged()) {
            Flash::send(Alert::ERROR, 'Forum', 'Connectez-vous avant de réagire.');
            Redirect::redirect('login');
        }

        $category = ForumCategoryModel::getInstance()->getCatBySlug($catSlug);

        if (!$category) {
            Redirect::errorPage(404);
        }

        $forum = forumModel::getInstance()->getForumBySlug($forumSlug);

        if (!$forum) {
            Redirect::errorPage(404);
        }

        if (!$category->isUserAllowed()) {
            Flash::send(Alert::ERROR, 'Forum', 'Cette catégorie est privé !');
            Redirect::redirect('forum');
        }
        if (!$forum->isUserAllowed()) {
            Flash::send(Alert::ERROR, 'Forum', 'Ce forum est privé !');
            Redirect::redirect('forum');
        }

        ForumPermissionController::getInstance()->redirectIfNotHavePermissions('user_response_change_react');
        $userBlocked = ForumUserBlockedModel::getInstance();
        $userId = UsersSessionsController::getInstance()->getCurrentUser()?->getId();
        if ($userBlocked->getUserBlockedByUserId($userId)?->isBlocked()) {
            Flash::send(Alert::ERROR, 'Forum', 'Vous ne pouvez plus faire ceci, vous êtes bloqué pour la raison : ' . $userBlocked->getUserBlockedByUserId($userId)?->getReason());
            Redirect::redirectPreviousRoute();
        }
        ForumFeedbackModel::getInstance()->changeFeedbackResponseByFeedbackId($responseId, $feedbackId, $userId);

        Redirect::redirectPreviousRoute();
    }
}
