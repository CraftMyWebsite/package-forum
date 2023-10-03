<?php
namespace CMW\Controller\Forum;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Package\AbstractController;
use CMW\Model\Forum\ForumPermissionModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Redirect;

/**
 * Class: @ForumPermissionController
 * @package Forum
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ForumPermissionController extends AbstractController
{
    /**
     * @param string $permCode
     * @return bool
     * @desc used in public view : return true if user have permission to do something like "user_create_topic"
     */
    public function hasPermission(string $permCode) :bool
    {
        $userId = UsersModel::getCurrentUser()->getId();

        if (!ForumPermissionModel::getInstance()->hasForumPermission($userId, $permCode)) {
            return false;
        }
        return true;
    }

    /**
     * @param string $permCode
     * @return void
     * @desc used in controller : Redirect to previous route if the user don't have the permission
     */
    public function redirectIfNotHavePermissions(string $permCode): void
    {
        if (!UsersController::isUserLogged()) {
            Flash::send(Alert::ERROR, "Forum", "L'accès au forum est reservé au membre, merci de vous connectez avant d'allez plus loin.");
            Redirect::redirect('login');
        }

        $userId = UsersModel::getCurrentUser()->getId();

        if (!ForumPermissionModel::getInstance()->hasForumPermission($userId, $permCode)) {
            Flash::send(Alert::ERROR, "Forum", "Vous n'avez pas la permission de faire ceci");
            Redirect::redirectPreviousRoute();
        }
    }

    /**
     * @param string $permCode
     * @return void
     * @desc used in controller : Just alert the user if doesn't have permission, action required in addition to this, to properly process the request
     */
    public function alertNotHavePermissions(string $permCode): void
    {
        if ($permCode === "user_create_topic_tag") {
            Flash::send(Alert::ERROR, "Forum", "Vous ne pouvez pas définir de tag, ce champ à été vidé");
        }
        if ($permCode === "user_response_topic") {
            Flash::send(Alert::ERROR, "Forum", "Vous n'avez pas la permission de répondre au topic");
        }
    }
}