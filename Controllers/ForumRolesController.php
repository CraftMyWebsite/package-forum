<?php


namespace CMW\Controller\Forum;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Forum\ForumSettingsModel;


class ForumRolesController extends AbstractController {
    #[Link("/roles", Link::GET, [], "/cmw-admin/forum")]
    public function adminSettingsView(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.categories.list");

        $visitorCanViewForum = ForumSettingsModel::getInstance()->getOptionValue("visitorCanViewForum");

        View::createAdminView("Forum", "Roles/manage")
            ->addVariableList(["visitorCanViewForum" => $visitorCanViewForum])
            ->addStyle("Admin/Resources/Vendors/Simple-datatables/style.css","Admin/Resources/Assets/Css/Pages/simple-datatables.css")
            ->addScriptAfter("Admin/Resources/Vendors/Simple-datatables/Umd/simple-datatables.js","Admin/Resources/Assets/Js/Pages/simple-datatables.js")
            ->view();
    }

}