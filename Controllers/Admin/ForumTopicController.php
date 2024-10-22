<?php

namespace CMW\Controller\Forum\Admin;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Forum\ForumCategoryModel;
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
 * @author Zomb
 * @version 0.0.1
 */
class ForumTopicController extends AbstractController
{
    #[Link('/topics', Link::GET, [], '/cmw-admin/forum')]
    private function adminTopicView(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'forum.topics');

        $forumModel = forumModel::getInstance();
        $categoryModel = ForumCategoryModel::getInstance();
        $topicModel = ForumTopicModel::getInstance();
        $responseModel = ForumResponseModel::getInstance();
        $iconNotRead = ForumSettingsModel::getInstance()->getOptionValue('IconNotRead');
        $iconNotReadColor = ForumSettingsModel::getInstance()->getOptionValue('IconNotReadColor');
        $iconImportant = ForumSettingsModel::getInstance()->getOptionValue('IconImportant');
        $iconImportantColor = ForumSettingsModel::getInstance()->getOptionValue('IconImportantColor');
        $iconPin = ForumSettingsModel::getInstance()->getOptionValue('IconPin');
        $iconPinColor = ForumSettingsModel::getInstance()->getOptionValue('IconPinColor');
        $iconClosed = ForumSettingsModel::getInstance()->getOptionValue('IconClosed');
        $iconClosedColor = ForumSettingsModel::getInstance()->getOptionValue('IconClosedColor');
        $ForumRoles = ForumPermissionRoleModel::getInstance()->getRole();

        View::createAdminView('Forum', 'topics')
            ->addVariableList(['forumModel' => $forumModel, 'categoryModel' => $categoryModel, 'topicModel' => $topicModel, 'responseModel' => $responseModel, 'iconNotRead' => $iconNotRead, 'iconImportant' => $iconImportant, 'iconPin' => $iconPin, 'iconClosed' => $iconClosed, 'ForumRoles' => $ForumRoles, 'iconNotReadColor' => $iconNotReadColor, 'iconImportantColor' => $iconImportantColor, 'iconPinColor' => $iconPinColor, 'iconClosedColor' => $iconClosedColor])
            ->addStyle('Admin/Resources/Assets/Css/simple-datatables.css')
            ->addScriptAfter('Admin/Resources/Vendors/Simple-datatables/simple-datatables.js',
                'Admin/Resources/Vendors/Simple-datatables/config-datatables.js')
            ->view();
    }

    #[NoReturn]
    #[Link('/topics', Link::POST, ['.*?'], '/cmw-admin/forum')]
    private function adminEditTopicPost(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'forum.topics');

        [$topicId, $name, $disallowReplies, $important, $pin, $tags, $prefix, $move] = Utils::filterInput('topicId', 'name', 'disallow_replies', 'important', 'pin', 'tags', 'prefix', 'move');

        ForumTopicModel::getInstance()->adminEditTopic($topicId, $name, (is_null($disallowReplies) ? 0 : 1), (is_null($important) ? 0 : 1), (is_null($pin) ? 0 : 1), $prefix, $move);

        $tags = explode(',', $tags);
        // Need to clear tag befor update
        ForumTopicModel::getInstance()->clearTag($topicId);
        foreach ($tags as $tag) {
            // Clean tag
            $tag = mb_strtolower(trim($tag));

            if (empty($tag)) {
                continue;
            }

            ForumTopicModel::getInstance()->addTag($tag, $topicId);
        }

        // Flash::send("success", LangManager::translate("core.toaster.success"),LangManager::translate("forum.topic.add.success"));

        Redirect::redirectPreviousRoute();
    }
}
