<?php

namespace CMW\Model\Forum;

use CMW\Entity\Forum\ForumSettingsEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;

/**
 * Class: @ForumCategoryModel
 * @package Forum
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ForumSettingsModel extends AbstractModel
{
    /**
     * @return array
     */
    public function getSettings(): array
    {
        $db = DatabaseManager::getInstance();
        $req = $db->prepare("SELECT * FROM cmw_forums_settings");

        if ($req->execute()) {
            return $req->fetchAll();
        }

        return ($req->execute()) ? $req->fetchAll() : [];
    }

    /**
     * @param string $option
     * @return string
     * @desc get the selected option
     */
    public function getOptionValue(string $option): string
    {
        $db = DatabaseManager::getInstance();
        $req = $db->prepare("SELECT forum_settings_value FROM cmw_forums_settings WHERE forum_settings_name = ?");
        $req->execute(array($option));
        $option = $req->fetch();

        return $option['forum_settings_value'];
    }

    /**
     * @param string $iconNotRead
     * @param string $iconImportant
     * @param string $iconPin
     * @param string $iconClosed
     * @return void
     */
    public function updateIcons(string $iconNotRead, string $iconImportant, string $iconPin, string $iconClosed): void
    {
        $db = DatabaseManager::getInstance();
        $req = $db->prepare("UPDATE cmw_forums_settings SET forum_settings_value= CASE forum_settings_name WHEN 'IconNotRead' THEN :iconNotRead WHEN 'IconImportant' THEN :iconImportant WHEN 'IconPin' THEN :iconPin WHEN 'IconClosed' THEN :iconClosed ELSE forum_settings_value END WHERE forum_settings_name IN('IconNotRead','IconImportant','IconPin','IconClosed')");
        $req->execute(array("iconNotRead" => $iconNotRead, "iconImportant" => $iconImportant, "iconPin" => $iconPin, "iconClosed" => $iconClosed));
    }

    /**
     * @param string $visitorCanViewForum
     * @return void
     */
    public function updateVisitorCanViewForum(string $visitorCanViewForum): void
    {
        $db = DatabaseManager::getInstance();
        $req = $db->prepare("UPDATE cmw_forums_settings SET forum_settings_value= :visitorCanViewForum WHERE forum_settings_name = 'visitorCanViewForum'");
        $req->execute(array("visitorCanViewForum" => $visitorCanViewForum));
    }

    /**
     * @param string $responsePerPage
     * @return void
     */
    public function updateResponsePerPage(string $responsePerPage): void
    {
        $db = DatabaseManager::getInstance();
        $req = $db->prepare("UPDATE cmw_forums_settings SET forum_settings_value= :responsePerPage WHERE forum_settings_name = 'responsePerPage'");
        $req->execute(array("responsePerPage" => $responsePerPage));
    }
}