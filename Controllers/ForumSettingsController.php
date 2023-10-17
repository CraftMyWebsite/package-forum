<?php
namespace CMW\Controller\Forum;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Requests\Request;
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
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ForumSettingsController extends AbstractController {
    #[Link("/settings", Link::GET, [], "/cmw-admin/forum")]
    public function adminSettingsView(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.categories.list");

        $responsePerPage = ForumSettingsModel::getInstance()->getOptionValue("responsePerPage");
        $iconNotRead = ForumSettingsModel::getInstance()->getOptionValue("IconNotRead");
        $iconImportant = ForumSettingsModel::getInstance()->getOptionValue("IconImportant");
        $iconPin = ForumSettingsModel::getInstance()->getOptionValue("IconPin");
        $iconClosed = ForumSettingsModel::getInstance()->getOptionValue("IconClosed");
        $prefixesModel = ForumPrefixModel::getInstance();
        $feedbackModel = ForumFeedbackModel::getInstance();

        View::createAdminView("Forum", "settings")
            ->addVariableList(["prefixesModel" => $prefixesModel, "feedbackModel" => $feedbackModel, "responsePerPage" => $responsePerPage, "iconNotRead" => $iconNotRead, "iconImportant" => $iconImportant, "iconPin" => $iconPin, "iconClosed" => $iconClosed])
            ->addStyle("Admin/Resources/Vendors/Simple-datatables/style.css","Admin/Resources/Assets/Css/Pages/simple-datatables.css")
            ->addScriptAfter("Admin/Resources/Vendors/Simple-datatables/Umd/simple-datatables.js","Admin/Resources/Assets/Js/Pages/simple-datatables.js")
            ->view();
    }

    #[NoReturn] #[Link("/settings/applyicons", Link::POST, [], "/cmw-admin/forum")]
    private function settingsIconsPost(): void
        {
            UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.categories.list");

            $iconNotRead = filter_input(INPUT_POST, "icon_notRead");
            $iconImportant = filter_input(INPUT_POST, "icon_important");
            $iconPin = filter_input(INPUT_POST, "icon_pin");
            $iconClosed = filter_input(INPUT_POST, "icon_closed");

            ForumSettingsModel::getInstance()->updateIcons($iconNotRead,$iconImportant,$iconPin,$iconClosed);

            Redirect::redirectPreviousRoute();
        }

    #[NoReturn] #[Link("/settings/responsePerPage", Link::POST, [], "/cmw-admin/forum")]
    private function settingsResponsePerPagePost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.categories.list");

        $responsePerPage = filter_input(INPUT_POST, "responsePerPage");

        ForumSettingsModel::getInstance()->updateResponsePerPage($responsePerPage);

        Flash::send(Alert::SUCCESS, "Forum", "Les page affiche désormais " .$responsePerPage." réponses");

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link("/settings/addprefix", Link::POST, [], "/cmw-admin/forum")]
    private function settingsAddPrefixPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.categories.list");

        [$name, $color, $textColor, $description] = Utils::filterInput("prefixName", "prefixColor", "prefixTextColor", "prefixDescription");

        ForumPrefixModel::getInstance()->createPrefix($name, $color, $textColor, $description);

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link("/settings/editprefix", Link::POST, [], "/cmw-admin/forum")]
    private function settingsEditPrefixPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.categories.list");

        [$id, $name, $color, $textColor, $description] = Utils::filterInput("prefixId", "prefixName", "prefixColor", "prefixTextColor", "prefixDescription");

        ForumPrefixModel::getInstance()->editPrefix($id, $name, $color, $textColor, $description);

        Redirect::redirectPreviousRoute();
    }

    #[Link("/settings/deleteprefix/:prefixId", Link::GET, [], "/cmw-admin/forum")]
    public function settingsDeletePrefix(Request $request, string $prefixId): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.categories.list");
        if (ForumPrefixModel::getInstance()->deletePrefix($prefixId)) {

            Flash::send("success", LangManager::translate("core.toaster.success"),
                "C'est chao");

            Redirect::redirectPreviousRoute();
        }
    }

    #[NoReturn] #[Link("/settings/addreaction", Link::POST, [], "/cmw-admin/forum")]
    private function settingsAddReactionPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.categories.list");
        [$name] = Utils::filterInput("name");
        $image = $_FILES['image'];

        ForumFeedbackModel::getInstance()->createFeedback($image, $name);

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link("/settings/editreaction", Link::POST, [], "/cmw-admin/forum")]
    private function settingsEditReactionPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.categories.list");
        [$name, $id] = Utils::filterInput("name", "id");
        $image = $_FILES['image'];

        ForumFeedbackModel::getInstance()->editFeedback($image, $name, $id);

        Redirect::redirectPreviousRoute();
    }

    #[Link("/settings/deletereaction/:reactionId", Link::GET, [], "/cmw-admin/forum")]
    private function settingsDeleteReaction(Request $request, int $reactionId): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "forum.categories.list");
        if (ForumFeedbackModel::getInstance()->removeFeedback($reactionId)) {

            Flash::send("success", LangManager::translate("core.toaster.success"),
                "C'est chao");

            Redirect::redirectPreviousRoute();
        }
    }
}