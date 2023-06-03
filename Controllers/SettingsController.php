<?php


namespace CMW\Controller\Forum;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Forum\SettingsModel;
use CMW\Utils\Redirect;
use CMW\Utils\Website;

class SettingsController extends AbstractController {
    #[Link("/settings", Link::GET, [], "/cmw-admin/forum")]
    public function adminSettingsView(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.categories.list");


        $iconNotRead = SettingsModel::getInstance()->getOptionValue("IconNotRead");
        $iconImportant = SettingsModel::getInstance()->getOptionValue("IconImportant");
        $iconPin = SettingsModel::getInstance()->getOptionValue("IconPin");
        $iconClosed = SettingsModel::getInstance()->getOptionValue("IconClosed");

        View::createAdminView("Forum", "settings")
            ->addVariableList(["iconNotRead" => $iconNotRead, "iconImportant" => $iconImportant, "iconPin" => $iconPin, "iconClosed" => $iconClosed,])
            ->view();
    }

    #[Link("/settings/applyicons", Link::POST, [], "/cmw-admin/forum")]
    private function settingsIconsPost(): void
        {
            UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.categories.list");

            $iconNotRead = filter_input(INPUT_POST, "icon_notRead");
            $iconImportant = filter_input(INPUT_POST, "icon_important");
            $iconPin = filter_input(INPUT_POST, "icon_pin");
            $iconClosed = filter_input(INPUT_POST, "icon_closed");

            SettingsModel::getInstance()->updateIcons($iconNotRead,$iconImportant,$iconPin,$iconClosed);

            Redirect::redirectPreviousRoute();
        }
}