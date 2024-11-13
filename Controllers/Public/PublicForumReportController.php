<?php

namespace CMW\Controller\Forum\Public;

use CMW\Controller\Forum\Admin\ForumPermissionController;
use CMW\Controller\Users\UsersController;
use CMW\Controller\Users\UsersSessionsController;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Forum\ForumCategoryModel;
use CMW\Model\Forum\ForumModel;
use CMW\Model\Forum\ForumReportedModel;
use CMW\Model\Forum\ForumSettingsModel;
use CMW\Model\Forum\ForumTopicModel;
use CMW\Model\Forum\ForumUserBlockedModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use CMW\Utils\Website;

/**
 * Class: @ForumSettingsController
 * @package Forum
 * @author Zomb
 * @desc You can create "reportTopic.view.php" or "reportResponse.view.php" in Theme/Views/Forum/** or directly use à modal and call action"" form > POST
 * @version 0.0.1
 */
class PublicForumReportController extends AbstractController
{
    #[Link('/c/:catSlug/f/:forumSlug/t/:topicSlug/p:page/reportTopic/:topicId', Link::GET, ['.*?'], '/forum')]
    private function publicReportTopic(string $catSlug, string $forumSlug, string $topicSlug, int $page, int $topicId): void
    {
        $visitorCanViewForum = ForumSettingsModel::getInstance()->getOptionValue('visitorCanViewForum');

        if ($visitorCanViewForum === '0') {
            ForumPermissionController::getInstance()->redirectIfNotHavePermissions('user_view_forum');
        }

        $category = ForumCategoryModel::getInstance()->getCatBySlug($catSlug);
        $forum = forumModel::getInstance()->getForumBySlug($forumSlug);
        $topic = ForumTopicModel::getInstance()->getTopicBySlug($topicSlug);

        View::createPublicView('Forum', 'reportTopic')
            ->addVariableList(['category' => $category, 'forum' => $forum, 'topic' => $topic, 'topicId' => $topicId])
            ->addStyle('Admin/Resources/Vendors/Fontawesome-free/Css/fa-all.min.css')
            ->view();
    }

    #[Link('/c/:catSlug/f/:forumSlug/t/:topicSlug/p:page/reportResponse/:responseId', Link::GET, ['.*?'], '/forum')]
    private function publicReportResponse(string $catSlug, string $forumSlug, string $topicSlug, int $page, int $responseId): void
    {
        $visitorCanViewForum = ForumSettingsModel::getInstance()->getOptionValue('visitorCanViewForum');

        if ($visitorCanViewForum === '0') {
            ForumPermissionController::getInstance()->redirectIfNotHavePermissions('user_view_forum');
        }

        $category = ForumCategoryModel::getInstance()->getCatBySlug($catSlug);
        $forum = forumModel::getInstance()->getForumBySlug($forumSlug);
        $topic = ForumTopicModel::getInstance()->getTopicBySlug($topicSlug);

        View::createPublicView('Forum', 'reportResponse')
            ->addVariableList(['category' => $category, 'forum' => $forum, 'topic' => $topic, 'topicId' => $responseId])
            ->addStyle('Admin/Resources/Vendors/Fontawesome-free/Css/fa-all.min.css')
            ->view();
    }

    #[Link('/c/:catSlug/f/:forumSlug/t/:topicSlug/p:page/reportTopic/:topicId', Link::POST, ['.*?'], '/forum')]
    private function publicReportTopicPost(string $catSlug, string $forumSlug, string $topicSlug, int $page, int $topicId): void
    {
        if (!UsersController::isUserLogged()) {
            Flash::send(Alert::ERROR, 'Forum', 'Connectez-vous avant de signaler ce topic.');
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

        $topic = ForumTopicModel::getInstance()->getTopicBySlug($topicSlug);

        if (!$topic) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('core.toaster.internalError'));
            Website::refresh();
            return;
        }

        [$reason] = Utils::filterInput('reason');

        ForumReportedModel::getInstance()->creatTopicReport($userId, $topicId, $reason);

        Flash::send(Alert::ERROR, 'Forum',
            'Votre signalement est pris en compte et sera éxaminer');
        Redirect::redirectPreviousRoute();
    }

    #[Link('/c/:catSlug/f/:forumSlug/t/:topicSlug/p:page/reportResponse/:responseId', Link::POST, ['.*?'], '/forum')]
    private function publicReportResponsePost(string $catSlug, string $forumSlug, string $topicSlug, int $page, int $responseId): void
    {
        if (!UsersController::isUserLogged()) {
            Flash::send(Alert::ERROR, 'Forum', 'Connectez-vous avant de signaler ce topic.');
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
            Flash::send(Alert::ERROR, 'Forum', 'Vous ne pouvez plus faire ceci, vous êtes bloqué pour la raison : ' . $userBlocked->getUserBlockedByUserId($userId)->getReason());
            Redirect::redirectPreviousRoute();
        }

        $topic = ForumTopicModel::getInstance()->getTopicBySlug($topicSlug);

        if (!$topic) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('core.toaster.internalError'));
            Website::refresh();
            return;
        }

        [$reason] = Utils::filterInput('reason');

        ForumReportedModel::getInstance()->creatResponseReport($userId, $responseId, $reason);

        Flash::send('success', 'Forum',
            'Votre signalement est pris en compte et sera éxaminer');
        Redirect::redirectPreviousRoute();
    }
}
