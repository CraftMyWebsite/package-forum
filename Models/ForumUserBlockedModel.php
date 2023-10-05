<?php

namespace CMW\Model\Forum;

use CMW\Entity\Forum\ForumUserBlockedEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Model\Users\UsersModel;

/**
 * Class: @ForumUserBlockedModel
 * @package Forum
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */

class ForumUserBlockedModel extends AbstractModel
{
    public function getUserBlockedByUserId(int $userId): ?ForumUserBlockedEntity
    {
        $sql = "SELECT * FROM cmw_forums_users_blocked WHERE user_id = :user_id";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("user_id" => $userId))) {
            return null;
        }

        $res = $res->fetch();

        $user = UsersModel::getInstance()->getUserById($res["user_id"]);

        return new ForumUserBlockedEntity(
            $res["forums_users_blocked_id"],
            $user,
            $res["forum_user_is_blocked"],
            $res["forum_blocked_reason"] ?? "",
            $res["forum_blocked_updated"]
        );
    }

    public function blockUser(int $userId, string $reason): void
    {
        $data = array(
            "user_id" => $userId,
            "forum_blocked_reason" => $reason,
        );

        $sql = "UPDATE cmw_forums_users_blocked SET forum_user_is_blocked = 1, forum_blocked_reason =:forum_blocked_reason WHERE user_id = :user_id";

        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        $req->execute($data);
    }

    public function unblockUser(int $userId, string $reason): void
    {
        $data = array(
            "user_id" => $userId,
            "forum_blocked_reason" => $reason,
        );

        $sql = "UPDATE cmw_forums_users_blocked SET forum_user_is_blocked = 0, forum_blocked_reason =:forum_blocked_reason WHERE user_id = :user_id";

        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        $req->execute($data);
    }

    public function addDefaultBlockOnRegister(int $userId): void
    {
        $data = array(
            "user_id" => $userId
        );

        $sql = "INSERT INTO `cmw_forums_users_blocked`(`user_id`) VALUES (:user_id)";

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        $req->execute($data);

    }

}