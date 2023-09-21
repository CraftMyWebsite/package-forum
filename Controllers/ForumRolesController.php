<?php


namespace CMW\Controller\Forum;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Requests\Request;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Forum\ForumPermissionModel;
use CMW\Model\Forum\ForumPermissionRoleModel;
use CMW\Model\Forum\ForumSettingsModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;


class ForumRolesController extends AbstractController {
    #[Link("/roles", Link::GET, [], "/cmw-admin/forum")]
    public function forumRoleView(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.categories.list");

        $visitorCanViewForum = ForumSettingsModel::getInstance()->getOptionValue("visitorCanViewForum");
        $roles = ForumPermissionRoleModel::getInstance()->getRole();
        $userList = UsersModel::getInstance()->getUsers();
        $userRole = ForumPermissionRoleModel::getInstance();

        View::createAdminView("Forum", "Roles/manage")
            ->addVariableList(["visitorCanViewForum" => $visitorCanViewForum, "roles" => $roles, "userList" => $userList, "userRole" => $userRole])
            ->addStyle("Admin/Resources/Vendors/Simple-datatables/style.css","Admin/Resources/Assets/Css/Pages/simple-datatables.css")
            ->addScriptAfter("Admin/Resources/Vendors/Simple-datatables/Umd/simple-datatables.js","Admin/Resources/Assets/Js/Pages/simple-datatables.js")
            ->view();
    }

    #[Link("/roles/settings", Link::POST, [], "/cmw-admin/forum")]
    private function forumRoleSettings(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.roles.edit");

        [$visitorCanViewForum] = Utils::filterInput("visitorCanViewForum");

        ForumSettingsModel::getInstance()->updateVisitorCanViewForum($visitorCanViewForum === NULL ? 0 : 1);

        Redirect::redirectPreviousRoute();
    }

    #[Link("/roles/user_role/:userId", Link::POST, ["userId" => "[0-9]+"], "/cmw-admin/forum")]
    private function forumUserRoleSettings(Request $request, int $userId): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.roles.edit");

        [$roleId] = Utils::filterInput("role_id");

        ForumPermissionRoleModel::getInstance()->changeUserRole($userId,$roleId);

        Redirect::redirectPreviousRoute();
    }

    #[Link("/roles/set_default/:id/:question", Link::GET, ["id" => "[0-9]+"], "/cmw-admin/forum")]
    #[NoReturn] private function forumRolesSetDefault(Request $request, int $id, string $question): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.roles.edit");

        ForumPermissionRoleModel::getInstance()->changeDefaultRole($id, $question);

        Flash::send(Alert::SUCCESS, LangManager::translate("core.toaster.success"),
            LangManager::translate('users.toaster.role_edited'));

        Redirect::redirectPreviousRoute();
    }

}