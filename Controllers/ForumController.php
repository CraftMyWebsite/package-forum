<?php
namespace CMW\Controller\Forum;

use CMW\Controller\Users\UsersController;
use CMW\Event\Users\RegisterEvent;
use CMW\Manager\Events\Listener;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Requests\Request;
use CMW\Manager\Views\View;
use CMW\Model\Forum\ForumCategoryModel;
use CMW\Model\Forum\ForumModel;
use CMW\Manager\Router\Link;
use CMW\Manager\Flash\Flash;
use CMW\Model\Forum\ForumPermissionRoleModel;
use CMW\Model\Forum\ForumUserBlockedModel;
use CMW\Utils\Utils;

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
        $disallowTopics = empty($_POST['disallowTopics']) ? 0 : 1;

        $forum = forumModel::getInstance()->createForum($name, $icon, $description, $isRestricted, $disallowTopics, $catId);

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
        $disallowTopics = empty($_POST['disallowTopics']) ? 0 : 1;

        $forum = forumModel::getInstance()->createSubForum($name, $icon, $description, $isRestricted, $disallowTopics, $forumId);

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
        $disallowTopics = empty($_POST['disallowTopics']) ? 0 : 1;

        [$name, $icon, $description] = Utils::filterInput("name", "icon", "description");

        ForumModel::getInstance()->editForum($forumId, $name, $icon, $description, $isRestricted, $disallowTopics);

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

    #[Link("/manage/deleteForum/:id", Link::GET, ['[0-9]+'], "/cmw-admin/forum")]
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

    /*------------------------
     * USERS EVENT DEPENDENCIES
     * ----------------------*/
    #[Listener(eventName: RegisterEvent::class, times: 0, weight: 1)]
    public static function onRegister(mixed $userId): void
    {
        ForumPermissionRoleModel::getInstance()->addUserForumDefaultRoleOnRegister($userId);
        ForumUserBlockedModel::getInstance()->addDefaultBlockOnRegister($userId);
    }

}