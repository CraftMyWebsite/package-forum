<?php


namespace CMW\Controller\Forum;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Env\EnvManager;
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
use JetBrains\PhpStorm\NoReturn;


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

    #[Link("/roles/add", Link::GET, [], "/cmw-admin/forum")]
    public function forumRoleAddView(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.roles.add");

        View::createAdminView("Forum", "Roles/add")
            ->view();
    }

    #[NoReturn] #[Link("/roles/delete/:roleId", Link::GET, [], "/cmw-admin/forum")]
    public function deleteRole(Request $request, int $roleId): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.role.remove");

        ForumPermissionRoleModel::getInstance()->removeRole($roleId);

        Flash::send(Alert::SUCCESS,"Forum","Ce role n'existe plus");

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link("/roles/add", Link::POST, [], "/cmw-admin/forum")]
    private function forumRoleAddPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.roles.edit");

        /*Role*/
        [$name, $weight, $description] = Utils::filterInput("name", "weight", "description");

        $role = ForumPermissionRoleModel::getInstance()->addRole($name, $weight, $description);

        /*Permissions*/
        $roleId = $role->getId();
        [$operator, $user_view_forum, $user_view_topic, $user_create_topic, $user_create_topic_tag, $user_create_pool, $user_edit_topic, $user_edit_tag, $user_edit_pool, $user_remove_topic, $user_react_topic, $user_change_react_topic, $user_remove_react_topic, $user_response_topic, $user_response_react, $user_response_change_react, $user_response_remove_react, $admin_change_topic_name, $admin_change_topic_tag, $admin_change_topic_prefix, $admin_set_important, $admin_set_pin, $admin_set_closed, $admin_move_topic] = Utils::filterInput("operator", "user_view_forum", "user_view_topic", "user_create_topic", "user_create_topic_tag", "user_create_pool", "user_edit_topic", "user_edit_tag", "user_edit_pool", "user_remove_topic", "user_react_topic", "user_change_react_topic", "user_remove_react_topic", "user_response_topic", "user_response_react", "user_response_change_react", "user_response_remove_react", "admin_change_topic_name", "admin_change_topic_tag", "admin_change_topic_prefix", "admin_set_important", "admin_set_pin", "admin_set_closed", "admin_move_topic");

        if ($operator != null) {ForumPermissionRoleModel::getInstance()->addRolePermissions(1,$roleId);}
        if ($user_view_forum != null) {ForumPermissionRoleModel::getInstance()->addRolePermissions(2,$roleId);}
        if ($user_view_topic != null) {ForumPermissionRoleModel::getInstance()->addRolePermissions(3,$roleId);}
        if ($user_create_topic != null) {ForumPermissionRoleModel::getInstance()->addRolePermissions(4,$roleId);}
        if ($user_create_topic_tag != null) {ForumPermissionRoleModel::getInstance()->addRolePermissions(5,$roleId);}
        if ($user_create_pool != null) {ForumPermissionRoleModel::getInstance()->addRolePermissions(6,$roleId);}
        if ($user_edit_topic != null) {ForumPermissionRoleModel::getInstance()->addRolePermissions(7,$roleId);}
        if ($user_edit_tag != null) {ForumPermissionRoleModel::getInstance()->addRolePermissions(8,$roleId);}
        if ($user_edit_pool != null) {ForumPermissionRoleModel::getInstance()->addRolePermissions(9,$roleId);}
        if ($user_remove_topic != null) {ForumPermissionRoleModel::getInstance()->addRolePermissions(10,$roleId);}
        if ($user_react_topic != null) {ForumPermissionRoleModel::getInstance()->addRolePermissions(11,$roleId);}
        if ($user_change_react_topic != null) {ForumPermissionRoleModel::getInstance()->addRolePermissions(12,$roleId);}
        if ($user_remove_react_topic != null) {ForumPermissionRoleModel::getInstance()->addRolePermissions(13,$roleId);}
        if ($user_response_topic != null) {ForumPermissionRoleModel::getInstance()->addRolePermissions(14,$roleId);}
        if ($user_response_react != null) {ForumPermissionRoleModel::getInstance()->addRolePermissions(15,$roleId);}
        if ($user_response_change_react != null) {ForumPermissionRoleModel::getInstance()->addRolePermissions(16,$roleId);}
        if ($user_response_remove_react != null) {ForumPermissionRoleModel::getInstance()->addRolePermissions(17,$roleId);}
        if ($admin_change_topic_name != null) {ForumPermissionRoleModel::getInstance()->addRolePermissions(18,$roleId);}
        if ($admin_change_topic_tag != null) {ForumPermissionRoleModel::getInstance()->addRolePermissions(19,$roleId);}
        if ($admin_change_topic_prefix != null) {ForumPermissionRoleModel::getInstance()->addRolePermissions(20,$roleId);}
        if ($admin_set_important != null) {ForumPermissionRoleModel::getInstance()->addRolePermissions(21,$roleId);}
        if ($admin_set_pin != null) {ForumPermissionRoleModel::getInstance()->addRolePermissions(22,$roleId);}
        if ($admin_set_closed != null) {ForumPermissionRoleModel::getInstance()->addRolePermissions(23,$roleId);}
        if ($admin_move_topic != null) {ForumPermissionRoleModel::getInstance()->addRolePermissions(24,$roleId);}

        Flash::send(Alert::SUCCESS,"Forum", "Le rôle ". $role->getName(). " est ajouté !");

        Redirect::redirectPreviousRoute();
    }

    #[Link("/roles/edit/:role_id", Link::GET, [], "/cmw-admin/forum")]
    public function forumRoleEditView(Request $request, int $role_id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.roles.add");

        $role = ForumPermissionRoleModel::getInstance()->getRoleById($role_id);

        View::createAdminView("Forum", "Roles/edit")
            ->addVariableList(["role" => $role])
            ->view();
    }

    #[NoReturn] #[Link("/roles/edit/:role_id", Link::POST, [], "/cmw-admin/forum")]
    private function forumRoleEditPost(Request $request, int $role_id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.roles.edit");

        /*Role*/
        [$name, $weight, $description] = Utils::filterInput("name", "weight", "description");

        ForumPermissionRoleModel::getInstance()->editRole($name, $weight, $description,$role_id);

        /*Permissions*/
        [$operator, $user_view_forum, $user_view_topic, $user_create_topic, $user_create_topic_tag, $user_create_pool, $user_edit_topic, $user_edit_tag, $user_edit_pool, $user_remove_topic, $user_react_topic, $user_change_react_topic, $user_remove_react_topic, $user_response_topic, $user_response_react, $user_response_change_react, $user_response_remove_react, $admin_change_topic_name, $admin_change_topic_tag, $admin_change_topic_prefix, $admin_set_important, $admin_set_pin, $admin_set_closed, $admin_move_topic] = Utils::filterInput("operator", "user_view_forum", "user_view_topic", "user_create_topic", "user_create_topic_tag", "user_create_pool", "user_edit_topic", "user_edit_tag", "user_edit_pool", "user_remove_topic", "user_react_topic", "user_change_react_topic", "user_remove_react_topic", "user_response_topic", "user_response_react", "user_response_change_react", "user_response_remove_react", "admin_change_topic_name", "admin_change_topic_tag", "admin_change_topic_prefix", "admin_set_important", "admin_set_pin", "admin_set_closed", "admin_move_topic");


        if (ForumPermissionRoleModel::getInstance()->roleHasPerm($role_id,1) && $operator == null) {
            ForumPermissionRoleModel::getInstance()->removeRolePermissions(1,$role_id);
        } elseif (!ForumPermissionRoleModel::getInstance()->roleHasPerm($role_id,1) && $operator != null) {
            ForumPermissionRoleModel::getInstance()->addRolePermissions(1,$role_id);
        }
        if (ForumPermissionRoleModel::getInstance()->roleHasPerm($role_id,2) && $user_view_forum == null) {
            ForumPermissionRoleModel::getInstance()->removeRolePermissions(2,$role_id);
        } elseif (!ForumPermissionRoleModel::getInstance()->roleHasPerm($role_id,2) && $user_view_forum != null) {
            ForumPermissionRoleModel::getInstance()->addRolePermissions(2,$role_id);
        }
        if (ForumPermissionRoleModel::getInstance()->roleHasPerm($role_id,3) && $user_view_topic == null) {
            ForumPermissionRoleModel::getInstance()->removeRolePermissions(3,$role_id);
        } elseif (!ForumPermissionRoleModel::getInstance()->roleHasPerm($role_id,3) && $user_view_topic != null) {
            ForumPermissionRoleModel::getInstance()->addRolePermissions(3,$role_id);
        }
        if (ForumPermissionRoleModel::getInstance()->roleHasPerm($role_id,4) && $user_create_topic == null) {
            ForumPermissionRoleModel::getInstance()->removeRolePermissions(4,$role_id);
        } elseif (!ForumPermissionRoleModel::getInstance()->roleHasPerm($role_id,4) && $user_create_topic != null) {
            ForumPermissionRoleModel::getInstance()->addRolePermissions(4,$role_id);
        }
        if (ForumPermissionRoleModel::getInstance()->roleHasPerm($role_id,5) && $user_create_topic_tag == null) {
            ForumPermissionRoleModel::getInstance()->removeRolePermissions(5,$role_id);
        } elseif (!ForumPermissionRoleModel::getInstance()->roleHasPerm($role_id,5) && $user_create_topic_tag != null) {
            ForumPermissionRoleModel::getInstance()->addRolePermissions(5,$role_id);
        }
        if (ForumPermissionRoleModel::getInstance()->roleHasPerm($role_id,6) && $user_create_pool == null) {
            ForumPermissionRoleModel::getInstance()->removeRolePermissions(6,$role_id);
        } elseif (!ForumPermissionRoleModel::getInstance()->roleHasPerm($role_id,6) && $user_create_pool != null) {
            ForumPermissionRoleModel::getInstance()->addRolePermissions(6,$role_id);
        }
        if (ForumPermissionRoleModel::getInstance()->roleHasPerm($role_id,7) && $user_edit_topic == null) {
            ForumPermissionRoleModel::getInstance()->removeRolePermissions(7,$role_id);
        } elseif (!ForumPermissionRoleModel::getInstance()->roleHasPerm($role_id,7) && $user_edit_topic != null) {
            ForumPermissionRoleModel::getInstance()->addRolePermissions(7,$role_id);
        }
        if (ForumPermissionRoleModel::getInstance()->roleHasPerm($role_id,8) && $user_edit_tag == null) {
            ForumPermissionRoleModel::getInstance()->removeRolePermissions(8,$role_id);
        } elseif (!ForumPermissionRoleModel::getInstance()->roleHasPerm($role_id,8) && $user_edit_tag != null) {
            ForumPermissionRoleModel::getInstance()->addRolePermissions(8,$role_id);
        }
        if (ForumPermissionRoleModel::getInstance()->roleHasPerm($role_id,9) && $user_edit_pool == null) {
            ForumPermissionRoleModel::getInstance()->removeRolePermissions(9,$role_id);
        } elseif (!ForumPermissionRoleModel::getInstance()->roleHasPerm($role_id,9) && $user_edit_pool != null) {
            ForumPermissionRoleModel::getInstance()->addRolePermissions(9,$role_id);
        }
        if (ForumPermissionRoleModel::getInstance()->roleHasPerm($role_id,10) && $user_remove_topic == null) {
            ForumPermissionRoleModel::getInstance()->removeRolePermissions(10,$role_id);
        } elseif (!ForumPermissionRoleModel::getInstance()->roleHasPerm($role_id,10) && $user_remove_topic != null) {
            ForumPermissionRoleModel::getInstance()->addRolePermissions(10,$role_id);
        }
        if (ForumPermissionRoleModel::getInstance()->roleHasPerm($role_id,11) && $user_react_topic == null) {
            ForumPermissionRoleModel::getInstance()->removeRolePermissions(11,$role_id);
        } elseif (!ForumPermissionRoleModel::getInstance()->roleHasPerm($role_id,11) && $user_react_topic != null) {
            ForumPermissionRoleModel::getInstance()->addRolePermissions(11,$role_id);
        }
        if (ForumPermissionRoleModel::getInstance()->roleHasPerm($role_id,12) && $user_change_react_topic == null) {
            ForumPermissionRoleModel::getInstance()->removeRolePermissions(12,$role_id);
        } elseif (!ForumPermissionRoleModel::getInstance()->roleHasPerm($role_id,12) && $user_change_react_topic != null) {
            ForumPermissionRoleModel::getInstance()->addRolePermissions(12,$role_id);
        }
        if (ForumPermissionRoleModel::getInstance()->roleHasPerm($role_id,13) && $user_remove_react_topic == null) {
            ForumPermissionRoleModel::getInstance()->removeRolePermissions(13,$role_id);
        } elseif (!ForumPermissionRoleModel::getInstance()->roleHasPerm($role_id,13) && $user_remove_react_topic != null) {
            ForumPermissionRoleModel::getInstance()->addRolePermissions(13,$role_id);
        }
        if (ForumPermissionRoleModel::getInstance()->roleHasPerm($role_id,14) && $user_response_topic == null) {
            ForumPermissionRoleModel::getInstance()->removeRolePermissions(14,$role_id);
        } elseif (!ForumPermissionRoleModel::getInstance()->roleHasPerm($role_id,14) && $user_response_topic != null) {
            ForumPermissionRoleModel::getInstance()->addRolePermissions(14,$role_id);
        }
        if (ForumPermissionRoleModel::getInstance()->roleHasPerm($role_id,15) && $user_response_react == null) {
            ForumPermissionRoleModel::getInstance()->removeRolePermissions(15,$role_id);
        } elseif (!ForumPermissionRoleModel::getInstance()->roleHasPerm($role_id,15) && $user_response_react != null) {
            ForumPermissionRoleModel::getInstance()->addRolePermissions(15,$role_id);
        }
        if (ForumPermissionRoleModel::getInstance()->roleHasPerm($role_id,16) && $user_response_change_react == null) {
            ForumPermissionRoleModel::getInstance()->removeRolePermissions(16,$role_id);
        } elseif (!ForumPermissionRoleModel::getInstance()->roleHasPerm($role_id,16) && $user_response_change_react != null) {
            ForumPermissionRoleModel::getInstance()->addRolePermissions(16,$role_id);
        }
        if (ForumPermissionRoleModel::getInstance()->roleHasPerm($role_id,17) && $user_response_remove_react == null) {
            ForumPermissionRoleModel::getInstance()->removeRolePermissions(17,$role_id);
        } elseif (!ForumPermissionRoleModel::getInstance()->roleHasPerm($role_id,17) && $user_response_remove_react != null) {
            ForumPermissionRoleModel::getInstance()->addRolePermissions(17,$role_id);
        }
        if (ForumPermissionRoleModel::getInstance()->roleHasPerm($role_id,18) && $admin_change_topic_name == null) {
            ForumPermissionRoleModel::getInstance()->removeRolePermissions(18,$role_id);
        } elseif (!ForumPermissionRoleModel::getInstance()->roleHasPerm($role_id,18) && $admin_change_topic_name != null) {
            ForumPermissionRoleModel::getInstance()->addRolePermissions(18,$role_id);
        }
        if (ForumPermissionRoleModel::getInstance()->roleHasPerm($role_id,19) && $admin_change_topic_tag == null) {
            ForumPermissionRoleModel::getInstance()->removeRolePermissions(19,$role_id);
        } elseif (!ForumPermissionRoleModel::getInstance()->roleHasPerm($role_id,19) && $admin_change_topic_tag != null) {
            ForumPermissionRoleModel::getInstance()->addRolePermissions(19,$role_id);
        }
        if (ForumPermissionRoleModel::getInstance()->roleHasPerm($role_id,20) && $admin_change_topic_prefix == null) {
            ForumPermissionRoleModel::getInstance()->removeRolePermissions(20,$role_id);
        } elseif (!ForumPermissionRoleModel::getInstance()->roleHasPerm($role_id,20) && $admin_change_topic_prefix != null) {
            ForumPermissionRoleModel::getInstance()->addRolePermissions(20,$role_id);
        }
        if (ForumPermissionRoleModel::getInstance()->roleHasPerm($role_id,21) && $admin_set_important == null) {
            ForumPermissionRoleModel::getInstance()->removeRolePermissions(21,$role_id);
        } elseif (!ForumPermissionRoleModel::getInstance()->roleHasPerm($role_id,21) && $admin_set_important != null) {
            ForumPermissionRoleModel::getInstance()->addRolePermissions(21,$role_id);
        }
        if (ForumPermissionRoleModel::getInstance()->roleHasPerm($role_id,22) && $admin_set_pin == null) {
            ForumPermissionRoleModel::getInstance()->removeRolePermissions(22,$role_id);
        } elseif (!ForumPermissionRoleModel::getInstance()->roleHasPerm($role_id,22) && $admin_set_pin != null) {
            ForumPermissionRoleModel::getInstance()->addRolePermissions(22,$role_id);
        }
        if (ForumPermissionRoleModel::getInstance()->roleHasPerm($role_id,23) && $admin_set_closed == null) {
            ForumPermissionRoleModel::getInstance()->removeRolePermissions(23,$role_id);
        } elseif (!ForumPermissionRoleModel::getInstance()->roleHasPerm($role_id,23) && $admin_set_closed != null) {
            ForumPermissionRoleModel::getInstance()->addRolePermissions(23,$role_id);
        }
        if (ForumPermissionRoleModel::getInstance()->roleHasPerm($role_id,24) && $admin_move_topic == null) {
            ForumPermissionRoleModel::getInstance()->removeRolePermissions(24,$role_id);
        } elseif (!ForumPermissionRoleModel::getInstance()->roleHasPerm($role_id,24) && $admin_move_topic != null) {
            ForumPermissionRoleModel::getInstance()->addRolePermissions(24,$role_id);
        }

        Flash::send(Alert::SUCCESS,"Forum", "Le rôle est modifié !");

        Redirect::redirectPreviousRoute();
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