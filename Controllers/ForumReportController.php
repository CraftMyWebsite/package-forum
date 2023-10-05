<?php
namespace CMW\Controller\Forum;


use CMW\Controller\Users\UsersController;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Requests\Request;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Forum\ForumReportedModel;
use CMW\Model\Forum\ForumResponseModel;
use CMW\Model\Forum\ForumTopicModel;
use CMW\Utils\Redirect;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class: @ForumSettingsController
 * @package Forum
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ForumReportController extends AbstractController {
    #[Link("/report", Link::GET, [], "/cmw-admin/forum")]
    public function adminReportView(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.report.list");

        $reportModel = ForumReportedModel::getInstance();

        View::createAdminView("Forum", "report")
            ->addVariableList(["reportModel" => $reportModel])
            ->addStyle("Admin/Resources/Vendors/Simple-datatables/style.css","Admin/Resources/Assets/Css/Pages/simple-datatables.css")
            ->addScriptAfter("Admin/Resources/Vendors/Simple-datatables/Umd/simple-datatables.js","Admin/Resources/Assets/Js/Pages/simple-datatables.js")
            ->view();
    }

    #[NoReturn] #[Link("/report/unReportTopic/:topicId", Link::GET, ['.*?'], "/cmw-admin/forum")]
    public function adminUnReportTopic(Request $request, int $topicId): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.report.list");

        ForumReportedModel::getInstance()->removeReportTopic($topicId);

        Flash::send(Alert::SUCCESS, "Forum", "Ce signalement est traité !");

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link("/report/unReportResponse/:responseId", Link::GET, ['.*?'], "/cmw-admin/forum")]
    public function adminUnReportResponse(Request $request, int $responseId): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.report.list");

        ForumReportedModel::getInstance()->removeReportResponse($responseId);

        Flash::send(Alert::SUCCESS, "Forum", "Ce signalement est traité !");

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link("/report/removeTopic/:topicId", Link::GET, ['.*?'], "/cmw-admin/forum")]
    public function adminRemoveTopic(Request $request, int $topicId): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.report.list");

        ForumTopicModel::getInstance()->deleteTopic($topicId);

        Flash::send(Alert::SUCCESS, "Forum", "Ce topic est supprimé !");

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link("/report/removeResponse/:responseId", Link::GET, ['.*?'], "/cmw-admin/forum")]
    public function adminRemoveResponse(Request $request, int $responseId): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.report.list");

        ForumResponseModel::getInstance()->deleteResponse($responseId);

        Flash::send(Alert::SUCCESS, "Forum", "La réponse est supprimé !");

        Redirect::redirectPreviousRoute();
    }
}