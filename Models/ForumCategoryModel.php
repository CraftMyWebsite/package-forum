<?php

namespace CMW\Model\Forum;

use CMW\Entity\Forum\ForumCategoryEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;


/**
 * Class: @ForumCategoryModel
 * @package Forum
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ForumCategoryModel extends AbstractModel
{

    /**
     * @return \CMW\Entity\Forum\ForumCategoryEntity[]
     */
    public function getCategories(): array
    {

        $sql = "SELECT forum_category_id FROM cmw_forums_categories";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return array();
        }

        $toReturn = array();

        while ($cat = $res->fetch()) {
            $toReturn[] = $this->getCategoryById($cat["forum_category_id"]);
        }

        return $toReturn;

    }

    public function getCategoryById(int $id): ?ForumCategoryEntity
    {
        $sql = "SELECT * FROM cmw_forums_categories WHERE forum_category_id = :category_id";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("category_id" => $id))) {
            return null;
        }

        $res = $res->fetch();

        return new ForumCategoryEntity(
            $res["forum_category_id"],
            $res["forum_category_name"],
            $res["forum_category_icon"],
            $res["forum_category_created"],
            $res["forum_category_updated"],
            $res["forum_category_description"] ?? ""
        );
    }

    public function createCategory(string $name, string $icon, string $description): ?ForumCategoryEntity
    {

        $data = array(
            "category_name" => $name,
            "category_icon" => $icon,
            "category_description" => $description
        );

        $sql = "INSERT INTO cmw_forums_categories(forum_category_name, forum_category_icon, forum_category_description) VALUES (:category_name, :category_icon, :category_description)";

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            $id = $db->lastInsertId();
            return $this->getCategoryById($id);
        }

        return null;
    }

    public function editCategory(int $id, string $name, string $icon, string $description): ?ForumCategoryEntity
    {

        $data = array(
            "category_id" => $id,
            "category_name" => $name,
            "category_icon" => $icon,
            "category_description" => $description
        );

        $sql = "UPDATE cmw_forums_categories SET forum_category_name=:category_name, forum_category_icon=:category_icon, forum_category_description=:category_description WHERE forum_category_id=:category_id";
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            return $this->getCategoryById($id);
        }

        return null;
    }

    public function deleteCategory(int $id): bool
    {
        $sql = "DELETE FROM cmw_forums_categories WHERE forum_category_id = :category_id";

        $db = DatabaseManager::getInstance();

        return $db->prepare($sql)->execute(array("category_id" => $id));
    }

    public function getNumberOfTopics(int $categoryId): int
    {
        $sql = "SELECT COUNT('forum_topic_id') AS `count` FROM cmw_forums_topics 
                JOIN cmw_forums ON cmw_forums_topics.forum_id = cmw_forums.forum_id
                JOIN cmw_forums_categories ON cmw_forums.forum_category_id = cmw_forums_categories.forum_category_id
                WHERE cmw_forums_categories.forum_category_id = :id";

        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if (!$req->execute(['id' => $categoryId])){
            return 0;
        }

        $res = $req->fetch();

        if (!$res){
            return 0;
        }

        return $res['count'] ?? 0;
    }

    public function getNumberOfMessages(int $categoryId): int
    {
        $sql = "SELECT COUNT('forum_response_id') AS `count` FROM cmw_forums_response 
                JOIN cmw_forums_topics ON cmw_forums_response.forum_topic_id = cmw_forums_topics.forum_topic_id
                JOIN cmw_forums ON cmw_forums_topics.forum_id = cmw_forums.forum_id
                JOIN cmw_forums_categories ON cmw_forums.forum_category_id = cmw_forums_categories.forum_category_id
                WHERE cmw_forums_categories.forum_category_id = :id";

        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if (!$req->execute(['id' => $categoryId])){
            return 0;
        }

        $res = $req->fetch();

        if (!$res){
            return 0;
        }

        return $res['count'] ?? 0;

    }


}