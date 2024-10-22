<?php
namespace CMW\Controller\Forum\Admin;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Package\AbstractController;
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
 * @author Zomb
 * @version 0.0.1
 */
class ForumReportController extends AbstractController
{
    #[Link('/report', Link::GET, [], '/cmw-admin/forum')]
    private function adminReportView(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'forum.report');

        $reportModel = ForumReportedModel::getInstance();

        View::createAdminView('Forum', 'report')
            ->addVariableList(['reportModel' => $reportModel])
            ->addStyle('Admin/Resources/Assets/Css/simple-datatables.css')
            ->addScriptAfter('Admin/Resources/Vendors/Simple-datatables/simple-datatables.js',
                'Admin/Resources/Vendors/Simple-datatables/config-datatables.js')
            ->view();
    }

    #[NoReturn]
    #[Link('/report/unReportTopic/:topicId', Link::GET, ['.*?'], '/cmw-admin/forum')]
    private function adminUnReportTopic(int $topicId): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'forum.report');

        ForumReportedModel::getInstance()->removeReportTopic($topicId);

        Flash::send(Alert::SUCCESS, 'Forum', 'Ce signalement est traité !');

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/report/unReportResponse/:responseId', Link::GET, ['.*?'], '/cmw-admin/forum')]
    private function adminUnReportResponse(int $responseId): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'forum.report');

        ForumReportedModel::getInstance()->removeReportResponse($responseId);

        Flash::send(Alert::SUCCESS, 'Forum', 'Ce signalement est traité !');

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/report/removeTopic/:topicId', Link::GET, ['.*?'], '/cmw-admin/forum')]
    private function adminRemoveTopic(int $topicId): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'forum.report');

        ForumTopicModel::getInstance()->deleteTopic($topicId);

        Flash::send(Alert::SUCCESS, 'Forum', 'Ce topic est supprimé !');

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/report/removeResponse/:responseId', Link::GET, ['.*?'], '/cmw-admin/forum')]
    private function adminRemoveResponse(int $responseId): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'forum.report');

        ForumResponseModel::getInstance()->deleteResponse($responseId);

        Flash::send(Alert::SUCCESS, 'Forum', 'La réponse est supprimé !');

        Redirect::redirectPreviousRoute();
    }
}
