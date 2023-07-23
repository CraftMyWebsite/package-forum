<?php


namespace CMW\Controller\Forum;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Requests\Request;
use CMW\Model\Forum\ForumModel;
use CMW\Manager\Router\Link;
use CMW\Manager\Flash\Flash;
use CMW\Model\Forum\ForumPermissionModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use CMW\Utils\Website;

/**
 * Class: @ForumController
 * @package Forum
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ForumController extends AbstractController
{

    #[Link("/add", Link::POST, [], "/cmw-admin/forum/forums")]
    public function adminAddForumPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.add");

        [$name, $icon, $description, $categoryId] = Utils::filterInput("name", "icon", "description", "category_id");

        forumModel::getInstance()->createForum($name, $icon, $description, $categoryId);

        Flash::send("success", LangManager::translate("core.toaster.success"),
            LangManager::translate("forum.forum.add.toaster.success"));

        header("location: ../manage");
    }

    #[Link("/add", Link::POST, [], "/cmw-admin/forum/subforums")]
    public function adminAddSubForumPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.add");

        [$name, $icon, $description, $forumId] = Utils::filterInput("name", "icon", "description", "forum_id");

        forumModel::getInstance()->createSubForum($name, $icon, $description, $forumId);

        Flash::send("success", LangManager::translate("core.toaster.success"),
            LangManager::translate("forum.forum.add.toaster.success"));

        header("location: ../manage");
    }

    #[Link("/edit/:id", Link::POST, ['[0-9]+'], "/cmw-admin/forum/forums")]
    public function adminEditCategory(Request $request, int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.categories.delete");

        if (Utils::isValuesEmpty($_POST, "name", "description")) {
            Flash::send("error", LangManager::translate("core.toaster.error"), "ça va pas du tout !");
            Website::refresh();
            return;
        }

        [$name, $icon, $description, $category_id] = Utils::filterInput("name", "icon", "description", "category_id");

        forumModel::getInstance()->editForum($id, $name, $icon, $description, $category_id);

        header("location: ../../manage");
    }

    #[Link("/delete/:id", Link::GET, ['[0-9]+'], "/cmw-admin/forum/forums")]
    public function adminDeleteForum(Request $request, int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.delete");

        $forum = forumModel::getInstance()->getForumById($id);

        if (is_null($forum)) {
            Flash::send("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.internalError"));

            header("location: ../../manage/");
            return;
        }

        forumModel::getInstance()->deleteForum($id);

        Flash::send("success", LangManager::translate("core.toaster.success"),
            LangManager::translate("forum.forum.delete.success"));

        header("location: ../../manage/");
    }

    /*--------PERM UTILS--------*/
    public function redirectIfNotHavePermissions(string $permCode): void
    {
        if (!UsersController::isUserLogged()) {
            Flash::send(Alert::ERROR, "Forum", "L'accès au forum est reservé au membre, merci de vous connectez avant d'allez plus loin.");
            Redirect::redirect('login');
        }

        $userId = UsersModel::getCurrentUser()->getId();

        if (!ForumPermissionModel::getInstance()->hasForumPermission($userId, $permCode)) {
            Flash::send(Alert::ERROR, "Forum", "Vous n'avez pas la permission de consulter ceci");
            Redirect::redirectPreviousRoute();
        }
    }

    public function alertNotHavePermissions(string $permCode): void
    {
        if ($permCode === "user_create_topic_tag") {
            Flash::send(Alert::ERROR, "Forum", "Vous ne pouvez pas définir de tag, ce champ à été vidé");
        }
    }

}