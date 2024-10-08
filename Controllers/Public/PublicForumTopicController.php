<?php

namespace CMW\Controller\Forum\Public;

use CMW\Controller\Forum\Admin\ForumPermissionController;
use CMW\Controller\Users\UsersController;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Forum\ForumCategoryModel;
use CMW\Model\Forum\ForumDiscordModel;
use CMW\Model\Forum\ForumFeedbackModel;
use CMW\Model\Forum\ForumFollowedModel;
use CMW\Model\Forum\ForumModel;
use CMW\Model\Forum\ForumPermissionModel;
use CMW\Model\Forum\ForumResponseModel;
use CMW\Model\Forum\ForumSettingsModel;
use CMW\Model\Forum\ForumTopicModel;
use CMW\Model\Forum\ForumUserBlockedModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Client;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use CMW\Utils\Website;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class: @PublicForumTopicController
 * @package Forum
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 0.0.1
 */
class PublicForumTopicController extends AbstractController
{
    #[Link('/search', Link::POST, ['.*?'], '/forum')]
    private function publicForumResearch(): void
    {
        [$for] = Utils::filterInput('for');

        $results = ForumTopicModel::getInstance()->getTopicByResearch($for);
        $forumModel = forumModel::getInstance();
        $iconNotRead = ForumSettingsModel::getInstance()->getOptionValue('IconNotRead');
        $iconNotReadColor = ForumSettingsModel::getInstance()->getOptionValue('IconNotReadColor');
        $iconImportant = ForumSettingsModel::getInstance()->getOptionValue('IconImportant');
        $iconImportantColor = ForumSettingsModel::getInstance()->getOptionValue('IconImportantColor');
        $iconPin = ForumSettingsModel::getInstance()->getOptionValue('IconPin');
        $iconPinColor = ForumSettingsModel::getInstance()->getOptionValue('IconPinColor');
        $iconClosed = ForumSettingsModel::getInstance()->getOptionValue('IconClosed');
        $iconClosedColor = ForumSettingsModel::getInstance()->getOptionValue('IconClosedColor');

        $view = new View('Forum', 'search');
        $view->addVariableList(['forumModel' => $forumModel, 'results' => $results, 'for' => $for, 'iconNotRead' => $iconNotRead, 'iconImportant' => $iconImportant, 'iconPin' => $iconPin, 'iconClosed' => $iconClosed, 'iconNotReadColor' => $iconNotReadColor, 'iconImportantColor' => $iconImportantColor, 'iconPinColor' => $iconPinColor, 'iconClosedColor' => $iconClosedColor, 'responseModel' => ForumResponseModel::getInstance()]);
        $view->addStyle('Admin/Resources/Vendors/Fontawesome-free/Css/fa-all.min.css');
        $view->view();
    }

