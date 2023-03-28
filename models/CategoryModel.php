<?php

namespace CMW\Model\Forum;

use CMW\Entity\Forum\CategoryEntity;
use CMW\Manager\Database\DatabaseManager;


/**
 * Class: @CategoryModel
 * @package Forum
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class CategoryModel extends DatabaseManager
{

    /**
     * @return \CMW\Entity\Forum\CategoryEntity[]
     */
    public function getCategories(): array
    {

        $sql = "SELECT forum_category_id FROM cmw_forums_categories";
        $db = self::getInstance();

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

    public function getCategoryById(int $id): ?CategoryEntity
    {
        $sql = "SELECT * FROM cmw_forums_categories WHERE forum_category_id = :category_id";

        $db = self::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("category_id" => $id))) {
            return null;
        }

        $res = $res->fetch();

        return new CategoryEntity(
            $res["forum_category_id"],
            $res["forum_category_name"],
            $res["forum_category_created"],
            $res["forum_category_updated"],
            $res["forum_category_description"] ?? ""
        );
    }

    public function createCategory(string $name, string $description): ?CategoryEntity
    {

        $data = array(
            "category_name" => $name,
            "category_description" => $description
        );

        $sql = "INSERT INTO cmw_forums_categories(forum_category_name, forum_category_description) VALUES (:category_name, :category_description)";

        $db = self::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            $id = $db->lastInsertId();
            return new CategoryEntity($id, $name, $description);
        }

        return null;
    }

    public function deleteCategory(int $id): bool
    {
        $sql = "DELETE FROM cmw_forums_categories WHERE forum_category_id = :category_id";

        $db = self::getInstance();

        return $db->prepare($sql)->execute(array("category_id" => $id));
    }


}