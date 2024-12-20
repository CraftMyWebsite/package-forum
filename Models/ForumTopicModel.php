<?php

namespace CMW\Model\Forum;

use CMW\Entity\Forum\ForumCategoryEntity;
use CMW\Entity\Forum\ForumTopicEntity;
use CMW\Entity\Forum\ForumTopicTagEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Editor\EditorManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Client;
use CMW\Utils\Website;
use PDO;

/**
 * Class: @ForumTopicModel
 * @package Forum
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ForumTopicModel extends AbstractModel
{
    private UsersModel $userModel;
    private ForumModel $forumModel;

    public function __construct()
    {
        $this->userModel = new UsersModel();
        $this->forumModel = new ForumModel();
    }

    /**
     * @return array
     */
    public function getTopic(): array
    {
        $sql = 'SELECT forum_topic_id FROM cmw_forums_topics';
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return array();
        }

        $toReturn = array();

        while ($topic = $res->fetch()) {
            $toReturn[] = $this->getTopicById($topic['forum_topic_id']);
        }

        return $toReturn;
    }

    /**
     * @param string $slug
     * @return \CMW\Entity\Forum\ForumTopicEntity|null
     */
    public function getTopicBySlug(string $slug): ?ForumTopicEntity
    {
        $sql = 'SELECT forum_topic_id FROM cmw_forums_topics WHERE forum_topic_slug = :topic_slug';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array('topic_slug' => $slug))) {
            return null;
        }

        $res = $res->fetch();

        if (!$res) {
            return null;
        }

        return $this->getTopicById($res['forum_topic_id']);
    }

    /**
     * @param string $search
     * @return \CMW\Entity\Forum\ForumTopicEntity|null
     */
    public function getTopicByResearch(string $search): array
    {
        $sql = 'SELECT * FROM cmw_forums_topics WHERE MATCH (forum_topic_content, forum_topic_name) AGAINST (:search)';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array('search' => $search))) {
            return [];
        }

        $toReturn = array();

        while ($top = $res->fetch()) {
            $topic = $this->getTopicById($top['forum_topic_id']);
            if (!is_null($topic)) {
                $toReturn[] = $topic;
            }
        }

        return $toReturn;
    }

    /**
     * @param int $id
     * @return \CMW\Entity\Forum\ForumTopicEntity|null
     */
    public function getTopicById(int $id): ?ForumTopicEntity
    {
        $sql = 'SELECT * FROM cmw_forums_topics WHERE forum_topic_id = :topic_id';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array('topic_id' => $id))) {
            return null;
        }

        $res = $res->fetch();

        if (!$res) {
            return null;
        }

        $user = $this->userModel->getUserById($res['user_id']);
        $forum = $this->forumModel->getForumById($res['forum_id']);

        if (is_null($forum) || is_null($user?->getPseudo())) {
            return null;
        }

        return new ForumTopicEntity(
            $res['forum_topic_id'],
            $res['forum_topic_name'],
            $res['forum_topic_prefix'] ?? '',
            $res['forum_topic_slug'],
            $res['forum_topic_content'] ?? '',
            $res['forum_topic_is_trash'],
            $res['forum_topic_trash_reason'],
            $res['forum_topic_created'],
            $res['forum_topic_updated'],
            $res['forum_topic_pinned'],
            $res['forum_topic_disallow_replies'],
            $res['forum_topic_important'],
            $user,
            $forum,
            $this->getTagsForTopicById($res['forum_topic_id'])
        );
    }

    /**
     * @param int $prefixId
     * @return string
     * @desc get the Name of given PrefixId
     */
    public function getPrefixName(int $prefixId): string
    {
        $db = DatabaseManager::getInstance();
        $req = $db->prepare('SELECT forum_prefix_name FROM cmw_forums_prefixes WHERE forum_prefix_id = :prefix_id');
        $req->execute(array('prefix_id' => $prefixId));
        $prefixName = $req->fetch();

        return $prefixName['forum_prefix_name'];
    }

    /**
     * @param int $prefixId
     * @return string
     * @desc get the Color of given PrefixId
     */
    public function getPrefixColor(int $prefixId): string
    {
        $db = DatabaseManager::getInstance();
        $req = $db->prepare('SELECT forum_prefix_color FROM cmw_forums_prefixes WHERE forum_prefix_id = :prefix_id');
        $req->execute(array('prefix_id' => $prefixId));
        $prefixColor = $req->fetch();

        return $prefixColor['forum_prefix_color'];
    }

    /**
     * @param int $prefixId
     * @return string
     * @desc get the Text Color of given PrefixId
     */
    public function getPrefixTextColor(int $prefixId): string
    {
        $db = DatabaseManager::getInstance();
        $req = $db->prepare('SELECT forum_prefix_text_color FROM cmw_forums_prefixes WHERE forum_prefix_id = :prefix_id');
        $req->execute(array('prefix_id' => $prefixId));
        $prefixTextColor = $req->fetch();

        return $prefixTextColor['forum_prefix_text_color'];
    }

    /**
     * @return bool
     * @desc check if exist
     */
    public function checkViews(int $topicId, string $ip): bool
    {
        $sql = 'SELECT forum_topics_views_ip FROM cmw_forums_topics_views WHERE forum_topics_views_topic_id = :topicId AND forum_topics_views_ip = :ip';

        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if ($req->execute(array('topicId' => $topicId, 'ip' => $ip))) {
            return $req->rowCount() === 1;
        }
        return false;
    }

    /**
     * @return void
     * @desc add a view
     */
    public function addViews(int $topicId): void
    {
        $ip = Client::getIp();
        $sql = 'INSERT INTO cmw_forums_topics_views (forum_topics_views_topic_id, forum_topics_views_ip) VALUES (:topicId, :ip);';
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        $req->execute(array('topicId' => $topicId, 'ip' => $ip));
    }

    /**
     * @param int $topicId
     * @return string
     * @desc count number of views by topic id
     */
    public function countViews(int $topicId): mixed
    {
        $sql = 'SELECT COUNT(forum_topics_views_id) as count FROM cmw_forums_topics_views WHERE forum_topics_views_topic_id = :topic_id';
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array('topic_id' => $topicId))) {
            return 0;
        }

        return $res->fetch(0)['count'];
    }

    /**
     * @return \CMW\Entity\Forum\ForumTopicEntity[]
     */
    public function getTopicByForumAndOffset(int $id, int $offset, int $responsePerPage): array
    {
        $sql = 'SELECT forum_topic_id FROM cmw_forums_topics WHERE forum_id = :forum_id AND forum_topic_is_trash = 0
                                             ORDER BY forum_topic_pinned DESC, forum_topic_important DESC, forum_topic_created DESC LIMIT :responsePerPage OFFSET :offset';
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array('forum_id' => $id, 'offset' => $offset, 'responsePerPage' => $responsePerPage))) {
            return array();
        }

        $toReturn = array();

        while ($top = $res->fetch()) {
            $topic = $this->getTopicById($top['forum_topic_id']);
            if (!is_null($topic)) {
                $toReturn[] = $topic;
            }
        }

        return $toReturn;
    }

    /**
     * @param string $name
     * @param string $content
     * @param int $userId
     * @param int $forumId
     * @param int $disallowReplies
     * @param int $important
     * @param int $pin
     * @return \CMW\Entity\Forum\ForumTopicEntity|null
     */
    public function createTopic(string $name, string $content, int $userId, int $forumId, int $disallowReplies, int $important, int $pin): ?ForumTopicEntity
    {
        $var = array(
            'topic_name' => $name,
            'topic_content' => $content,
            'topic_slug' => 'NOT DEFINED',
            'disallow_replies' => $disallowReplies,
            'important' => $important,
            'pin' => $pin,
            'user_id' => $userId,
            'forum_id' => $forumId
        );

        $sql = 'INSERT INTO cmw_forums_topics (forum_topic_name, forum_topic_content, forum_topic_slug, 
                               forum_topic_disallow_replies, forum_topic_important, forum_topic_pinned, user_id, forum_id) 
                VALUES (:topic_name, :topic_content, :topic_slug, :disallow_replies, :important, :pin, :user_id, :forum_id)';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($var)) {
            $id = $db->lastInsertId();

            $this->setTopicSlug($id, $name);
            return $this->getTopicById($id);
        }

        return null;
    }

    /**
     * @param int $topicId
     * @param string $name
     * @param int $disallowReplies
     * @param int $important
     * @param int $pin
     * @param string $prefix
     * @param int $move
     * @return \CMW\Entity\Forum\ForumTopicEntity|null
     */
    public function adminEditTopic(int $topicId, string $name, int $disallowReplies, int $important, int $pin, string $prefix, int $move): ?ForumTopicEntity
    {
        if ($prefix === '') {
            $prefixReturn = null;
        } else {
            $prefixReturn = $prefix;
        }

        $var = array(
            'topicId' => $topicId,
            'name' => $name,
            'disallow_replies' => $disallowReplies,
            'important' => $important,
            'pin' => $pin,
            'prefix' => $prefixReturn,
            'move' => $move
        );

        $sql = 'UPDATE cmw_forums_topics SET 
            forum_topic_name = :name,
            forum_topic_disallow_replies = :disallow_replies,
            forum_topic_important = :important,
            forum_topic_pinned = :pin,
            forum_topic_prefix = :prefix,
            forum_id = :move
            WHERE forum_topic_id = :topicId';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($var)) {
            $this->setTopicSlug($topicId, $name);
        }

        return null;
    }

    /**
     * @param int $topicId
     * @param string $name
     * @param string $content
     * @return \CMW\Entity\Forum\ForumTopicEntity|null
     */
    public function authorEditTopic(int $topicId, string $name, string $content): ?ForumTopicEntity
    {
        $var = array(
            'topicId' => $topicId,
            'name' => $name,
            'content' => $content,
        );

        $sql = 'UPDATE cmw_forums_topics SET 
            forum_topic_name = :name,
            forum_topic_content = :content
            WHERE forum_topic_id = :topicId';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($var)) {
            $this->setTopicSlug($topicId, $name);
        }

        return null;
    }

    /**
     * @param int $id
     * @param string $name
     * @return void
     */
    private function setTopicSlug(int $id, string $name): void
    {
        $slug = $this->forumModel->generateSlug($id, $name);

        $data = array(
            'topic_slug' => $slug,
            'topic_id' => $id,
        );

        $sql = 'UPDATE cmw_forums_topics SET forum_topic_slug = :topic_slug WHERE forum_topic_id = :topic_id';

        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        $req->execute($data);
    }

    /**
     * @param \CMW\Entity\Forum\ForumTopicEntity $topic
     * @return bool
     */
    public function pinTopic(ForumTopicEntity $topic): bool
    {
        $data = array(
            'topic_id' => $topic->getId(),
            'status' => $topic->isPinned() ? 0 : 1,
        );

        $sql = 'UPDATE cmw_forums_topics SET forum_topic_pinned = :status WHERE forum_topic_id = :topic_id';

        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            return $req->rowCount() === 1;
        }
        return false;
    }

    /**
     * @param \CMW\Entity\Forum\ForumTopicEntity $topic
     * @return bool
     */
    public function DisallowReplies(ForumTopicEntity $topic): bool
    {
        $data = array(
            'topic_id' => $topic->getId(),
            'status' => $topic->isDisallowReplies() ? 0 : 1,
        );

        $sql = 'UPDATE cmw_forums_topics SET forum_topic_disallow_replies = :status WHERE forum_topic_id = :topic_id';

        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            return $req->rowCount() === 1;
        }
        return false;
    }

    /**
     * @param \CMW\Entity\Forum\ForumTopicEntity $topic
     * @return bool
     */
    public function ImportantTopic(ForumTopicEntity $topic): bool
    {
        $data = array(
            'topic_id' => $topic->getId(),
            'status' => $topic->isImportant() ? 0 : 1,
        );

        $sql = 'UPDATE cmw_forums_topics SET forum_topic_important = :status WHERE forum_topic_id = :topic_id';

        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            return $req->rowCount() === 1;
        }
        return false;
    }

    /**
     * @param int $topicId
     * @return bool
     */
    public function deleteTopic(int $topicId): bool
    {
        $topicContent = $this->getTopicById($topicId)->getContent();
        EditorManager::getInstance()->deleteEditorImageInContent($topicContent);

        $sql = 'DELETE FROM cmw_forums_topics WHERE forum_topic_id = :topic_id';

        $db = DatabaseManager::getInstance();

        return $db->prepare($sql)->execute(array('topic_id' => $topicId));
    }

    /**
     * @param \CMW\Entity\Forum\ForumTopicEntity $topic
     * @return \CMW\Entity\Forum\ForumTopicEntity|null
     */
    public function trashTopic(ForumTopicEntity $topic): ?ForumTopicEntity
    {
        $topicId = $topic->getId();
        $responseModel = new ForumResponseModel;
        if ($responseModel->countResponseInTopic($topicId) === 0) {
            $sql = 'UPDATE `cmw_forums_topics` SET `cmw_forums_topics`.`forum_topic_is_trash`= 1 WHERE `cmw_forums_topics`.`forum_topic_id` = :topic_id';
            $db = DatabaseManager::getInstance();
            $req = $db->prepare($sql);
            if ($req->execute(array('topic_id' => $topicId))) {
                return $this->getTopicById($topicId);
            }
            return null;
        } else {
            $sql = 'UPDATE `cmw_forums_response`, `cmw_forums_topics` SET `cmw_forums_response`.`forum_response_is_trash`= 1, `cmw_forums_response`.`forum_response_trash_reason`= 0, `cmw_forums_topics`.`forum_topic_is_trash`= 1 WHERE `cmw_forums_response`.`forum_topic_id` = :topic_id AND `cmw_forums_topics`.`forum_topic_id` = :topic_id_2';
            $db = DatabaseManager::getInstance();
            $req = $db->prepare($sql);
            if ($req->execute(array('topic_id' => $topicId, 'topic_id_2' => $topicId))) {
                return $this->getTopicById($topicId);
            }
            return null;
        }
    }

    /**
     * @param int $topic
     * @return int
     */
    public function restoreTopic(int $topic): int
    {
        // Revoir comment fair fonctionner ceci (j'ai pas le time tout de suite)
        $responseModel = new ForumResponseModel;
        if ($responseModel->countResponseInTopicWithoutTrashFunction($topic) === 0) {
            $sql = 'UPDATE `cmw_forums_topics` SET `cmw_forums_topics`.`forum_topic_is_trash`= 0 WHERE `cmw_forums_topics`.`forum_topic_id` = :topic_id';
            $db = DatabaseManager::getInstance();
            return $db->prepare($sql)->execute(array('topic_id' => $topic));
        } else {
            $sql = 'UPDATE `cmw_forums_response`, `cmw_forums_topics` SET `cmw_forums_response`.`forum_response_is_trash`= 0, `cmw_forums_topics`.`forum_topic_is_trash`= 0 WHERE `cmw_forums_response`.`forum_topic_id` = :topic_id AND `cmw_forums_topics`.`forum_topic_id` = :topic_id_2';
            $db = DatabaseManager::getInstance();
            return $db->prepare($sql)->execute(array('topic_id' => $topic, 'topic_id_2' => $topic));
        }
    }

    /**
     * @return array
     */
    public function getTrashTopic(): array
    {
        $sql = 'SELECT * FROM `cmw_forums_topics` WHERE `forum_topic_is_trash` = 1 ORDER BY `cmw_forums_topics`.`forum_topic_updated` DESC';

        $db = DatabaseManager::getInstance();
        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return array();
        }

        $toReturn = array();

        while ($topic = $res->fetch()) {
            $toReturn[] = $this->getTopicById($topic['forum_topic_id']);
        }

        return $toReturn;
    }

    /**
     * @param $topicId
     * @return bool
     */
    public function isTrashedTopic($topicId): bool
    {
        $sql = 'SELECT * FROM `cmw_forums_topics` WHERE `forum_topic_is_trash` = 1 AND `forum_topic_id` = :topic_id';

        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if ($req->execute(array('topic_id' => $topicId))) {
            return $req->rowCount() === 1;
        }
        return false;
    }

    /**
     * @param string $content
     * @param int $topicId
     * @return \CMW\Entity\Forum\ForumTopicTagEntity|null
     */
    public function addTag(string $content, int $topicId): ?ForumTopicTagEntity
    {
        $sql = 'INSERT INTO cmw_forums_topics_tags (forums_topics_tags_content, forums_topics_tags_topic_id) 
                VALUES (:content, :topic_id)';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute(array('content' => $content, 'topic_id' => $topicId))) {
            return $this->getTagById($db->lastInsertId());
        }

        return null;
    }

    /**
     * @param int $topicId
     * @return \CMW\Entity\Forum\ForumTopicTagEntity|null
     */
    public function clearTag(int $topicId): ?ForumTopicTagEntity
    {
        $sql = 'DELETE FROM cmw_forums_topics_tags WHERE forums_topics_tags_topic_id = :topic_id';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        if ($req->execute(array('topic_id' => $topicId))) {
            return null;
        }
        return null;
    }

    /**
     * @param int $tagId
     * @return \CMW\Entity\Forum\ForumTopicTagEntity|null
     */
    public function getTagById(int $tagId): ?ForumTopicTagEntity
    {
        $sql = 'SELECT * FROM cmw_forums_topics_tags WHERE forums_topics_tags_id = :tag_id';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if (!$req->execute(array('tag_id' => $tagId))) {
            return null;
        }

        $res = $req->fetch();
        return new ForumTopicTagEntity(
            $res['forums_topics_tags_id'],
            $res['forums_topics_tags_content']
        );
    }

    /**
     * @return \CMW\Entity\Forum\ForumTopicTagEntity[]
     */
    public function getTags(): array
    {
        $sql = 'SELECT forums_topics_tags_id FROM cmw_forums_topics_tags';

        $db = DatabaseManager::getInstance();

        $req = $db->query($sql);

        $toReturn = array();

        while ($data = $req->fetch()) {
            $toReturn[] = $this->getTagById($data['forums_topics_tags_id']);
        }

        return $toReturn;
    }

    /**
     * @param int $topicId
     * @return \CMW\Entity\Forum\ForumTopicTagEntity[]
     */
    public function getTagsForTopicById(int $topicId): array
    {
        $toReturn = array();

        $sql = 'SELECT forums_topics_tags_id  FROM cmw_forums_topics_tags WHERE forums_topics_tags_topic_id = :topic_id';
        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if (!$req->execute(array('topic_id' => $topicId))) {
            return $toReturn;
        }

        while ($data = $req->fetch()) {
            $toReturn[] = $this->getTagById($data['forums_topics_tags_id']);
        }

        return $toReturn;
    }

    /**
     * @param int $tagId
     * @param string $content
     * @param int $topicId
     * @return \CMW\Entity\Forum\ForumTopicTagEntity|null
     */
    public function editTag(int $tagId, string $content, int $topicId): ?ForumTopicTagEntity
    {
        $var = array(
            'tag_id' => $tagId,
            'content' => $content,
            'topic_id' => $topicId
        );

        $sql = 'UPDATE cmw_forums_topics_tags SET forums_topics_tags_content = :content, 
                                  forums_topics_tags_topic_id = :topic_id WHERE forums_topics_tags_id = :tag_id';
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if (!$req->execute($var)) {
            return null;
        }

        return new ForumTopicTagEntity(
            $tagId,
            $content,
        );
    }

    /**
     * @param int $tagId
     * @return bool
     */
    public function deleteTag(int $tagId): bool
    {
        $sql = 'DELETE FROM cmw_forums_topics_tags WHERE forums_topics_tags_id = :tag_id';

        $db = DatabaseManager::getInstance();

        return $db->prepare($sql)->execute(array('tag_id' => $tagId));
    }
}
