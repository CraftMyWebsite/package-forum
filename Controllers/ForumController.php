<?php


namespace CMW\Controller\Forum;

use CMW\Controller\Users\UsersController;
use CMW\Event\Users\RegisterEvent;
use CMW\Manager\Events\Listener;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Requests\Request;
use CMW\Manager\Views\View;
use CMW\Model\Forum\ForumCategoryModel;
use CMW\Model\Forum\ForumModel;
use CMW\Manager\Router\Link;
use CMW\Manager\Flash\Flash;
use CMW\Model\Forum\ForumPermissionModel;
use CMW\Model\Forum\ForumPermissionRoleModel;
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
    #[Link("/manage/addForum/:catId", Link::GET, [], "/cmw-admin/forum")]
    public function adminAddForum(Request $request, int $catId): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.categories.list");

        $category = ForumCategoryModel::getInstance()->getCategoryById($catId);
        $ForumRoles = ForumPermissionRoleModel::getInstance()->getRole();

        View::createAdminView("Forum", "Manage/addForum")
            ->addVariableList(["category" => $category, "ForumRoles" => $ForumRoles])
            ->view();
    }
    #[Link("/manage/addForum/:catId", Link::POST, [], "/cmw-admin/forum")]
    public function adminAddForumPost(Request $request, int $catId): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.add");

        [$name, $icon, $description] = Utils::filterInput("name", "icon", "description");

        $isRestricted = empty($_POST['allowedGroupsToggle']) ? 0 : 1;

        $forum = forumModel::getInstance()->createForum($name, $icon, $description,$isRestricted , $catId);

        if (!empty($_POST['allowedGroupsToggle']) && !empty($_POST['allowedGroups'])) {
            foreach ($_POST['allowedGroups'] as $roleId) {
                ForumModel::getInstance()->addForumGroupsAllowed($roleId, $forum->getId());
            }
        }

        Flash::send("success", LangManager::translate("core.toaster.success"),
            LangManager::translate("forum.forum.add.toaster.success"));

        header("location: ../");
    }

    #[Link("/manage/addSubForum/:forumId", Link::GET, [], "/cmw-admin/forum")]
    public function adminAddSubForum(Request $request, int $forumId): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.categories.list");

        $forum = ForumModel::getInstance()->getForumById($forumId);
        $ForumRoles = ForumPermissionRoleModel::getInstance()->getRole();

        View::createAdminView("Forum", "Manage/addSubForum")
            ->addVariableList(["forum" => $forum, "ForumRoles" => $ForumRoles])
            ->view();
    }

    #[Link("/manage/addSubForum/:forumId", Link::POST, [], "/cmw-admin/forum")]
    public function adminAddSubForumPost(Request $request, int $forumId): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.add");

        [$name, $icon, $description] = Utils::filterInput("name", "icon", "description");

        $isRestricted = empty($_POST['allowedGroupsToggle']) ? 0 : 1;

        $forum = forumModel::getInstance()->createSubForum($name, $icon, $description,$isRestricted , $forumId);

        if (!empty($_POST['allowedGroupsToggle']) && !empty($_POST['allowedGroups'])) {
            foreach ($_POST['allowedGroups'] as $roleId) {
                ForumModel::getInstance()->addForumGroupsAllowed($roleId, $forum->getId());
            }
        }

        Flash::send("success", LangManager::translate("core.toaster.success"),
            LangManager::translate("forum.forum.add.toaster.success"));

        header("location: ../");
    }

    #[Link("/manage/editForum/:forumId", Link::GET, [], "/cmw-admin/forum")]
    public function adminEditForum(Request $request, int $forumId): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.categories.list");

        $forum = ForumModel::getInstance()->getForumById($forumId);
        $forumModel = ForumModel::getInstance();
        $ForumRoles = ForumPermissionRoleModel::getInstance()->getRole();

        View::createAdminView("Forum", "Manage/editForum")
            ->addVariableList(["forum" => $forum, "ForumRoles" => $ForumRoles, "forumModel" => $forumModel])
            ->view();
    }

    #[Link("/manage/editForum/:forumId", Link::POST, [], "/cmw-admin/forum")]
    public function adminEditForumPost(Request $request, int $forumId): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.add");

        $isRestricted = empty($_POST['allowedGroupsToggle']) ? 0 : 1;

        [$name, $icon, $description] = Utils::filterInput("name", "icon", "description");

        ForumModel::getInstance()->editForum($forumId, $name, $icon, $description, $isRestricted);

        if ($isRestricted === 0) {
            ForumModel::getInstance()->deleteForumGroupsAllowed($forumId);
        }

        if (!empty($_POST['allowedGroupsToggle'])) {
            ForumModel::getInstance()->deleteForumGroupsAllowed($forumId);
            foreach ($_POST['allowedGroups'] as $roleId) {
                ForumModel::getInstance()->addForumGroupsAllowed($roleId, $forumId);
            }
        }

        Flash::send("success", LangManager::translate("core.toaster.success"),
            LangManager::translate("forum.forum.add.toaster.success"));

        header("location: ../");
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

    /**
     * @param string $permCode
     * @return bool
     * @desc used in public view : return true if user have permission to do something like "user_create_topic"
     */
    public function hasPermission(string $permCode) :bool
    {
        $userId = UsersModel::getCurrentUser()->getId();

        if (!ForumPermissionModel::getInstance()->hasForumPermission($userId, $permCode)) {
            return false;
        }
        return true;
    }

    /**
     * @param string $permCode
     * @return void
     * @desc used in controller : Redirect to previous route if the user don't have the permission
     */
    public function redirectIfNotHavePermissions(string $permCode): void
    {
        if (!UsersController::isUserLogged()) {
            Flash::send(Alert::ERROR, "Forum", "L'accès au forum est reservé au membre, merci de vous connectez avant d'allez plus loin.");
            Redirect::redirect('login');
        }

        $userId = UsersModel::getCurrentUser()->getId();

        if (!ForumPermissionModel::getInstance()->hasForumPermission($userId, $permCode)) {
            Flash::send(Alert::ERROR, "Forum", "Vous n'avez pas la permission de faire ceci");
            Redirect::redirectPreviousRoute();
        }
    }

    /**
     * @param string $permCode
     * @return void
     * @desc used in controller : Just alert the user if doesn't have permission, action required in addition to this, to properly process the request
     */
    public function alertNotHavePermissions(string $permCode): void
    {
        if ($permCode === "user_create_topic_tag") {
            Flash::send(Alert::ERROR, "Forum", "Vous ne pouvez pas définir de tag, ce champ à été vidé");
        }
        if ($permCode === "user_response_topic") {
            Flash::send(Alert::ERROR, "Forum", "Vous n'avez pas la permission de répondre au topic");
        }
    }

    /*------------------------
     * USERS EVENT DEPENDENCIES
     * ----------------------*/
    #[Listener(eventName: RegisterEvent::class, times: 0, weight: 1)]
    public static function onRegister(mixed $userId): void
    {
        ForumPermissionRoleModel::getInstance()->addUserForumDefaultRoleOnRegister($userId);
    }

}