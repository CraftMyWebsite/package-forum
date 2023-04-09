<?php

namespace CMW\Model\Forum;

use CMW\Entity\Forum\TopicEntity;
use CMW\Entity\Forum\TopicTagEntity;
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

    public function getTopicBySlug(string $slug): ?TopicEntity
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

    public function getTopicById(int $id): ?TopicEntity
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

        if (is_null($forum) || is_null($user?->getPseudo())) {
            return null;
        }

        return new TopicEntity(
            $res["forum_topic_id"],
            $res["forum_topic_name"],
            $res["forum_topic_slug"],
            $res["forum_topic_content"] ?? "",
            $res["forum_topic_created"],
            $res["forum_topic_updated"],
            $res["forum_topic_pinned"],
            $res["forum_topic_disallow_replies"],
            $res["forum_topic_important"],
            $user,
            $forum,
            $this->getTagsForTopicById($res["forum_topic_id"])
        );
    }

    /**
     * @return \CMW\Entity\Forum\TopicEntity[]
     */
    public function getTopicByForum(int $id): array
    {

        $sql = "SELECT forum_topic_id FROM cmw_forums_topics WHERE forum_id = :forum_id 
                                             ORDER BY forum_topic_pinned DESC, forum_topic_important DESC ";
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

    public function createTopic(string $name, string $content, int $userId, int $forumId, int $disallowReplies, int $important, int $pin): ?TopicEntity
    {

        $var = array(
            "topic_name" => $name,
            "topic_content" => $content,
            "topic_slug" => "NOT DEFINED",
            "disallow_replies" => $disallowReplies,
            "important" => $important,
            "pin" => $pin,
            "user_id" => $userId,
            "forum_id" => $forumId
        );

        $sql = "INSERT INTO cmw_forums_topics (forum_topic_name, forum_topic_content, forum_topic_slug, 
                               forum_topic_disallow_replies, forum_topic_important, forum_topic_pinned, user_id, forum_id) 
                VALUES (:topic_name, :topic_content, :topic_slug, :disallow_replies, :important, :pin, :user_id, :forum_id)";

        $db = self::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($var)) {
            $id = $db->lastInsertId();

            $this->setTopicSlug($id, $name);
            return $this->getTopicById($id);
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


    public function pinTopic(TopicEntity $topic): bool
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

    public function DisallowReplies(TopicEntity $topic): bool
    {
        $data = array(
            "topic_id" => $topic->getId(),
            "status" => $topic->isDisallowReplies() ? 0 : 1,
        );

        $sql = "UPDATE cmw_forums_topics SET forum_topic_disallow_replies = :status WHERE forum_topic_id = :topic_id";

        $db = self::getInstance();

        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            return $req->rowCount() === 1;
        }
        return false;
    }

    public function ImportantTopic(TopicEntity $topic): bool
    {
        $data = array(
            "topic_id" => $topic->getId(),
            "status" => $topic->isImportant() ? 0 : 1,
        );

        $sql = "UPDATE cmw_forums_topics SET forum_topic_important = :status WHERE forum_topic_id = :topic_id";

        $db = self::getInstance();

        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            return $req->rowCount() === 1;
        }
        return false;
    }

    public function deleteTopic(int $topicId): bool
    {
        $sql = "DELETE FROM cmw_forums_topics WHERE forum_topic_id = :topic_id";

        $db = self::getInstance();

        return $db->prepare($sql)->execute(array("topic_id" => $topicId));
    }

    public function addTag(string $content, int $topicId): ?TopicTagEntity
    {
        $sql = "INSERT INTO cmw_forums_topics_tags (forums_topics_tags_content, forums_topics_tags_topic_id) 
                VALUES (:content, :topic_id)";

        $db = self::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute(array("content" => $content, "topic_id" => $topicId))) {
            return $this->getTagById($db->lastInsertId());
        }

        return null;
    }

    public function getTagById(int $tagId): ?TopicTagEntity
    {

        $sql = "SELECT * FROM cmw_forums_topics_tags WHERE forums_topics_tags_id = :tag_id";

        $db = self::getInstance();
        $req = $db->prepare($sql);

        if (!$req->execute(array('tag_id' => $tagId))) {
            return null;
        }

        $res = $req->fetch();
        return new TopicTagEntity(
            $res['forums_topics_tags_id'],
            $res['forums_topics_tags_content']
        );
    }

    /**
     * @return \CMW\Entity\Forum\TopicTagEntity[]
     */
    public function getTags(): array
    {
        $sql = "SELECT forums_topics_tags_id FROM cmw_forums_topics_tags";

        $db = self::getInstance();

        $req = $db->query($sql);

        $toReturn = array();

        while ($data = $req->fetch()) {
            $toReturn[] = $this->getTagById($data["forums_topics_tags_id"]);
        }

        return $toReturn;
    }

    /**
     * @param int $topicId
     * @return \CMW\Entity\Forum\TopicTagEntity[]
     */
    public function getTagsForTopicById(int $topicId): array
    {
        $toReturn = array();

        $sql = "SELECT forums_topics_tags_id  FROM cmw_forums_topics_tags WHERE forums_topics_tags_topic_id = :topic_id";
        $db = self::getInstance();

        $req = $db->prepare($sql);

        if (!$req->execute(array("topic_id" => $topicId))) {
            return $toReturn;
        }

        while ($data = $req->fetch()) {
            $toReturn[] = $this->getTagById($data["forums_topics_tags_id"]);
        }

        return $toReturn;
    }

    public function editTag(int $tagId, string $content, int $topicId): ?TopicTagEntity
    {
        $var = array(
            "tag_id" => $tagId,
            "content" => $content,
            "topic_id" => $topicId
        );

        $sql = "UPDATE cmw_forums_topics_tags SET forums_topics_tags_content = :content, 
                                  forums_topics_tags_topic_id = :topic_id WHERE forums_topics_tags_id = :tag_id";
        $db = self::getInstance();
        $req = $db->prepare($sql);

        if (!$req->execute($var)) {
            return null;
        }

        return new TopicTagEntity(
            $tagId,
            $content,
        );
    }

    public function deleteTag(int $tagId): bool
    {
        $sql = "DELETE FROM cmw_forums_topics_tags WHERE forums_topics_tags_id = :tag_id";

        $db = self::getInstance();

        return $db->prepare($sql)->execute(array("tag_id" => $tagId));
    }
}