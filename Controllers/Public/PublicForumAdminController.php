<?php

namespace CMW\Controller\Forum\Public;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Model\Forum\ForumCategoryModel;
use CMW\Model\Forum\ForumModel;
use CMW\Model\Forum\ForumTopicModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class: @PublicForumAdminController
 * @package Forum
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class PublicForumAdminController extends AbstractController
{
    /*
     * POST METHOD
     */
    #[NoReturn]
    #[Link('/c/:catSlug/f/:forumSlug/fp:forumPage/adminedit', Link::POST, ['.*?'], '/forum')]
    private function publicForumAdminEditTopicPost(string $catSlug, string $forumSlug, int $forumPage): void
    {
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

        if (UsersController::isAdminLogged()) {
            [$topicId, $name, $disallowReplies, $important, $pin, $tags, $prefix, $move] = Utils::filterInput('topicId', 'name', 'disallow_replies', 'important', 'pin', 'tags', 'prefix', 'move');

            $forum = forumModel::getInstance()->getForumBySlug($forumSlug);

            if (is_null($forum)) {
                Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                    LangManager::translate('core.toaster.internalError'));
                Redirect::redirectPreviousRoute();
            }

            ForumTopicModel::getInstance()->adminEditTopic($topicId, $name, (is_null($disallowReplies) ? 0 : 1), (is_null($important) ? 0 : 1), (is_null($pin) ? 0 : 1), $prefix, $move);

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

            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.success'), "Le topic viens d'être éditer");

            Redirect::redirectPreviousRoute();
        } else {
            Flash::send(Alert::ERROR, 'Erreur', "Vous n'êtes pas autoriser à faire ceci !");
            Redirect::redirect('forum');
        }
    }

    /*
     * DIRECT LINK :
     */

    #[Link('/c/:catSlug/f/:forumSlug/t/:topicSlug/p:page/pinned', Link::GET, ['.*?'], '/forum')]
    private function publicTopicPinned(string $catSlug, string $forumSlug, string $topicSlug, int $page): void
    {
        if (UsersController::isAdminLogged()) {
            $topic = ForumTopicModel::getInstance()->getTopicBySlug($topicSlug);
            if (is_null($topic)) {
                Flash::send('error', LangManager::translate('core.toaster.error'),
                    LangManager::translate('core.toaster.internalError'));
                return;
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

            if (ForumTopicModel::getInstance()->pinTopic($topic)) {
                Flash::send(Alert::ERROR, LangManager::translate('core.toaster.success'),
                    $topic->isPinned()
                        ? LangManager::translate('forum.topic.unpinned.success')
                        : LangManager::translate('forum.topic.pinned.success'));

                header("location: ../../f/{$topic->getForum()->getSlug()}");
            }
        } else {
            Flash::send(Alert::ERROR, 'Erreur', "Vous n'avez pas la permission de faire ceci !");
            Redirect::redirect('forum');
        }
    }

    #[Link('/c/:catSlug/f/:forumSlug/t/:topicSlug/p:page/disallowreplies', Link::GET, ['.*?'], '/forum')]
    private function publicTopicDisallowReplies(string $catSlug, string $forumSlug, string $topicSlug, int $page): void
    {
        if (UsersController::isAdminLogged()) {
            $topic = ForumTopicModel::getInstance()->getTopicBySlug($topicSlug);
            if (is_null($topic)) {
                Flash::send('error', LangManager::translate('core.toaster.error'),
                    LangManager::translate('core.toaster.internalError'));
                return;
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

            if (ForumTopicModel::getInstance()->DisallowReplies($topic)) {
                Flash::send('success', LangManager::translate('core.toaster.success'),
                    $topic->isPinned()
                        ? LangManager::translate('forum.topic.unpinned.success')
                        : LangManager::translate('forum.topic.pinned.success'));

                header("location: ../../f/{$topic->getForum()->getSlug()}");
            }
        } else {
            Flash::send(Alert::ERROR, 'Erreur', "Vous n'avez pas la permission de faire ceci !");
            Redirect::redirect('forum');
        }
    }

    #[Link('/c/:catSlug/f/:forumSlug/t/:topicSlug/p:page/isimportant', Link::GET, ['.*?'], '/forum')]
    private function publicTopicIsImportant(string $catSlug, string $forumSlug, string $topicSlug, int $page): void
    {
        if (UsersController::isAdminLogged()) {
            $topic = ForumTopicModel::getInstance()->getTopicBySlug($topicSlug);
            if (is_null($topic)) {
                Flash::send('error', LangManager::translate('core.toaster.error'),
                    LangManager::translate('core.toaster.internalError'));
                return;
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

            if (ForumTopicModel::getInstance()->ImportantTopic($topic)) {
                Flash::send('success', LangManager::translate('core.toaster.success'),
                    $topic->isPinned()
                        ? LangManager::translate('forum.topic.unpinned.success')
                        : LangManager::translate('forum.topic.pinned.success'));

                header("location: ../../f/{$topic->getForum()->getSlug()}");
            }
        } else {
            Flash::send(Alert::ERROR, 'Erreur', "Vous n'avez pas la permission de faire ceci !");
            Redirect::redirect('forum');
        }
    }

    #[Link('/c/:catSlug/f/:forumSlug/t/:topicSlug/p:page/trash', Link::GET, ['.*?'], '/forum')]
    private function publicTopicIsTrash(string $catSlug, string $forumSlug, string $topicSlug, int $page): void
    {
        if (UsersController::isAdminLogged()) {
            $topic = ForumTopicModel::getInstance()->getTopicBySlug($topicSlug);
            if (is_null($topic)) {
                Flash::send('error', LangManager::translate('core.toaster.error'),
                    LangManager::translate('core.toaster.internalError'));
                return;
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

            if (ForumTopicModel::getInstance()->trashTopic($topic)) {
                Flash::send(Alert::ERROR, LangManager::translate('core.toaster.success'), 'Topic mis à la poubelle !');

                Redirect::redirectPreviousRoute();
            }
        } else {
            Flash::send(Alert::ERROR, 'Erreur', "Vous n'avez pas la permission de faire ceci !");
            Redirect::redirect('forum');
        }
    }
}