    #[Link('/c/:catSlug/f/:forumSlug/fp:forumPage/add', Link::GET, ['.*?'], '/forum')]
    private function publicForumAddTopicView(string $catSlug, string $forumSlug, int $forumPage): void
    {
        ForumPermissionController::getInstance()->redirectIfNotHavePermissions('user_create_topic');

        $category = ForumCategoryModel::getInstance()->getCatBySlug($catSlug);

        if (!$category) {
            Redirect::errorPage(404);
        }

        $forum = forumModel::getInstance()->getForumBySlug($forumSlug);

        if (!$forum) {
            Redirect::errorPage(404);
        }

        $forumModel = forumModel::getInstance();

        if (!$category->isUserAllowed()) {
            Flash::send(Alert::ERROR, 'Forum', 'Cette catégorie est privé !');
            Redirect::redirect('forum');
        }
        if (!$forum->isUserAllowed()) {
            Flash::send(Alert::ERROR, 'Forum', 'Ce forum est privé !');
            Redirect::redirect('forum');
        }
        if ($forum->disallowTopics()) {
            Flash::send(Alert::ERROR, 'Forum', "Ce forum n'autorise pas la création de nouveau topics");
            Redirect::redirectPreviousRoute();
        }
        $userBlocked = ForumUserBlockedModel::getInstance();
        $userId = UsersModel::getCurrentUser()?->getId();
        if ($userBlocked->getUserBlockedByUserId($userId)?->isBlocked()) {
            Flash::send(Alert::ERROR, 'Forum', 'Vous ne pouvez plus faire ceci, vous êtes bloqué pour la raison : ' . $userBlocked->getUserBlockedByUserId($userId)?->getReason());
            Redirect::redirectPreviousRoute();
        }

        $iconNotRead = ForumSettingsModel::getInstance()->getOptionValue('IconNotRead');
        $iconNotReadColor = ForumSettingsModel::getInstance()->getOptionValue('IconNotReadColor');
        $iconImportant = ForumSettingsModel::getInstance()->getOptionValue('IconImportant');
        $iconImportantColor = ForumSettingsModel::getInstance()->getOptionValue('IconImportantColor');
        $iconPin = ForumSettingsModel::getInstance()->getOptionValue('IconPin');
        $iconPinColor = ForumSettingsModel::getInstance()->getOptionValue('IconPinColor');
        $iconClosed = ForumSettingsModel::getInstance()->getOptionValue('IconClosed');
        $iconClosedColor = ForumSettingsModel::getInstance()->getOptionValue('IconClosedColor');

        $view = new View('Forum', 'addTopic');
        $view->addVariableList(['forumModel' => $forumModel, 'forum' => $forum, 'iconNotRead' => $iconNotRead, 'iconImportant' => $iconImportant, 'iconPin' => $iconPin, 'iconClosed' => $iconClosed, 'category' => $category, 'iconNotReadColor' => $iconNotReadColor, 'iconImportantColor' => $iconImportantColor, 'iconPinColor' => $iconPinColor, 'iconClosedColor' => $iconClosedColor]);
        $view->addScriptBefore('Admin/Resources/Vendors/Tinymce/tinymce.min.js', 'App/Package/Forum/Views/Assets/Js/tinyConfig.js');
        $view->addStyle('Admin/Resources/Vendors/Fontawesome-free/Css/fa-all.min.css');
        $view->view();
    }

