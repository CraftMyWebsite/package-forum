<?php

namespace CMW\Model\Forum;

use CMW\Entity\Forum\topicEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Model\Users\UsersModel;


/**
 * Class: @TopicModel
 * @package Forum
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class TopicModel extends DatabaseManager
{

    private UsersModel $userModel;
    private ForumModel $forumModel;

    public function __construct()
    {

        $this->userModel = new UsersModel();
        $this->forumModel = new ForumModel();

    }

    public function getTopicBySlug(string $slug): ?topicEntity
    {
        $sql = "SELECT forum_topic_id FROM cmw_forums_topics WHERE forum_topic_slug = :topic_slug";

        $db = self::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("topic_slug" => $slug))) {
            return null;
        }

        $res = $res->fetch();

        if (!$res) {
            return null;
        }

        return $this->getTopicById($res["forum_topic_id"]);
    }

    public function getTopicById(int $id): ?topicEntity
    {
        $sql = "SELECT * FROM cmw_forums_topics WHERE forum_topic_id = :topic_id";

        $db = self::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("topic_id" => $id))) {
            return null;
        }

        $res = $res->fetch();

        if (!$res) {
            return null;
        }

        $user = $this->userModel->getUserById($res["user_id"]);
        $forum = $this->forumModel->getForumById($res["forum_id"]);

        if (is_null($forum) || is_null($user?->getUsername())) {
            return null;
        }

        return new topicEntity(
            $res["forum_topic_id"],
            $res["forum_topic_name"],
            $res["forum_topic_content"] ?? "",
            $res["forum_topic_disallow_replies"],
            $res["forum_topic_important"],
            $res["forum_topic_slug"],
            $res["forum_topic_pinned"],
            $forum,
            $user
        );
    }

    /**
     * @return \CMW\Entity\Forum\topicEntity[]
     */
    public function getTopicByForum(int $id): array
    {

        $sql = "SELECT forum_topic_id FROM cmw_forums_topics WHERE forum_id = :forum_id ORDER BY forum_topic_pinned DESC";
        $db = self::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("forum_id" => $id))) {
            return array();
        }

        $toReturn = array();

        while ($top = $res->fetch()) {
            $topic = $this->getTopicById($top["forum_topic_id"]);
            if (!is_null($topic)) {
                $toReturn[] = $topic;
            }
        }

        return $toReturn;
    }

    public function createTopic(string $name, string $content, int $userId, int $forumId): ?topicEntity
    {

        $var = array(
            "topic_name" => $name,
            "topic_content" => $content,
            "topic_slug" => "NOT DEFINED",
            "user_id" => $userId,
            "forum_id" => $forumId
        );

        $sql = "INSERT INTO cmw_forums_topics (forum_topic_name, forum_topic_content, forum_topic_slug, user_id, forum_id) 
                VALUES (:topic_name, :topic_content, :topic_slug, :user_id, :forum_id)";

        $db = self::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($var)) {
            $this->setTopicSlug($db->lastInsertId("forum_topic_id"), $name);
            return $this->getTopicById($db->lastInsertId());
        }

        return null;
    }


    private function setTopicSlug(int $id, string $name): void
    {
        $slug = $this->forumModel->generateSlug($id, $name);

        $data = array(
            "topic_slug" => $slug,
            "topic_id" => $id,
        );

        $sql = "UPDATE cmw_forums_topics SET forum_topic_slug = :topic_slug WHERE forum_topic_id = :topic_id";

        $db = self::getInstance();

        $req = $db->prepare($sql);

        $req->execute($data);
    }


    public function pinTopic(topicEntity $topic): bool
    {
        $data = array(
            "topic_id" => $topic->getId(),
            "status" => $topic->isPinned() ? 0 : 1,
        );

        $sql = "UPDATE cmw_forums_topics SET forum_topic_pinned = :status WHERE forum_topic_id = :topic_id";

        $db = self::getInstance();

        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            return $req->rowCount() === 1;
        }
        return false;
    }
}