<?php

namespace CMW\Controller\Forum\Admin;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Forum\ForumPermissionRoleModel;
use CMW\Model\Forum\ForumSettingsModel;
use CMW\Model\Forum\ForumUserBlockedModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class: @ForumRolesController
 * @package Forum
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ForumRolesController extends AbstractController
{
    #[Link("/roles", Link::GET, [], "/cmw-admin/forum")]
    private function forumRoleView(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.roles");

        $visitorCanViewForum = ForumSettingsModel::getInstance()->getOptionValue("visitorCanViewForum");
        $roles = ForumPermissionRoleModel::getInstance()->getRole();
        $userList = UsersModel::getInstance()->getUsers();
        $userRole = ForumPermissionRoleModel::getInstance();
        $userBlocked = ForumUserBlockedModel::getInstance();

        View::createAdminView("Forum", "Roles/manage")
            ->addVariableList(["visitorCanViewForum" => $visitorCanViewForum, "roles" => $roles, "userList" => $userList, "userRole" => $userRole, "userBlocked" => $userBlocked])
            ->addStyle("Admin/Resources/Assets/Css/simple-datatables.css")
            ->addScriptAfter("Admin/Resources/Vendors/Simple-datatables/simple-datatables.js",
                "Admin/Resources/Vendors/Simple-datatables/config-datatables.js")
            ->view();
    }

    #[Link("/roles/add", Link::GET, [], "/cmw-admin/forum")]
    private function forumRoleAddView(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.roles");

        View::createAdminView("Forum", "Roles/add")
            ->view();
    }

    #[NoReturn] #[Link("/roles/delete/:roleId", Link::GET, [], "/cmw-admin/forum")]
    private function deleteRole(int $roleId): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.roles");

        ForumPermissionRoleModel::getInstance()->removeRole($roleId);

        Flash::send(Alert::SUCCESS, "Forum", "Ce role n'existe plus");

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link("/roles/add", Link::POST, [], "/cmw-admin/forum")]
    private function forumRoleAddPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.roles");

        /*Role*/
        [$name, $weight, $description] = Utils::filterInput("name", "weight", "description");

        $role = ForumPermissionRoleModel::getInstance()->addRole($name, $weight, $description);

        if (!$role) {
            Flash::send(Alert::ERROR, "Forum", "Le rôle n'a pas pu être ajouté !");
            Redirect::redirectPreviousRoute();
        }

        /*Permissions*/
        $roleId = $role->getId();
        [$operator,
            $user_view_forum,
            $user_view_topic,
            $user_create_topic,
            $user_create_topic_tag,
            $user_create_pool,
            $user_edit_topic,
            $user_edit_tag,
            $user_edit_pool,
            $user_remove_topic,
            $user_react_topic,
            $user_change_react_topic,
            $user_remove_react_topic,
            $user_response_topic,
            $user_response_react,
            $user_response_change_react,
            $user_response_remove_react,
            $admin_change_topic_name,
            $admin_change_topic_tag,
            $admin_change_topic_prefix,
            $admin_set_important,
            $admin_set_pin,
            $admin_set_closed,
            $admin_move_topic,
            $admin_bypass_forum_disallow_topics,
            $user_remove_response,
            $user_edit_response,
            $user_add_file,
            $user_download_file] = Utils::filterInput(
            "operator",
            "user_view_forum",
            "user_view_topic",
            "user_create_topic",
            "user_create_topic_tag",
            "user_create_pool",
            "user_edit_topic",
            "user_edit_tag",
            "user_edit_pool",
            "user_remove_topic",
            "user_react_topic",
            "user_change_react_topic",
            "user_remove_react_topic",
            "user_response_topic",
            "user_response_react",
            "user_response_change_react",
            "user_response_remove_react",
            "admin_change_topic_name",
            "admin_change_topic_tag",
            "admin_change_topic_prefix",
            "admin_set_important",
            "admin_set_pin",
            "admin_set_closed",
            "admin_move_topic",
            "admin_bypass_forum_disallow_topics",
            "user_remove_response",
            "user_edit_response",
            "user_add_file",
            "user_download_file");

        $permissionsName = [
            $operator,
            $user_view_forum,
            $user_view_topic,
            $user_create_topic,
            $user_create_topic_tag,
            $user_create_pool,
            $user_edit_topic,
            $user_edit_tag,
            $user_edit_pool,
            $user_remove_topic,
            $user_react_topic,
            $user_change_react_topic,
            $user_remove_react_topic,
            $user_response_topic,
            $user_response_react,
            $user_response_change_react,
            $user_response_remove_react,
            $admin_change_topic_name,
            $admin_change_topic_tag,
            $admin_change_topic_prefix,
            $admin_set_important,
            $admin_set_pin,
            $admin_set_closed,
            $admin_move_topic,
            $admin_bypass_forum_disallow_topics,
            $user_remove_response,
            $user_edit_response,
            $user_add_file,
            $user_download_file];

        foreach ($permissionsName as $i => $iValue) {
            $checkbox = $iValue;
            $_permissionRoleInstance = ForumPermissionRoleModel::getInstance();
            if ($checkbox !== null) {
                $_permissionRoleInstance->addRolePermissions($roleId, $i + 1);
            }
        }

        Flash::send(Alert::SUCCESS, "Forum", "Le rôle " . $role->getName() . " est ajouté !");

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link("/roles/edit/:role_id", Link::POST, [], "/cmw-admin/forum")]
    private function forumRoleEditPost(int $roleId): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.roles");

        /*Role*/
        [$name, $weight, $description] = Utils::filterInput("name", "weight", "description");

        ForumPermissionRoleModel::getInstance()->editRole($name, $weight, $description, $roleId);

        /*Permissions*/
        [$operator,
            $user_view_forum,
            $user_view_topic,
            $user_create_topic,
            $user_create_topic_tag,
            $user_create_pool,
            $user_edit_topic,
            $user_edit_tag,
            $user_edit_pool,
            $user_remove_topic,
            $user_react_topic,
            $user_change_react_topic,
            $user_remove_react_topic,
            $user_response_topic,
            $user_response_react,
            $user_response_change_react,
            $user_response_remove_react,
            $admin_change_topic_name,
            $admin_change_topic_tag,
            $admin_change_topic_prefix,
            $admin_set_important,
            $admin_set_pin,
            $admin_set_closed,
            $admin_move_topic,
            $admin_bypass_forum_disallow_topics,
            $user_remove_response,
            $user_edit_response,
            $user_add_file,
            $user_download_file] = Utils::filterInput(
            "operator",
            "user_view_forum",
            "user_view_topic",
            "user_create_topic",
            "user_create_topic_tag",
            "user_create_pool",
            "user_edit_topic",
            "user_edit_tag",
            "user_edit_pool",
            "user_remove_topic",
            "user_react_topic",
            "user_change_react_topic",
            "user_remove_react_topic",
            "user_response_topic",
            "user_response_react",
            "user_response_change_react",
            "user_response_remove_react",
            "admin_change_topic_name",
            "admin_change_topic_tag",
            "admin_change_topic_prefix",
            "admin_set_important",
            "admin_set_pin",
            "admin_set_closed",
            "admin_move_topic",
            "admin_bypass_forum_disallow_topics",
            "user_remove_response",
            "user_edit_response",
            "user_add_file",
            "user_download_file");

        $permissionsName = [
            $operator,
            $user_view_forum,
            $user_view_topic,
            $user_create_topic,
            $user_create_topic_tag,
            $user_create_pool,
            $user_edit_topic,
            $user_edit_tag,
            $user_edit_pool,
            $user_remove_topic,
            $user_react_topic,
            $user_change_react_topic,
            $user_remove_react_topic,
            $user_response_topic,
            $user_response_react,
            $user_response_change_react,
            $user_response_remove_react,
            $admin_change_topic_name,
            $admin_change_topic_tag,
            $admin_change_topic_prefix,
            $admin_set_important,
            $admin_set_pin,
            $admin_set_closed,
            $admin_move_topic,
            $admin_bypass_forum_disallow_topics,
            $user_remove_response,
            $user_edit_response,
            $user_add_file,
            $user_download_file];

        foreach ($permissionsName as $i => $iValue) {
            $checkbox = $iValue;
            $_permissionRoleInstance = ForumPermissionRoleModel::getInstance();
            if ($_permissionRoleInstance->roleHasPerm($roleId, $i + 1) && $checkbox === null) {
                $_permissionRoleInstance->removeRolePermissions($roleId, $i + 1);
            } elseif (!$_permissionRoleInstance->roleHasPerm($roleId, $i + 1) && $checkbox !== null) {
                $_permissionRoleInstance->addRolePermissions($roleId, $i + 1);
            }
        }

        Flash::send(Alert::SUCCESS, "Forum", "Le rôle est modifié !");

        Redirect::redirectPreviousRoute();
    }

    #[Link("/roles/edit/:role_id", Link::GET, [], "/cmw-admin/forum")]
    private function forumRoleEditView(int $role_id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.roles");

        $role = ForumPermissionRoleModel::getInstance()->getRoleById($role_id);

        View::createAdminView("Forum", "Roles/edit")
            ->addVariableList(["role" => $role])
            ->view();
    }

    #[NoReturn] #[Link("/roles/settings", Link::POST, [], "/cmw-admin/forum")]
    private function forumRoleSettings(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.roles");

        [$visitorCanViewForum] = Utils::filterInput("visitorCanViewForum");

        ForumSettingsModel::getInstance()->updateVisitorCanViewForum($visitorCanViewForum === NULL ? 0 : 1);

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link("/roles/user_role/:userId", Link::POST, ["userId" => "[0-9]+"], "/cmw-admin/forum")]
    private function forumUserRoleSettings(int $userId): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.roles");

        [$roleId] = Utils::filterInput("role_id");

        ForumPermissionRoleModel::getInstance()->changeUserRole($userId, $roleId);

        Redirect::redirectPreviousRoute();
    }

    #[Link("/roles/set_default/:id/:question", Link::GET, ["id" => "[0-9]+"], "/cmw-admin/forum")]
    #[NoReturn] private function forumRolesSetDefault(int $id, string $question): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.roles");

        ForumPermissionRoleModel::getInstance()->changeDefaultRole($id, $question);

        Flash::send(Alert::SUCCESS, LangManager::translate("core.toaster.success"),
            LangManager::translate('users.toaster.role_edited'));

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link("/roles/block/:userId", Link::POST, ["userId" => "[0-9]+"], "/cmw-admin/forum")]
    private function forumBlockUser(int $userId): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.roles");

        [$reason] = Utils::filterInput("reason");

        ForumUserBlockedModel::getInstance()->blockUser($userId, $reason);

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link("/roles/unblock/:userId", Link::POST, ["userId" => "[0-9]+"], "/cmw-admin/forum")]
    private function forumUnblockUser(int $userId): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.roles");

        [$reason] = Utils::filterInput("reason");

        ForumUserBlockedModel::getInstance()->unblockUser($userId, $reason);

        Redirect::redirectPreviousRoute();
    }

}