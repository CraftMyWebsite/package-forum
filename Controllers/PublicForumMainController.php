<?php
namespace CMW\Controller\Forum;

use CMW\Controller\Core\CoreController;
use CMW\Model\Forum\ForumCategoryModel;
use CMW\Model\Forum\ForumModel;
use CMW\Model\Forum\ForumSettingsModel;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;

/**
 * Class: @PublicForumMainController
 * @package Forum
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class PublicForumMainController extends CoreController
{
    #[Link("/", Link::GET, [], "/forum")]
    public function publicBaseView(): void
    {
        $visitorCanViewForum = ForumSettingsModel::getInstance()->getOptionValue("visitorCanViewForum");

        if ($visitorCanViewForum === "0") {
            ForumPermissionController::getInstance()->redirectIfNotHavePermissions("user_view_forum");
        }

        $view = new View("Forum", "main");
        $view->addVariableList(["forumModel" => forumModel::getInstance(), "categoryModel" => ForumCategoryModel::getInstance()]);
        $view->addStyle("Admin/Resources/Vendors/Fontawesome-free/Css/fa-all.min.css");
        $view->view();
    }
}