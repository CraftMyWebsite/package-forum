<?php


namespace CMW\Controller\Forum;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Requests\Request;
use CMW\Model\Forum\CategoryModel;
use CMW\Model\Forum\ForumModel;
use CMW\Model\Forum\ResponseModel;
use CMW\Model\Forum\TopicModel;
use CMW\Manager\Views\View;
use CMW\Manager\Router\Link;
use CMW\Manager\Flash\Flash;

class TrashController extends AbstractController
{
    #[Link("/trash", Link::GET, [], "/cmw-admin/forum")]
    public function adminListTrashView(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.categories.list");

        View::createAdminView("Forum", "trash")
            ->addVariableList(["forumModel" => forumModel::getInstance(), "categoryModel" => categoryModel::getInstance(), "responseModel" => responseModel::getInstance(), "topicModel" => topicModel::getInstance()])
            ->addStyle("Admin/Resources/Vendors/Simple-datatables/style.css","Admin/Resources/Assets/Css/Pages/simple-datatables.css")
            ->addScriptAfter("Admin/Resources/Vendors/Simple-datatables/Umd/simple-datatables.js","Admin/Resources/Assets/Js/Pages/simple-datatables.js")
            ->view();
    }

    #[Link("/trash/deletereply/:replyId", Link::GET, [], "/cmw-admin/forum")]
    public function publicReplyDelete(Request $request, int $replyId): void
    {

        if (responseModel::getInstance()->deleteResponse($replyId)) {

            Flash::send("success", LangManager::translate("core.toaster.success"),
                LangManager::translate("forum.reply.delete.success"));

            header("location: ..");
        }
    }

    #[Link("/trash/restorereply/:replyId/:topicId", Link::GET, [], "/cmw-admin/forum")]
    public function publicReplyRestore(Request $request, int $replyId, int $topicId): void
    {
        if (topicModel::getInstance()->isTrashedTopic($topicId) == 1) {
            Flash::send("error", LangManager::translate("core.toaster.error"), "Le topic de cette réponse est actuellement en corbeille !");
            header("location: ../..");
        } else {
            if (responseModel::getInstance()->restoreResponse($replyId)) {
                Flash::send("success", LangManager::translate("core.toaster.success"), LangManager::translate("forum.reply.delete.success"));
                header("location: ../..");
            }
        }  
    }

    #[Link("/trash/deletetopic/:topicId", Link::GET, [], "/cmw-admin/forum")]
    public function publicTopicDelete(Request $request, int $topicId): void
    {

        if (topicModel::getInstance()->deleteTopic($topicId)) {

            Flash::send("success", LangManager::translate("core.toaster.success"), "Tu as complétement virer le truc et toutes ces réponse");

            header("location: ..");
        }
    }

    #[Link("/trash/restoretopic/:topicId", Link::GET, [], "/cmw-admin/forum")]
    public function publicTopicRestore(Request $request, int $topicId): void
    {

        if (topicModel::getInstance()->restoreTopic($topicId)) {

            Flash::send("success", LangManager::translate("core.toaster.success"), "Tu as remis le truc");

            header("location: ..");
        }
    }

}