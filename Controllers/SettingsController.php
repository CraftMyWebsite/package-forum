<?php


namespace CMW\Controller\Forum;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Requests\Request;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Forum\FeedbackModel;
use CMW\Model\Forum\PrefixModel;
use CMW\Model\Forum\SettingsModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;

class SettingsController extends AbstractController {
    #[Link("/settings", Link::GET, [], "/cmw-admin/forum")]
    public function adminSettingsView(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.categories.list");


        $iconNotRead = SettingsModel::getInstance()->getOptionValue("IconNotRead");
        $iconImportant = SettingsModel::getInstance()->getOptionValue("IconImportant");
        $iconPin = SettingsModel::getInstance()->getOptionValue("IconPin");
        $iconClosed = SettingsModel::getInstance()->getOptionValue("IconClosed");
        $prefixesModel = prefixModel::getInstance();
        $feedbackModel = feedbackModel::getInstance();

        View::createAdminView("Forum", "settings")
            ->addVariableList(["prefixesModel" => $prefixesModel, "feedbackModel" => $feedbackModel, "iconNotRead" => $iconNotRead, "iconImportant" => $iconImportant, "iconPin" => $iconPin, "iconClosed" => $iconClosed])
            ->addStyle("Admin/Resources/Vendors/Simple-datatables/style.css","Admin/Resources/Assets/Css/Pages/simple-datatables.css")
            ->addScriptAfter("Admin/Resources/Vendors/Simple-datatables/Umd/simple-datatables.js","Admin/Resources/Assets/Js/Pages/simple-datatables.js")
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

    #[Link("/settings/addprefix", Link::POST, [], "/cmw-admin/forum")]
    private function settingsAddPrefixPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.categories.list");

        [$name, $color, $textColor, $description] = Utils::filterInput("prefixName", "prefixColor", "prefixTextColor", "prefixDescription");

        PrefixModel::getInstance()->createPrefix($name, $color, $textColor, $description);

        Redirect::redirectPreviousRoute();
    }

    #[Link("/settings/editprefix", Link::POST, [], "/cmw-admin/forum")]
    private function settingsEditPrefixPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.categories.list");

        [$id, $name, $color, $textColor, $description] = Utils::filterInput("prefixId", "prefixName", "prefixColor", "prefixTextColor", "prefixDescription");

        PrefixModel::getInstance()->editPrefix($id, $name, $color, $textColor, $description);

        Redirect::redirectPreviousRoute();
    }

    #[Link("/settings/deleteprefix/:prefixId", Link::GET, [], "/cmw-admin/forum")]
    public function settingsDeletePrefix(Request $request, string $prefixId): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.categories.list");
        if (PrefixModel::getInstance()->deletePrefix($prefixId)) {

            Flash::send("success", LangManager::translate("core.toaster.success"),
                "C'est chao");

            Redirect::redirectPreviousRoute();
        }
    }

    #[Link("/settings/addreaction", Link::POST, [], "/cmw-admin/forum")]
    private function settingsAddReactionPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.categories.list");

        [$title] = Utils::filterInput("title");

        FeedbackModel::getInstance()->createFeedback($title);

        Redirect::redirectPreviousRoute();
    }
}