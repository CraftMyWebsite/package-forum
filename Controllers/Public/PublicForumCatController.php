<?php

namespace CMW\Controller\Forum\Public;

use CMW\Controller\Forum\Admin\ForumPermissionController;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Forum\ForumCategoryModel;
use CMW\Model\Forum\ForumModel;
use CMW\Model\Forum\ForumSettingsModel;
use CMW\Utils\Redirect;

/**
 * Class: @PublicForumCatController
 * @package Forum
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class PublicForumCatController extends AbstractController
{
    #[Link('/c/:catSlug', Link::GET, ['.*?'], '/forum')]
    private function publicCatView(string $catSlug): void
    {
        $visitorCanViewForum = ForumSettingsModel::getInstance()->getOptionValue('visitorCanViewForum');

        if ($visitorCanViewForum === '0') {
            ForumPermissionController::getInstance()->redirectIfNotHavePermissions('user_view_forum');
        }

        $category = ForumCategoryModel::getInstance()->getCatBySlug($catSlug);

        if (!$category) {
            Redirect::errorPage(404);
        }

        if (!$category->isUserAllowed()) {
            Flash::send(Alert::ERROR, 'Forum', 'Cette catégorie est privé !');
            Redirect::redirect('forum');
        }

        $view = new View('Forum', 'cat');
        $view->addVariableList(['forumModel' => forumModel::getInstance(), 'category' => $category]);
        $view->addStyle('Admin/Resources/Vendors/Fontawesome-free/Css/fa-all.min.css');
        $view->view();
    }
}
