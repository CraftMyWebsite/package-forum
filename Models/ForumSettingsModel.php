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
        $req = $db->prepare('SELECT * FROM cmw_forums_settings');

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
        $req = $db->prepare('SELECT forum_settings_value FROM cmw_forums_settings WHERE forum_settings_name = ?');
        $req->execute(array($option));
        $option = $req->fetch();

        return $option['forum_settings_value'];
    }

    /**
     * @param string $iconNotRead
     * @param string $iconNotReadColor
     * @param string $iconImportant
     * @param string $iconImportantColor
     * @param string $iconPin
     * @param string $iconPinColor
     * @param string $iconClosed
     * @param string $iconClosedColor
     * @return void
     */
    public function updateIcons(string $iconNotRead, string $iconImportant, string $iconPin, string $iconClosed, string $iconNotReadColor, string $iconImportantColor, string $iconPinColor, string $iconClosedColor): void
    {
        $db = DatabaseManager::getInstance();
        $req = $db->prepare("UPDATE cmw_forums_settings SET forum_settings_value= CASE forum_settings_name WHEN 'IconNotRead' THEN :iconNotRead WHEN 'IconImportant' THEN :iconImportant WHEN 'IconPin' THEN :iconPin WHEN 'IconClosed' THEN :iconClosed WHEN 'IconNotReadColor' THEN :iconNotReadColor WHEN 'IconImportantColor' THEN :iconImportantColor WHEN 'IconPinColor' THEN :iconPinColor WHEN 'IconClosedColor' THEN :iconClosedColor ELSE forum_settings_value END WHERE forum_settings_name IN('IconNotRead','IconImportant','IconPin','IconClosed','IconNotReadColor','IconImportantColor','IconPinColor','IconClosedColor')");
        $req->execute(array('iconNotRead' => $iconNotRead, 'iconImportant' => $iconImportant, 'iconPin' => $iconPin, 'iconClosed' => $iconClosed, 'iconNotReadColor' => $iconNotReadColor, 'iconImportantColor' => $iconImportantColor, 'iconPinColor' => $iconPinColor, 'iconClosedColor' => $iconClosedColor));
    }

    /**
     * @param string $visitorCanViewForum
     * @return void
     */
    public function updateVisitorCanViewForum(string $visitorCanViewForum): void
    {
        $db = DatabaseManager::getInstance();
        $req = $db->prepare("UPDATE cmw_forums_settings SET forum_settings_value= :visitorCanViewForum WHERE forum_settings_name = 'visitorCanViewForum'");
        $req->execute(array('visitorCanViewForum' => $visitorCanViewForum));
    }

    /**
     * @param string $responsePerPage
     * @return void
     */
    public function updatePerPage(string $responsePerPage, string $topicPerPage): void
    {
        $db = DatabaseManager::getInstance();
        $req = $db->prepare("UPDATE cmw_forums_settings SET forum_settings_value= CASE forum_settings_name WHEN 'responsePerPage' THEN :responsePerPage WHEN 'topicPerPage' THEN :topicPerPage ELSE forum_settings_value END WHERE forum_settings_name IN ('responsePerPage','topicPerPage')");
        $req->execute(array('responsePerPage' => $responsePerPage, 'topicPerPage' => $topicPerPage));
    }

    /**
     * @param string $needConnectUrl
     * @param string $needConnectText
     * @return void
     */
    public function updateNeedConnect(string $needConnectUrl, string $needConnectText): void
    {
        $db = DatabaseManager::getInstance();
        $req = $db->prepare("UPDATE cmw_forums_settings SET forum_settings_value= CASE forum_settings_name WHEN 'needConnectUrl' THEN :needConnectUrl WHEN 'needConnectText' THEN :needConnectText ELSE forum_settings_value END WHERE forum_settings_name IN ('needConnectUrl','needConnectText')");
        $req->execute(array('needConnectUrl' => $needConnectUrl, 'needConnectText' => $needConnectText));
    }

    /**
     * @param string $blinkResponse
     * @return void
     */
    public function updateBlinkResponse(string $blinkResponse): void
    {
        $db = DatabaseManager::getInstance();
        $req = $db->prepare("UPDATE cmw_forums_settings SET forum_settings_value= :blinkResponse WHERE forum_settings_name = 'blinkResponse'");
        $req->execute(array('blinkResponse' => $blinkResponse));
    }
}
