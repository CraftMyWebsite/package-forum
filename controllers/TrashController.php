<?php


namespace CMW\Controller\Forum;

use CMW\Controller\Core\CoreController;
use CMW\Controller\Users\UsersController;
use CMW\Manager\Lang\LangManager;
use CMW\Model\Forum\CategoryModel;
use CMW\Model\Forum\ForumModel;
use CMW\Model\Forum\ResponseModel;
use CMW\Model\Forum\TopicModel;
use CMW\Manager\Views\View;
use CMW\Router\Link;
use CMW\Utils\Response;
use CMW\Utils\Utils;

class TrashController extends CoreController
{
	private ForumModel $forumModel;
    private CategoryModel $categoryModel;
    private ResponseModel $responseModel;
    private TopicModel $topicModel;

    public function __construct()
    {
        parent::__construct();
        $this->forumModel = new ForumModel();
        $this->categoryModel = new CategoryModel();
        $this->responseModel = new ResponseModel();
        $this->topicModel = new TopicModel();
    }

    #[Link("/trash", Link::GET, [], "/cmw-admin/forum")]
    public function adminListTrashView(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.categories.list");

        View::createAdminView("forum", "trash")
            ->addVariableList(["forumModel" => $this->forumModel, "categoryModel" => $this->categoryModel, "responseModel" => $this->responseModel, "topicModel" => $this->topicModel])
            ->addStyle("admin/resources/vendors/simple-datatables/style.css","admin/resources/assets/css/pages/simple-datatables.css")
            ->addScriptAfter("admin/resources/vendors/simple-datatables/umd/simple-datatables.js","admin/resources/assets/js/pages/simple-datatables.js")
            ->view();
    }

    #[Link("/trash/deletereply/:replyId", Link::GET, [], "/cmw-admin/forum")]
    public function publicReplyDelete( int $replyId): void
    {

        if ($this->responseModel->deleteResponse($replyId)) {

            Response::sendAlert("success", LangManager::translate("core.toaster.success"),
                LangManager::translate("forum.reply.delete.success"));

            header("location: ..");
        }
    }

    #[Link("/trash/restorereply/:replyId", Link::GET, [], "/cmw-admin/forum")]
    public function publicReplyRestore( int $replyId): void
    {

        if ($this->responseModel->restoreResponse($replyId)) {

            Response::sendAlert("success", LangManager::translate("core.toaster.success"),
                LangManager::translate("forum.reply.delete.success"));

            header("location: ..");
        }
    }

}