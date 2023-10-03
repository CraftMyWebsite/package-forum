<?php
namespace CMW\Controller\Forum;

use CMW\Controller\Core\CoreController;
use CMW\Controller\Users\UsersController;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Requests\Request;
use CMW\Manager\Router\Link;
use CMW\Model\Forum\ForumCategoryModel;
use CMW\Model\Forum\ForumModel;
use CMW\Model\Forum\ForumTopicModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use CMW\Utils\Website;


/**
 * Class: @PublicForumAdminController
 * @package Forum
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class PublicForumAdminController extends CoreController
{

    /*
     * POST METHOD
     * */
    #[Link("/c/:catSlug/f/:forumSlug/adminedit", Link::POST, ['.*?'], "/forum")]
    public function publicForumAdminEditTopicPost(Request $request, string $catSlug, string $forumSlug): void
    {

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

        if (UsersController::isAdminLogged()) {

            [$topicId, $name, $disallowReplies, $important, $pin, $tags, $prefix, $move] = Utils::filterInput('topicId', 'name', 'disallow_replies', 'important', 'pin', 'tags', 'prefix', 'move');

            $forum = forumModel::getInstance()->getForumBySlug($forumSlug);

            if (is_null($forum)) {
                Flash::send("error", LangManager::translate("core.toaster.error"),
                    LangManager::translate("core.toaster.internalError"));
                Website::refresh();
                return;
            }

            ForumTopicModel::getInstance()->adminEditTopic($topicId, $name, (is_null($disallowReplies) ? 0 : 1), (is_null($important) ? 0 : 1), (is_null($pin) ? 0 : 1), $prefix, $move);

            // Add tags


            $tags = explode(",", $tags);
            //Need to clear tag befor update
            ForumTopicModel::getInstance()->clearTag($topicId);
            foreach ($tags as $tag) {
                //Clean tag
                $tag = mb_strtolower(trim($tag));

                if (empty($tag)) {
                    continue;
                }

                ForumTopicModel::getInstance()->addTag($tag, $topicId);
            }

            //Flash::send("success", LangManager::translate("core.toaster.success"),LangManager::translate("forum.topic.add.success"));

            header("location: ../$forumSlug");
        } else {
            Flash::send(Alert::ERROR, "Erreur", "Vous n'êtes pas autoriser à faire ceci !");
            Redirect::redirect("forum");
        }
    }

    /*
     * DIRECT LINK :
     * */

    #[Link("/c/:catSlug/f/:forumSlug/t/:topicSlug/pinned", Link::GET, ['.*?'], "/forum")]
    public function publicTopicPinned(Request $request, string $catSlug, string $forumSlug, string $topicSlug): void
    {
        if (UsersController::isAdminLogged()) {
            $topic = ForumTopicModel::getInstance()->getTopicBySlug($topicSlug);
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

            if (ForumTopicModel::getInstance()->pinTopic($topic)) {

                Flash::send("success", LangManager::translate("core.toaster.success"),
                    $topic->isPinned() ?
                        LangManager::translate("forum.topic.unpinned.success") :
                        LangManager::translate("forum.topic.pinned.success"));

                header("location: ../../f/{$topic->getForum()->getSlug()}");
            }
        } else {
            Flash::send(Alert::ERROR, "Erreur", "Vous n'avez pas la permission de faire ceci !");
            Redirect::redirect("forum");
        }
    }

    #[Link("/c/:catSlug/f/:forumSlug/t/:topicSlug/disallowreplies", Link::GET, ['.*?'], "/forum")]
    public function publicTopicDisallowReplies(Request $request, string $catSlug, string $forumSlug, string $topicSlug): void
    {
        if (UsersController::isAdminLogged()) {
            $topic = ForumTopicModel::getInstance()->getTopicBySlug($topicSlug);
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

            if (ForumTopicModel::getInstance()->DisallowReplies($topic)) {

                Flash::send("success", LangManager::translate("core.toaster.success"),
                    $topic->isPinned() ?
                        LangManager::translate("forum.topic.unpinned.success") :
                        LangManager::translate("forum.topic.pinned.success"));

                header("location: ../../f/{$topic->getForum()->getSlug()}");
            }
        } else {
            Flash::send(Alert::ERROR, "Erreur", "Vous n'avez pas la permission de faire ceci !");
            Redirect::redirect("forum");
        }
    }

    #[Link("/c/:catSlug/f/:forumSlug/t/:topicSlug/isimportant", Link::GET, ['.*?'], "/forum")]
    public function publicTopicIsImportant(Request $request, string $catSlug, string $forumSlug, string $topicSlug): void
    {
        if (UsersController::isAdminLogged()) {
            $topic = ForumTopicModel::getInstance()->getTopicBySlug($topicSlug);
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

            if (ForumTopicModel::getInstance()->ImportantTopic($topic)) {

                Flash::send("success", LangManager::translate("core.toaster.success"),
                    $topic->isPinned() ?
                        LangManager::translate("forum.topic.unpinned.success") :
                        LangManager::translate("forum.topic.pinned.success"));

                header("location: ../../f/{$topic->getForum()->getSlug()}");
            }
        } else {
            Flash::send(Alert::ERROR, "Erreur", "Vous n'avez pas la permission de faire ceci !");
            Redirect::redirect("forum");
        }
    }

    #[Link("/c/:catSlug/f/:forumSlug/t/:topicSlug/trash", Link::GET, ['.*?'], "/forum")]
    public function publicTopicIsTrash(Request $request, string $catSlug, string $forumSlug, string $topicSlug): void
    {
        if (UsersController::isAdminLogged()) {
            $topic = ForumTopicModel::getInstance()->getTopicBySlug($topicSlug);
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

            if (ForumTopicModel::getInstance()->trashTopic($topic)) {

                Flash::send("success", LangManager::translate("core.toaster.success"), "Topic mis à la poubelle !");

                header("location: ../../");
            }
        } else {
            Flash::send(Alert::ERROR, "Erreur", "Vous n'avez pas la permission de faire ceci !");
            Redirect::redirect("forum");
        }
    }
}