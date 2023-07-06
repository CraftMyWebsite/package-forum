<?php

namespace CMW\Model\Forum;

use CMW\Entity\Forum\ForumPrefixesEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;

/**
 * Class: @ForumPrefixModel
 * @package Forum
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */

class ForumPrefixModel extends AbstractModel
{
    /**
     * @return \CMW\Entity\Forum\ForumPrefixesEntity[]
     */
    public function getPrefixes(): array
    {

        $sql = "SELECT forum_prefix_id FROM cmw_forums_prefixes";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return array();
        }

        $toReturn = array();

        while ($prefixes = $res->fetch()) {
            $toReturn[] = $this->getPrefixesById($prefixes["forum_prefix_id"]);
        }

        return $toReturn;
    }

    public function getPrefixesById(int $id): ?ForumPrefixesEntity
    {
        $sql = "SELECT * FROM cmw_forums_prefixes WHERE forum_prefix_id = :prefix_id";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("prefix_id" => $id))) {
            return null;
        }

        $res = $res->fetch();

        return new ForumPrefixesEntity(
            $res["forum_prefix_id"],
            $res["forum_prefix_name"],
            $res["forum_prefix_color"],
            $res["forum_prefix_text_color"],
            $res["forum_prefix_description"],
            $res["forum_prefix_created"],
            $res["forum_prefix_updated"]
        );
    }

    public function createPrefix(string $name, string $color, string $textColor, string $description): ?ForumPrefixesEntity
    {

        $data = array(
            "prefix_name" => $name,
            "prefix_color" => $color,
            "prefix_text_color" => $textColor,
            "prefix_description" => $description
        );

        $sql = "INSERT INTO cmw_forums_prefixes(forum_prefix_name, forum_prefix_color, forum_prefix_text_color, forum_prefix_description) VALUES (:prefix_name, :prefix_color, :prefix_text_color, :prefix_description)";

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            $id = $db->lastInsertId();
            return $this->getPrefixesById($id);
        }

        return null;
    }

    public function editPrefix(int $id, string $name, string $color, string $textColor, string $description): ?ForumPrefixesEntity
    {

        $data = array(
            "prefix_id" => $id,
            "prefix_name" => $name,
            "prefix_color" => $color,
            "prefix_text_color" => $textColor,
            "prefix_description" => $description
        );

        $sql = "UPDATE cmw_forums_prefixes SET forum_prefix_name=:prefix_name, forum_prefix_color=:prefix_color, forum_prefix_text_color=:prefix_text_color, forum_prefix_description=:prefix_description WHERE forum_prefix_id=:prefix_id";
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            return $this->getPrefixesById($id);
        }

        return null;
    }

    public function deletePrefix(int $id): bool
    {
        $sql = "DELETE FROM cmw_forums_prefixes WHERE forum_prefix_id = :id";
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        if (!$req->execute(array("id" => $id))) {
            return false;
        }

        return $req->rowCount() === 1;
    }

}