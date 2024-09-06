<?php

namespace CMW\Controller\Forum\Public;

use CMW\Controller\Forum\Admin\ForumPermissionController;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Forum\ForumCategoryModel;
use CMW\Model\Forum\ForumModel;
use CMW\Model\Forum\ForumSettingsModel;

/**
 * Class: @PublicForumMainController
 * @package Forum
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class PublicForumMainController extends AbstractController
{
    #[Link('/', Link::GET, [], '/forum')]
    private function publicBaseView(): void
    {
        $visitorCanViewForum = ForumSettingsModel::getInstance()->getOptionValue('visitorCanViewForum');

        if ($visitorCanViewForum === '0') {
            ForumPermissionController::getInstance()->redirectIfNotHavePermissions('user_view_forum');
        }

        $view = new View('Forum', 'main');
        $view->addVariableList(['forumModel' => forumModel::getInstance(), 'categoryModel' => ForumCategoryModel::getInstance()]);
        $view->addStyle('Admin/Resources/Vendors/Fontawesome-free/Css/fa-all.min.css');
        $view->view();
    }
}
