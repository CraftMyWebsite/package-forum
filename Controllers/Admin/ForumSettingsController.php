<?php
namespace CMW\Controller\Forum\Admin;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Forum\ForumFeedbackModel;
use CMW\Model\Forum\ForumPrefixModel;
use CMW\Model\Forum\ForumSettingsModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class: @ForumSettingsController
 * @package Forum
 * @author Zomb
 * @version 0.0.1
 */
class ForumSettingsController extends AbstractController
{
    #[Link('/settings', Link::GET, [], '/cmw-admin/forum')]
    private function adminSettingsView(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'forum.settings');

        $needConnectUrl = ForumSettingsModel::getInstance()->getOptionValue('needConnectUrl');
        $needConnectText = ForumSettingsModel::getInstance()->getOptionValue('needConnectText');
        $blinkResponse = ForumSettingsModel::getInstance()->getOptionValue('blinkResponse');
        $responsePerPage = ForumSettingsModel::getInstance()->getOptionValue('responsePerPage');
        $topicPerPage = ForumSettingsModel::getInstance()->getOptionValue('topicPerPage');
        $iconNotRead = ForumSettingsModel::getInstance()->getOptionValue('IconNotRead');
        $iconNotReadColor = ForumSettingsModel::getInstance()->getOptionValue('IconNotReadColor');
        $iconImportant = ForumSettingsModel::getInstance()->getOptionValue('IconImportant');
        $iconImportantColor = ForumSettingsModel::getInstance()->getOptionValue('IconImportantColor');
        $iconPin = ForumSettingsModel::getInstance()->getOptionValue('IconPin');
        $iconPinColor = ForumSettingsModel::getInstance()->getOptionValue('IconPinColor');
        $iconClosed = ForumSettingsModel::getInstance()->getOptionValue('IconClosed');
        $iconClosedColor = ForumSettingsModel::getInstance()->getOptionValue('IconClosedColor');
        $prefixesModel = ForumPrefixModel::getInstance();
        $feedbackModel = ForumFeedbackModel::getInstance();

        View::createAdminView('Forum', 'settings')
            ->addVariableList(['prefixesModel' => $prefixesModel, 'feedbackModel' => $feedbackModel, 'blinkResponse' => $blinkResponse, 'needConnectUrl' => $needConnectUrl, 'needConnectText' => $needConnectText, 'topicPerPage' => $topicPerPage, 'responsePerPage' => $responsePerPage, 'iconNotRead' => $iconNotRead, 'iconImportant' => $iconImportant, 'iconPin' => $iconPin, 'iconClosed' => $iconClosed, 'iconNotReadColor' => $iconNotReadColor, 'iconImportantColor' => $iconImportantColor, 'iconPinColor' => $iconPinColor, 'iconClosedColor' => $iconClosedColor])
            ->addScriptBefore('Admin/Resources/Vendors/Tinymce/tinymce.min.js', 'Admin/Resources/Vendors/Tinymce/Config/medium.js')
            ->addStyle('Admin/Resources/Assets/Css/simple-datatables.css')
            ->addScriptAfter('Admin/Resources/Vendors/Simple-datatables/simple-datatables.js',
                'Admin/Resources/Vendors/Simple-datatables/config-datatables.js')
            ->view();
    }

    #[NoReturn]
    #[Link('/settings/applyicons', Link::POST, [], '/cmw-admin/forum')]
    private function settingsIconsPost(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'forum.settings');

        $iconNotRead = filter_input(INPUT_POST, 'icon_notRead');
        $iconNotReadColor = filter_input(INPUT_POST, 'icon_notRead_color');
        $iconImportant = filter_input(INPUT_POST, 'icon_important');
        $iconImportantColor = filter_input(INPUT_POST, 'icon_important_color');
        $iconPin = filter_input(INPUT_POST, 'icon_pin');
        $iconPinColor = filter_input(INPUT_POST, 'icon_pin_color');
        $iconClosed = filter_input(INPUT_POST, 'icon_closed');
        $iconClosedColor = filter_input(INPUT_POST, 'icon_closed_color');

        ForumSettingsModel::getInstance()->updateIcons($iconNotRead, $iconImportant, $iconPin, $iconClosed, $iconNotReadColor, $iconImportantColor, $iconPinColor, $iconClosedColor);

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/settings/general', Link::POST, [], '/cmw-admin/forum')]
    private function settingsResponsePerPagePost(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'forum.settings');

        $responsePerPage = filter_input(INPUT_POST, 'responsePerPage');
        $topicPerPage = filter_input(INPUT_POST, 'topicPerPage');
        $needConnectText = filter_input(INPUT_POST, 'needConnectText');

        $needConnectUrl = isset($_POST['needConnectUrl']) ? 1 : 0;
        $blinkResponse = isset($_POST['blinkResponse']) ? 1 : 0;

        ForumSettingsModel::getInstance()->updatePerPage($responsePerPage, $topicPerPage);
        ForumSettingsModel::getInstance()->updateNeedConnect($needConnectUrl, $needConnectText);
        ForumSettingsModel::getInstance()->updateBlinkResponse($blinkResponse);

        Flash::send(Alert::SUCCESS, 'Forum', 'Paramètres mis à jour avec succès');

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/settings/addprefix', Link::POST, [], '/cmw-admin/forum')]
    private function settingsAddPrefixPost(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'forum.settings');

        [$name, $color, $textColor, $description] = Utils::filterInput('prefixName', 'prefixColor', 'prefixTextColor', 'prefixDescription');

        ForumPrefixModel::getInstance()->createPrefix($name, $color, $textColor, $description);

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/settings/editprefix', Link::POST, [], '/cmw-admin/forum')]
    private function settingsEditPrefixPost(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'forum.settings');

        [$id, $name, $color, $textColor, $description] = Utils::filterInput('prefixId', 'prefixName', 'prefixColor', 'prefixTextColor', 'prefixDescription');

        ForumPrefixModel::getInstance()->editPrefix($id, $name, $color, $textColor, $description);

        Redirect::redirectPreviousRoute();
    }

    #[Link('/settings/deleteprefix/:prefixId', Link::GET, [], '/cmw-admin/forum')]
    private function settingsDeletePrefix(string $prefixId): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'forum.settings');
        if (ForumPrefixModel::getInstance()->deletePrefix($prefixId)) {
            Flash::send('success', LangManager::translate('core.toaster.success'),
                "C'est chao");

            Redirect::redirectPreviousRoute();
        }
    }

    #[NoReturn]
    #[Link('/settings/addreaction', Link::POST, [], '/cmw-admin/forum')]
    private function settingsAddReactionPost(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'forum.settings');
        [$name] = Utils::filterInput('name');
        $image = $_FILES['image'];

        ForumFeedbackModel::getInstance()->createFeedback($image, $name);

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/settings/editreaction', Link::POST, [], '/cmw-admin/forum')]
    private function settingsEditReactionPost(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'forum.settings');
        [$name, $id] = Utils::filterInput('name', 'id');
        $image = $_FILES['image'];

        ForumFeedbackModel::getInstance()->editFeedback($image, $name, $id);

        Redirect::redirectPreviousRoute();
    }

    #[Link('/settings/deletereaction/:reactionId', Link::GET, [], '/cmw-admin/forum')]
    private function settingsDeleteReaction(int $reactionId): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'forum.settings');
        if (ForumFeedbackModel::getInstance()->removeFeedback($reactionId)) {
            Flash::send('success', LangManager::translate('core.toaster.success'),
                "C'est chao");

            Redirect::redirectPreviousRoute();
        }
    }
}
