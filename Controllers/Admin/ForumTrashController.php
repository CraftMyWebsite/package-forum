<?php
namespace CMW\Controller\Forum\Admin;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Requests\Request;
use CMW\Model\Forum\ForumCategoryModel;
use CMW\Model\Forum\ForumModel;
use CMW\Model\Forum\ForumResponseModel;
use CMW\Model\Forum\ForumTopicModel;
use CMW\Manager\Views\View;
use CMW\Manager\Router\Link;
use CMW\Manager\Flash\Flash;

/**
 * Class: @ForumTrashController
 * @package Forum
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ForumTrashController extends AbstractController
{
    #[Link("/trash", Link::GET, [], "/cmw-admin/forum")]
    public function adminListTrashView(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.trash");

        View::createAdminView("Forum", "trash")
            ->addVariableList(["forumModel" => forumModel::getInstance(), "categoryModel" => ForumCategoryModel::getInstance(), "responseModel" => ForumResponseModel::getInstance(), "topicModel" => ForumTopicModel::getInstance()])
            ->addStyle("Admin/Resources/Assets/Css/simple-datatables.css")
            ->addScriptAfter("Admin/Resources/Vendors/Simple-datatables/Umd/simple-datatables.js",
                "Admin/Resources/Vendors/Simple-datatables/config-datatables.js")
            ->view();
    }

    #[Link("/trash/deletereply/:replyId", Link::GET, [], "/cmw-admin/forum")]
    public function publicReplyDelete(Request $request, int $replyId): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.trash");

        if (ForumResponseModel::getInstance()->deleteResponse($replyId)) {

            Flash::send("success", LangManager::translate("core.toaster.success"),
                LangManager::translate("forum.reply.delete.success"));

            header("location: ..");
        }
    }

    #[Link("/trash/restorereply/:replyId/:topicId", Link::GET, [], "/cmw-admin/forum")]
    public function publicReplyRestore(Request $request, int $replyId, int $topicId): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.trash");

        if (ForumTopicModel::getInstance()->isTrashedTopic($topicId) == 1) {
            Flash::send("error", LangManager::translate("core.toaster.error"), "Le topic de cette réponse est actuellement en corbeille !");
            header("location: ../..");
        } else {
            if (ForumResponseModel::getInstance()->restoreResponse($replyId)) {
                Flash::send("success", LangManager::translate("core.toaster.success"), LangManager::translate("forum.reply.delete.success"));
                header("location: ../..");
            }
        }  
    }

    #[Link("/trash/deletetopic/:topicId", Link::GET, [], "/cmw-admin/forum")]
    public function publicTopicDelete(Request $request, int $topicId): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.trash");

        if (ForumTopicModel::getInstance()->deleteTopic($topicId)) {

            Flash::send("success", LangManager::translate("core.toaster.success"), "Tu as complétement virer le truc et toutes ces réponse");

            header("location: ..");
        }
    }

    #[Link("/trash/restoretopic/:topicId", Link::GET, [], "/cmw-admin/forum")]
    public function publicTopicRestore(Request $request, int $topicId): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.trash");

        if (ForumTopicModel::getInstance()->restoreTopic($topicId)) {

            Flash::send("success", LangManager::translate("core.toaster.success"), "Tu as remis le truc");

            header("location: ..");
        }
    }

}