<?php
namespace CMW\Controller\Forum;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Views\View;
use CMW\Model\Forum\ForumCategoryModel;
use CMW\Manager\Router\Link;
use CMW\Model\Forum\ForumModel;
use CMW\Model\Forum\ForumPermissionRoleModel;
use CMW\Model\Forum\ForumResponseModel;
use CMW\Model\Forum\ForumSettingsModel;
use CMW\Model\Forum\ForumTopicModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class: @ForumTopicController
 * @package Forum
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ForumTopicController extends AbstractController
{
    #[Link("/topics", Link::GET, [], "/cmw-admin/forum")]
    public function adminTopicView(): void
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

        View::createAdminView("Forum", "topics")
            ->addVariableList(["forumModel" => $forumModel, "categoryModel" => $categoryModel,"topicModel" => $topicModel, "responseModel" => $responseModel,"iconNotRead" => $iconNotRead, "iconImportant" => $iconImportant, "iconPin" => $iconPin, "iconClosed" => $iconClosed, "ForumRoles" => $ForumRoles])
            ->addStyle("Admin/Resources/Vendors/Simple-datatables/style.css","Admin/Resources/Assets/Css/Pages/simple-datatables.css")
            ->addScriptAfter("Admin/Resources/Vendors/Simple-datatables/Umd/simple-datatables.js","Admin/Resources/Assets/Js/Pages/simple-datatables.js")
            ->view();
    }

    #[NoReturn] #[Link("/topics", Link::POST, ['.*?'], "/cmw-admin/forum")]
    public function adminEditTopicPost(): void
    {
        [$topicId, $name, $disallowReplies, $important, $pin, $tags, $prefix, $move] = Utils::filterInput('topicId', 'name', 'disallow_replies', 'important', 'pin', 'tags', 'prefix', 'move');

        ForumTopicModel::getInstance()->adminEditTopic($topicId, $name, (is_null($disallowReplies) ? 0 : 1), (is_null($important) ? 0 : 1), (is_null($pin) ? 0 : 1), $prefix, $move);

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