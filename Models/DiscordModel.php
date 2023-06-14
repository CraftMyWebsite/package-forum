<?php

namespace CMW\Model\Forum;

use CMW\Entity\Forum\DiscordEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Utils\Website;

/**
 * Class: @DiscordModel
 * @package Forum
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class DiscordModel extends AbstractModel
{
    public function getDiscordByAction(int $id): ?DiscordEntity
    {
        $sql = "SELECT * FROM cmw_forums_discord WHERE forum_discord_action = :discord_action";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("discord_action" => $id))) {
            return null;
        }

        $res = $res->fetch();

        return new DiscordEntity(
            $res["forum_discord_id"],
            $res["forum_discord_webhook"],
            $res["forum_discord_description"],
            $res["forum_discord_embed_color"]
        );
    }

    public function DiscordActionIsActive($action): bool
    {
        $sql = "SELECT forum_discord_action FROM cmw_forums_discord WHERE forum_discord_action = :discord_action";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);
        $res->execute(array("discord_action" => $action));

        return count($res->fetchAll()) === 0;
    }

    public function sendDiscordMsgNewTopic($topicName, $topicForumName, $topicLink, $topicUserPicture, $topicUserName): void
    {
        $webhook = $this->getDiscordByAction(0)->getWebhook();
        $embedColor = $this->getDiscordByAction(0)->getDiscordEmbedColor();

        $timestamp = date("c", strtotime("now"));
        $json = [
            "tts" => false,
            "embeds" => [
                [
                    "title" => "Nouveau Topics " . $topicName . " dans " . $topicForumName ,
                    "type" => "article",
                    "description" => "Allez le voir dès maintenant !",
                    "url" => Website::getProtocol() . '://' . $_SERVER['SERVER_NAME'] . EnvManager::getInstance()->getValue("PATH_SUBFOLDER") . $topicLink,
                    "timestamp" => $timestamp,
                    "color" => hexdec( "3366ff" ),
                    "footer" => ["text" => $_SERVER['SERVER_NAME'] . " - CMW",],
                    "thumbnail" => ["url" => Website::getProtocol() . '://' . $_SERVER['SERVER_NAME'] . EnvManager::getInstance()->getValue("PATH_SUBFOLDER") . "Public/Uploads/Users/" . $topicUserPicture],
                    "author" => ["name" => $topicUserName,]
                ]
            ]
        ];
        $msg = json_encode($json, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        if($webhook !== "") {
            $ch = curl_init( $webhook );
            curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
            curl_setopt( $ch, CURLOPT_POST, 1);
            curl_setopt( $ch, CURLOPT_POSTFIELDS, $msg);
            curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt( $ch, CURLOPT_HEADER, 0);
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

            $response = curl_exec( $ch );
            echo $response;
            curl_close( $ch );
        }
    }
}