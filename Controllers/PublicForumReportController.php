<?php
namespace CMW\Controller\Forum;


use CMW\Controller\Users\UsersController;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Requests\Request;
use CMW\Manager\Router\Link;
use CMW\Model\Forum\ForumCategoryModel;
use CMW\Model\Forum\ForumModel;
use CMW\Model\Forum\ForumReportedModel;
use CMW\Model\Forum\ForumSettingsModel;
use CMW\Model\Forum\ForumTopicModel;
use CMW\Model\Forum\ForumUserBlockedModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use CMW\Utils\Website;

/**
 * Class: @ForumSettingsController
 * @package Forum
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @desc You can create "reportTopic.view.php" or "reportResponse.view.php" in Theme/Views/Forum/** or directly use à modal and call action"" form > POST
 * @version 1.0
 */
class PublicForumReportController extends AbstractController {

    #[Link("/c/:catSlug/f/:forumSlug/t/:topicSlug/reportTopic/:topicId", Link::GET, ['.*?'], "/forum")]
    public function publicReportTopic(Request $request, string $catSlug, string $forumSlug, string $topicSlug, int $topicId): void
    {
        $visitorCanViewForum = ForumSettingsModel::getInstance()->getOptionValue("visitorCanViewForum");

        if ($visitorCanViewForum === "0") {
            ForumPermissionController::getInstance()->redirectIfNotHavePermissions("user_view_forum");
        }

        $category = ForumCategoryModel::getInstance()->getCatBySlug($catSlug);
        $forum = forumModel::getInstance()->getForumBySlug($forumSlug);
        $topic = ForumTopicModel::getInstance()->getTopicBySlug($topicSlug);

        $view = new View("Forum", "reportTopic");
        $view->addVariableList(["category" => $category, "forum" => $forum, "topic" => $topic, "topicId" => $topicId]);
        $view->addStyle("Admin/Resources/Vendors/Fontawesome-free/Css/fa-all.min.css");
        $view->view();
    }
    #[Link("/c/:catSlug/f/:forumSlug/t/:topicSlug/reportResponse/:responseId", Link::GET, ['.*?'], "/forum")]
    public function publicReportResponse(Request $request, string $catSlug, string $forumSlug, string $topicSlug, int $responseId): void
    {
        $visitorCanViewForum = ForumSettingsModel::getInstance()->getOptionValue("visitorCanViewForum");

        if ($visitorCanViewForum === "0") {
            ForumPermissionController::getInstance()->redirectIfNotHavePermissions("user_view_forum");
        }

        $category = ForumCategoryModel::getInstance()->getCatBySlug($catSlug);
        $forum = forumModel::getInstance()->getForumBySlug($forumSlug);
        $topic = ForumTopicModel::getInstance()->getTopicBySlug($topicSlug);

        $view = new View("Forum", "reportResponse");
        $view->addVariableList(["category" => $category, "forum" => $forum, "topic" => $topic, "topicId" => $responseId]);
        $view->addStyle("Admin/Resources/Vendors/Fontawesome-free/Css/fa-all.min.css");
        $view->view();
    }

    #[Link("/c/:catSlug/f/:forumSlug/t/:topicSlug/reportTopic/:topicId", Link::POST, ['.*?'], "/forum")]
    public function publicReportTopicPost(Request $request, string $catSlug, string $forumSlug, string $topicSlug, int $topicId): void
    {
        if (!UsersController::isUserLogged()) {
            Flash::send(Alert::ERROR, "Forum", "Connectez-vous avant de signaler ce topic.");
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


        $topic = ForumTopicModel::getInstance()->getTopicBySlug($topicSlug);

        if (!$topic) {
            Flash::send("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.internalError"));
            Website::refresh();
            return;
        }

        [$reason] = Utils::filterInput("reason");

        ForumReportedModel::getInstance()->creatTopicReport($userId,$topicId,$reason);

        Flash::send("success", "Forum",
            "Votre signalement est pris en compte et sera éxaminer");
        Redirect::redirectPreviousRoute();
    }

    #[Link("/c/:catSlug/f/:forumSlug/t/:topicSlug/reportResponse/:responseId", Link::POST, ['.*?'], "/forum")]
    public function publicReportResponsePost(Request $request, string $catSlug, string $forumSlug, string $topicSlug, int $responseId): void
    {
        if (!UsersController::isUserLogged()) {
            Flash::send(Alert::ERROR, "Forum", "Connectez-vous avant de signaler ce topic.");
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


        $topic = ForumTopicModel::getInstance()->getTopicBySlug($topicSlug);

        if (!$topic) {
            Flash::send("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.internalError"));
            Website::refresh();
            return;
        }

        [$reason] = Utils::filterInput("reason");

        ForumReportedModel::getInstance()->creatResponseReport($userId,$responseId,$reason);

        Flash::send("success", "Forum",
            "Votre signalement est pris en compte et sera éxaminer");
        Redirect::redirectPreviousRoute();
    }
}