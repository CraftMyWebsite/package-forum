<?php
namespace CMW\Controller\Forum;

use CMW\Controller\Core\CoreController;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Requests\Request;
use CMW\Manager\Router\Link;
use CMW\Model\Forum\ForumCategoryModel;
use CMW\Model\Forum\ForumModel;
use CMW\Model\Forum\ForumResponseModel;
use CMW\Model\Forum\ForumTopicModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Redirect;


/**
 * Class: @PublicForumTrashController
 * @package Forum
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class PublicForumTrashController extends CoreController
{
    #[Link("/c/:catSlug/f/:forumSlug/t/:topicSlug/p:page/trash/:replyId/:reason", Link::GET, ['.*?' => 'topicSlug', '[0-9]+' => 'replyId'], "/forum")]
    public function publicTopicReplyDelete(Request $request, string $catSlug, string $forumSlug, string $topicSlug,int $page, int $replyId, int $reason): void
    {
        $topic = ForumTopicModel::getInstance()->getTopicBySlug($topicSlug);

        if (UsersModel::getCurrentUser()->getId() !== $topic->getUser()->getId()) {
            Flash::send(Alert::ERROR, "Erreur", "Vous n'avez pas la permission de faire ceci !");
            Redirect::redirect("forum");
        }

        if (is_null($topic)) {
            Flash::send("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.internalError"));
            return;
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

        $reply = ForumResponseModel::getInstance()->getResponseById($replyId);

        if (!$reply?->isSelfReply()) {//Rajouter ici si on as la permission de supprimer (staff)
            Flash::send("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("forum.reply.delete.errors.no_access"));
            return;
        }

        if (ForumResponseModel::getInstance()->trashResponse($replyId, $reason)) {

            Flash::send("success", LangManager::translate("core.toaster.success"),
                LangManager::translate("forum.reply.delete.success"));

            header("Location: ". EnvManager::getInstance()->getValue("PATH_SUBFOLDER")."forum/c/$catSlug/f/$forumSlug/t/$topicSlug/p1/");
        }
    }
}