    #[Link('/c/:catSlug/f/:forumSlug/fp:forumPage/add', Link::POST, ['.*?'], '/forum')]
    private function publicForumAddTopicPost(string $catSlug, string $forumSlug, int $forumPage): void
    {
        ForumPermissionController::getInstance()->redirectIfNotHavePermissions('user_create_topic');

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
        if ($forum->disallowTopics()) {
            Flash::send(Alert::ERROR, 'Forum', "Ce forum n'autorise pas la création de nouveau topics");
            Redirect::redirectPreviousRoute();
        }
        $userId = UsersModel::getCurrentUser()?->getId();
        $userBlocked = ForumUserBlockedModel::getInstance();
        if ($userBlocked->getUserBlockedByUserId($userId)?->isBlocked()) {
            Flash::send(Alert::ERROR, 'Forum', 'Vous ne pouvez plus faire ceci, vous êtes bloqué pour la raison : ' . $userBlocked->getUserBlockedByUserId($userId)?->getReason());
            Redirect::redirectPreviousRoute();
        }

        [$name, $content, $disallowReplies, $important, $pin, $tags] = Utils::filterInput('name', 'content', 'disallow_replies', 'important', 'pin', 'tags');

        if (!ForumPermissionModel::getInstance()->hasForumPermission($userId, 'user_create_topic_tag') && $tags !== '') {
            ForumPermissionController::getInstance()->alertNotHavePermissions('user_create_topic_tag');
            $tags = '';
        }

        $forum = forumModel::getInstance()->getForumBySlug($forumSlug);

        if (is_null($forum) || Utils::containsNullValue($name, $content)) {
            Flash::send('error', LangManager::translate('core.toaster.error'),
                LangManager::translate('core.toaster.internalError'));
            Redirect::redirectPreviousRoute();
        }

        $res = ForumTopicModel::getInstance()->createTopic($name, $content, $userId, $forum->getId(),
            (is_null($disallowReplies) ? 0 : 1), (is_null($important) ? 0 : 1), (is_null($pin) ? 0 : 1));

        if (!$res) {
            Flash::send(Alert::ERROR, 'Erreur', 'Impossible de créer le topic.');
            Redirect::redirectPreviousRoute();
        }

        $followTopic = empty($_POST['followTopic']) ? 0 : 1;
        if ($followTopic === 1) {
            ForumFollowedModel::getInstance()->addFollower($res->getId(), $userId);
        }

        if (is_null($res)) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('core.toaster.internalError'));
            Redirect::redirectPreviousRoute();
        }

        // Add tags

        $tags = explode(',', $tags);

        foreach ($tags as $tag) {
            // Clean tag
            $tag = mb_strtolower(trim($tag));

            if (empty($tag)) {
                continue;
            }

            ForumTopicModel::getInstance()->addTag($tag, $res->getId());
        }

        Flash::send(Alert::ERROR, LangManager::translate('core.toaster.success'),
            LangManager::translate('forum.topic.add.success'));

        ForumDiscordModel::getInstance()->sendDiscordMsgNewTopic($forum->getId(), $name, $forum->getName(), 'test', UsersModel::getCurrentUser()?->getUserPicture()?->getImage(), UsersModel::getCurrentUser()?->getPseudo());

        header('location: ' . $forum->getLink());
    }

    #[Link('/c/:catSlug/f/:forumSlug/t/:topicSlug/p:page', Link::GET, ['.*?'], '/forum')]
    private function publicTopicView(string $catSlug, string $forumSlug, string $topicSlug, int $page): void
    {
        $visitorCanViewForum = ForumSettingsModel::getInstance()->getOptionValue('visitorCanViewForum');

        if ($visitorCanViewForum === '0') {
            ForumPermissionController::getInstance()->redirectIfNotHavePermissions('user_view_topic');
        }

        $topic = ForumTopicModel::getInstance()->getTopicBySlug($topicSlug);

        if (!$topic) {
            Redirect::errorPage(404);
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

        $responsePerPage = ForumSettingsModel::getInstance()->getOptionValue('responsePerPage');
        $offset = ($page - 1) * $responsePerPage;
        $totalPage = (string) ceil(ForumResponseModel::getInstance()->countResponseInTopic($topic->getId()) / $responsePerPage);
        preg_match('/\/p(\d+)/', $_SERVER['REQUEST_URI'], $matches);
        $currentPage = $matches[1];

        $responses = ForumResponseModel::getInstance()->getResponseByTopicAndOffset($topic->getId(), $offset, $responsePerPage);

        $forumModel = forumModel::getInstance();
        $topic = ForumTopicModel::getInstance()->getTopicBySlug($topicSlug);
        $isViewed = ForumTopicModel::getInstance()->checkViews($topic->getId(), Client::getIp());
        $currentUser = usersModel::getInstance()::getCurrentUser();

        $iconNotRead = ForumSettingsModel::getInstance()->getOptionValue('IconNotRead');
        $iconNotReadColor = ForumSettingsModel::getInstance()->getOptionValue('IconNotReadColor');
        $iconImportant = ForumSettingsModel::getInstance()->getOptionValue('IconImportant');
        $iconImportantColor = ForumSettingsModel::getInstance()->getOptionValue('IconImportantColor');
        $iconPin = ForumSettingsModel::getInstance()->getOptionValue('IconPin');
        $iconPinColor = ForumSettingsModel::getInstance()->getOptionValue('IconPinColor');
        $iconClosed = ForumSettingsModel::getInstance()->getOptionValue('IconClosed');
        $iconClosedColor = ForumSettingsModel::getInstance()->getOptionValue('IconClosedColor');
        $feedbackModel = ForumFeedbackModel::getInstance();

        if (!$isViewed) {
            ForumTopicModel::getInstance()->addViews($topic->getId());
        }

        $needConnectUrl = ForumSettingsModel::getInstance()->getOptionValue('needConnectUrl');
        $blinkResponse = ForumSettingsModel::getInstance()->getOptionValue('blinkResponse');

        $view = new View('Forum', 'topic');
        $view->addVariableList(['currentPage' => $currentPage, 'totalPage' => $totalPage, 'responses' => $responses,
            'forumModel' => $forumModel, 'currentUser' => $currentUser, 'topic' => $topic,
            'feedbackModel' => $feedbackModel, 'responseModel' => ForumResponseModel::getInstance(),
            'iconNotRead' => $iconNotRead, 'iconImportant' => $iconImportant, 'iconPin' => $iconPin,
            'iconClosed' => $iconClosed, 'forum' => $forum, 'category' => $category,
            'iconNotReadColor' => $iconNotReadColor, 'iconImportantColor' => $iconImportantColor,
            'iconPinColor' => $iconPinColor, 'iconClosedColor' => $iconClosedColor]);
        if ($needConnectUrl) {
            $view->addPhpAfter('App/Package/Forum/Views/Assets/Php/needConnect.php');
        }
        if ($blinkResponse) {
            $view->addPhpAfter('App/Package/Forum/Views/Assets/Php/blinkResponse.php');
        }
        $view->addScriptBefore('Admin/Resources/Vendors/Tinymce/tinymce.min.js',
            'App/Package/Forum/Views/Assets/Js/tinyConfig.js');
        $view->addStyle('Admin/Resources/Vendors/Fontawesome-free/Css/fa-all.min.css');
        $view->view();
    }

    #[Link('/c/:catSlug/f/:forumSlug/t/:topicSlug/p:page/edit', Link::GET, ['.*?'], '/forum')]
    private function publicTopicEdit(string $catSlug, string $forumSlug, string $topicSlug, int $page): void
    {
        if (!UsersController::isUserLogged()) {
            Flash::send(Alert::ERROR, 'Forum', 'Connectez-vous avant de modifier ce topic.');
            Redirect::redirect('login');
        }
        $userBlocked = ForumUserBlockedModel::getInstance();
        $userId = UsersModel::getCurrentUser()?->getId();
        if ($userBlocked->getUserBlockedByUserId($userId)?->isBlocked()) {
            Flash::send(
                Alert::ERROR,
                '
                Forum',
                'Vous ne pouvez plus faire ceci, vous êtes bloqué pour la raison : ' . $userBlocked->getUserBlockedByUserId($userId)?->getReason(),
            );
            Redirect::redirectPreviousRoute();
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

        $topic = ForumTopicModel::getInstance()->getTopicBySlug($topicSlug);

        if (!$topic) {
            Redirect::errorPage(404);
        }

        if (UsersModel::getCurrentUser()?->getId() !== $topic->getUser()->getId()) {
            Flash::send(Alert::ERROR, 'Erreur', "Vous n'avez pas la permission de faire ceci !");
            Redirect::redirect('forum');
        }

        $view = new View('Forum', 'editTopic');
        $view->addVariableList(['topic' => $topic, 'category' => $category, 'forum' => $forum]);
        $view->addScriptBefore('Admin/Resources/Vendors/Tinymce/tinymce.min.js', 'App/Package/Forum/Views/Assets/Js/tinyConfig.js');
        $view->addStyle('Admin/Resources/Vendors/Fontawesome-free/Css/fa-all.min.css');
        $view->view();
    }

    #[Link('/c/:catSlug/f/:forumSlug/t/:topicSlug/p:page/edit', Link::POST, ['.*?'], '/forum')]
    private function publicTopicEditPost(string $catSlug, string $forumSlug, string $topicSlug, int $page): void
    {
        $userBlocked = ForumUserBlockedModel::getInstance();
        $userId = UsersModel::getCurrentUser()?->getId();
        if ($userBlocked->getUserBlockedByUserId($userId)?->isBlocked()) {
            Flash::send(Alert::ERROR, 'Forum', 'Vous ne pouvez plus faire ceci, vous êtes bloqué pour la raison : ' . $userBlocked->getUserBlockedByUserId($userId)?->getReason());
            Redirect::redirectPreviousRoute();
        }
        [$topicId, $name, $content, $tags] = Utils::filterInput('topicId', 'name', 'content', 'tags');

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

        $topic = ForumTopicModel::getInstance()->getTopicBySlug($topicSlug);

        if (UsersModel::getCurrentUser()?->getId() !== $topic?->getUser()->getId()) {
            Flash::send(Alert::ERROR, 'Erreur', "Vous n'avez pas la permission de faire ceci !");
            Redirect::redirect('forum');
        }

        if (is_null($topic) || Utils::containsNullValue($name, $content)) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('core.toaster.internalError'));
            Website::refresh();
            return;
        }

        $res = ForumTopicModel::getInstance()->authorEditTopic($topicId, $name, $content);

        // Add tags

        $tags = explode(',', $tags);
        // Need to clear tag befor update
        ForumTopicModel::getInstance()->clearTag($topicId);
        foreach ($tags as $tag) {
            // Clean tag
            $tag = mb_strtolower(trim($tag));

            if (empty($tag)) {
                continue;
            }

            ForumTopicModel::getInstance()->addTag($tag, $topicId);
        }

        Flash::send(Alert::ERROR, LangManager::translate('core.toaster.success'),
            'Topic mis à jour');

        header("location: ../../{$topic->getSlug()}/p1");
    }

    #[NoReturn]
    #[Link('/c/:catSlug/f/:forumSlug/t/:topicSlug/p:page/react/:topicId/:feedbackId', Link::GET, ['.*?'], '/forum')]
    private function publicTopicAddFeedback(string $catSlug, string $forumSlug, string $topicSlug, int $page, int $topicId, int $feedbackId): void
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
        $userBlocked = ForumUserBlockedModel::getInstance();
        $userId = UsersModel::getCurrentUser()?->getId();
        if ($userBlocked->getUserBlockedByUserId($userId)?->isBlocked()) {
            Flash::send(Alert::ERROR, 'Forum', 'Vous ne pouvez plus faire ceci, vous êtes bloqué pour la raison : ' . $userBlocked->getUserBlockedByUserId($userId)?->getReason());
            Redirect::redirectPreviousRoute();
        }
        ForumPermissionController::getInstance()->redirectIfNotHavePermissions('user_react_topic');

        $user = usersModel::getInstance()::getCurrentUser();
        ForumFeedbackModel::getInstance()->addFeedbackByFeedbackId($topicId, $feedbackId, $user?->getId());

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/c/:catSlug/f/:forumSlug/t/:topicSlug/p:page/un_react/:topicId/:feedbackId', Link::GET, ['.*?'], '/forum')]
    private function publicTopicDeleteFeedback(string $catSlug, string $forumSlug, string $topicSlug, int $page, int $topicId, int $feedbackId): void
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

        ForumPermissionController::getInstance()->redirectIfNotHavePermissions('user_remove_react_topic');
        $userBlocked = ForumUserBlockedModel::getInstance();
        $userId = UsersModel::getCurrentUser()?->getId();
        if ($userBlocked->getUserBlockedByUserId($userId)?->isBlocked()) {
            Flash::send(Alert::ERROR, 'Forum', 'Vous ne pouvez plus faire ceci, vous êtes bloqué pour la raison : ' . $userBlocked->getUserBlockedByUserId($userId)?->getReason());
            Redirect::redirectPreviousRoute();
        }
        $user = usersModel::getInstance()::getCurrentUser();
        ForumFeedbackModel::getInstance()->removeFeedbackByFeedbackId($topicId, $feedbackId, $user?->getId());

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/c/:catSlug/f/:forumSlug/t/:topicSlug/p:page/change_react/:topicId/:feedbackId', Link::GET, ['.*?'], '/forum')]
    private function publicTopicChangeFeedback(string $catSlug, string $forumSlug, string $topicSlug, int $page, int $topicId, int $feedbackId): void
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

        ForumPermissionController::getInstance()->redirectIfNotHavePermissions('user_change_react_topic');
        $userBlocked = ForumUserBlockedModel::getInstance();
        $userId = UsersModel::getCurrentUser()?->getId();
        if ($userBlocked->getUserBlockedByUserId($userId)?->isBlocked()) {
            Flash::send(Alert::ERROR, 'Forum', 'Vous ne pouvez plus faire ceci, vous êtes bloqué pour la raison : ' . $userBlocked->getUserBlockedByUserId($userId)?->getReason());
            Redirect::redirectPreviousRoute();
        }
        $user = usersModel::getInstance()::getCurrentUser();
        ForumFeedbackModel::getInstance()->changeFeedbackByFeedbackId($topicId, $feedbackId, $user?->getId());

        Redirect::redirectPreviousRoute();
    }
}
