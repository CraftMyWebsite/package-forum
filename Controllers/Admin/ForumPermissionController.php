<?php

namespace CMW\Controller\Forum\Admin;

use CMW\Controller\Users\UsersController;
use CMW\Controller\Users\UsersSessionsController;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Package\AbstractController;
use CMW\Model\Forum\ForumPermissionModel;
use CMW\Utils\Redirect;

/**
 * Class: @ForumPermissionController
 * @package Forum
 * @author Zomb
 * @version 0.0.1
 */
class ForumPermissionController extends AbstractController
{
    /**
     * @param string $permCode
     * @return bool
     * @desc used in public view : return true if user have permission to do something like "user_create_topic" find the list of permcodes at the bottom of ForumPermissionController.php
     */
    public function hasPermission(string $permCode): bool
    {
        $userId = UsersSessionsController::getInstance()->getCurrentUser()?->getId();

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
            Flash::send(Alert::ERROR, 'Forum', "L'accès au forum est reservé au membre, merci de vous connectez avant d'allez plus loin.");
            Redirect::redirect('login');
        }

        $userId = UsersSessionsController::getInstance()->getCurrentUser()?->getId();

        if (!ForumPermissionModel::getInstance()->hasForumPermission($userId, $permCode)) {
            Flash::send(Alert::ERROR, 'Forum', "Vous n'avez pas la permission de faire ceci");
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
        if ($permCode === 'user_create_topic_tag') {
            Flash::send(Alert::ERROR, 'Forum', 'Vous ne pouvez pas définir de tag, ce champ à été vidé');
        }
        if ($permCode === 'user_response_topic') {
            Flash::send(Alert::ERROR, 'Forum', "Vous n'avez pas la permission de répondre au topic");
        }
    }
}

/*
 * ---------- PERM CODE LIST ----------
 *
 * ----- ADMINISTRATION -----
 * 1, 'operator'
 * ----- MODERATION -----
 * 18, 'admin_change_topic_name'
 * 19, 'admin_change_topic_tag'
 * 20, 'admin_change_topic_prefix'
 * 21, 'admin_set_important'
 * 22, 'admin_set_pin'
 * 23, 'admin_set_closed'
 * 24, 'admin_move_topic'
 * 25, 'admin_bypass_forum_disallow_topics'
 * /*----- USER -----
 * 2, 'user_view_forum'
 * 3, 'user_view_topic'
 * 4, 'user_create_topic'
 * 5, 'user_create_topic_tag'
 * 6, 'user_create_pool'
 * 7, 'user_edit_topic'
 * 8, 'user_edit_tag'
 * 9, 'user_edit_pool'
 * 10, 'user_remove_topic'
 * 11, 'user_react_topic'
 * 12, 'user_change_react_topic'
 * 13, 'user_remove_react_topic'
 * 14, 'user_response_topic'
 * 15, 'user_response_react'
 * 16, 'user_response_change_react'
 * 17, 'user_response_remove_react'
 * 26, 'user_remove_response'
 * 27, 'user_edit_response'
 * 28, 'user_add_file'
 * 29, 'user_download_file'
 */
