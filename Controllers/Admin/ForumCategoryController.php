<?php
namespace CMW\Controller\Forum\Admin;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Forum\ForumCategoryModel;
use CMW\Model\Forum\ForumModel;
use CMW\Model\Forum\ForumPermissionRoleModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use CMW\Utils\Website;

/**
 * Class: @ForumCategoryController
 * @package Forum
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ForumCategoryController extends AbstractController
{
    #[Link('/manage', Link::GET, [], '/cmw-admin/forum')]
    private function adminListCategoryView(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'forum.categories');

        $forumModel = forumModel::getInstance();
        $categoryModel = ForumCategoryModel::getInstance();
        $ForumRoles = ForumPermissionRoleModel::getInstance()->getRole();

        View::createAdminView('Forum', 'Manage/list')
            ->addVariableList(['forumModel' => $forumModel, 'categoryModel' => $categoryModel, 'ForumRoles' => $ForumRoles])
            ->view();
    }

    #[Link('/manage/add', Link::POST, [], '/cmw-admin/forum')]
    private function adminAddCategoryPost(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'forum.categories.add');
        if (Utils::isValuesEmpty($_POST, 'name')) {
            Flash::send('error', LangManager::translate('core.toaster.error'),
                LangManager::translate('forum.category.toaster.error.empty_input'));
            Redirect::redirectPreviousRoute();
        }
        [$name, $icon, $description] = Utils::filterInput('name', 'icon', 'description');

        $isRestricted = empty($_POST['allowedGroupsToggle']) ? 0 : 1;

        $forum = ForumCategoryModel::getInstance()->createCategory($name, $icon, $description, $isRestricted);

        if (!empty($_POST['allowedGroupsToggle'])) {
            foreach ($_POST['allowedGroups'] as $roleId) {
                ForumCategoryModel::getInstance()->addForumCategoryGroupsAllowed($roleId, $forum->getId());
            }
        }

        Flash::send('success', LangManager::translate('core.toaster.success'),
            LangManager::translate('forum.category.toaster.success'));

        header('location: ../manage');
    }

    #[Link('/manage/edit/:id', Link::POST, ['[0-9]+'], '/cmw-admin/forum')]
    private function adminEditCategory(int $id): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'forum.categories.edit');

        if (Utils::isValuesEmpty($_POST, 'name', 'description')) {
            Flash::send('error', LangManager::translate('core.toaster.error'),
                LangManager::translate('forum.category.toaster.error.empty_input'));
            Website::refresh();
            return;
        }

        $isRestricted = empty($_POST['allowedGroupsToggle']) ? 0 : 1;

        [$name, $icon, $description] = Utils::filterInput('name', 'icon', 'description');

        $forum = ForumCategoryModel::getInstance()->editCategory($id, $name, $icon, $description, $isRestricted);

        if ($isRestricted === 0) {
            ForumCategoryModel::getInstance()->deleteForumCategoryGroupsAllowed($forum->getId());
        }

        if (!empty($_POST['allowedGroupsToggle'])) {
            ForumCategoryModel::getInstance()->deleteForumCategoryGroupsAllowed($forum->getId());
            foreach ($_POST['allowedGroups'] as $roleId) {
                ForumCategoryModel::getInstance()->addForumCategoryGroupsAllowed($roleId, $forum->getId());
            }
        }

        header('location: ../../manage');
    }

    #[Link('/manage/delete/:id', Link::GET, ['[0-9]+'], '/cmw-admin/forum')]
    private function adminDeleteCategory(int $id): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'forum.categories.delete');

        $category = ForumCategoryModel::getInstance()->getCategoryById($id);

        if (is_null($category)) {
            Flash::send('error', LangManager::translate('core.toaster.error'),
                LangManager::translate('core.toaster.internalError'));

            header('location: ../../manage');
            return;
        }

        ForumCategoryModel::getInstance()->deleteCategory($id);

        Flash::send('success', LangManager::translate('core.toaster.success'),
            LangManager::translate('forum.category.delete.success'));

        header('location: ../../manage');
    }
}
