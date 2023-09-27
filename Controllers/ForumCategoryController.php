<?php


namespace CMW\Controller\Forum;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Requests\Request;
use CMW\Model\Forum\ForumCategoryModel;
use CMW\Model\Forum\ForumModel;
use CMW\Manager\Router\Link;
use CMW\Manager\Flash\Flash;
use CMW\Model\Forum\ForumPermissionRoleModel;
use CMW\Model\Forum\ForumResponseModel;
use CMW\Model\Forum\ForumSettingsModel;
use CMW\Model\Forum\ForumTopicModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use CMW\Manager\Views\View;
use CMW\Utils\Website;


/**
 * Class: @ForumCategoryController
 * @package Forum
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ForumCategoryController extends AbstractController
{
    #[Link("/manage", Link::GET, [], "/cmw-admin/forum")]
    public function adminListCategoryView(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.categories.list");

        $forumModel = forumModel::getInstance();
        $categoryModel = ForumCategoryModel::getInstance();
        $topicModel = ForumTopicModel::getInstance();
        $responseModel = ForumResponseModel::getInstance();
        $iconNotRead = ForumSettingsModel::getInstance()->getOptionValue("IconNotRead");
        $iconImportant = ForumSettingsModel::getInstance()->getOptionValue("IconImportant");
        $iconPin = ForumSettingsModel::getInstance()->getOptionValue("IconPin");
        $iconClosed = ForumSettingsModel::getInstance()->getOptionValue("IconClosed");
        $ForumRoles = ForumPermissionRoleModel::getInstance()->getRole();

        View::createAdminView("Forum", "list")
            ->addVariableList(["forumModel" => $forumModel, "categoryModel" => $categoryModel,"topicModel" => $topicModel, "responseModel" => $responseModel,"iconNotRead" => $iconNotRead, "iconImportant" => $iconImportant, "iconPin" => $iconPin, "iconClosed" => $iconClosed, "ForumRoles" => $ForumRoles])
            ->addStyle("Admin/Resources/Vendors/Simple-datatables/style.css","Admin/Resources/Assets/Css/Pages/simple-datatables.css")
            ->addScriptAfter("Admin/Resources/Vendors/Simple-datatables/Umd/simple-datatables.js","Admin/Resources/Assets/Js/Pages/simple-datatables.js")
            ->view();
    }

    #[Link("/add", Link::POST, [], "/cmw-admin/forum/categories")]
    public function adminAddCategoryPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.categories.add");

        if (Utils::isValuesEmpty($_POST, "name", "description")) {
            Flash::send("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("forum.category.toaster.error.empty_input"));
            Website::refresh();
            return;
        }

        [$name, $icon, $description] = Utils::filterInput("name", "icon", "description");

        $isRestricted = empty($_POST['allowedGroupsToggle']) ? 0 : 1;

        $forum = ForumCategoryModel::getInstance()->createCategory($name, $icon, $description, $isRestricted);

        if (!empty($_POST['allowedGroupsToggle']) && !empty($_POST['allowedGroups'])) {
            foreach ($_POST['allowedGroups'] as $roleId) {
                ForumCategoryModel::getInstance()->addForumCategoryGroupsAllowed($roleId, $forum->getId());
            }
        }

        Flash::send("success", LangManager::translate("core.toaster.success"),
            LangManager::translate("forum.category.toaster.success"));

        header("location: ../manage");
    }

    #[Link("/edit/:id", Link::POST, ['[0-9]+'], "/cmw-admin/forum/categories")]
    public function adminEditCategory(Request $request, int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.categories.delete");

        $category = ForumCategoryModel::getInstance()->getCategoryById($id);

        if (Utils::isValuesEmpty($_POST, "name", "description")) {
            Flash::send("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("forum.category.toaster.error.empty_input"));
            Website::refresh();
            return;
        }

        [$name, $icon, $description] = Utils::filterInput("name", "icon", "description");

        ForumCategoryModel::getInstance()->editCategory($id, $name, $icon, $description);

        header("location: ../../manage");
    }

    #[Link("/delete/:id", Link::GET, ['[0-9]+'], "/cmw-admin/forum/categories")]
    public function adminDeleteCategory(Request $request, int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.categories.delete");

        $category = ForumCategoryModel::getInstance()->getCategoryById($id);

        if (is_null($category)) {
            Flash::send("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.internalError"));

            header("location: ../../manage");
            return;
        }

        ForumCategoryModel::getInstance()->deleteCategory($id);

        Flash::send("success", LangManager::translate("core.toaster.success"),
            LangManager::translate("forum.category.delete.success"));

        header("location: ../../manage");
    }

    #[Link("/manage/edit", Link::POST, ['.*?'], "/cmw-admin/forum")]
    public function adminEditTopicPost(): void
    {
        [$topicId, $name, $disallowReplies, $important, $pin, $tags, $prefix, $move] = Utils::filterInput('topicId', 'name', 'disallow_replies', 'important', 'pin', 'tags', 'prefix', 'move');


        ForumTopicModel::getInstance()->adminEditTopic($topicId, $name, (is_null($disallowReplies) ? 0 : 1), (is_null($important) ? 0 : 1), (is_null($pin) ? 0 : 1), $prefix, $move);

        // Add tags


        $tags = explode(",", $tags);
        //Need to clear tag befor update
        ForumTopicModel::getInstance()->clearTag($topicId);
        foreach ($tags as $tag) {
            //Clean tag
            $tag = mb_strtolower(trim($tag));

            if (empty($tag)) {
                continue;
            }

            ForumTopicModel::getInstance()->addTag($tag, $topicId);
        }

        //Flash::send("success", LangManager::translate("core.toaster.success"),LangManager::translate("forum.topic.add.success"));

        Redirect::redirectPreviousRoute();
    }
}