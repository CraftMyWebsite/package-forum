<?php

namespace CMW\Model\Forum;

use CMW\Entity\Forum\ForumDiscordEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Utils\Website;

/**
 * Class: @ForumDiscordModel
 * @package Forum
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ForumDiscordModel extends AbstractModel
{
    /**
     * @param int $id
     * @return \CMW\Entity\Forum\ForumDiscordEntity|null
     */
    public function getDiscordById(int $id): ?ForumDiscordEntity
    {
        $sql = 'SELECT * FROM cmw_forums_discord WHERE forum_discord_id = :id';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array('id' => $id))) {
            return null;
        }

        $res = $res->fetch();

        return new ForumDiscordEntity(
            $res['forum_discord_id'],
            $res['forum_discord_webhook'],
            $res['forum_discord_description'],
            $res['forum_discord_embed_color']
        );
    }

    /**
     * @param $action
     * @param $type
     * @return bool
     */
    public function getAllActionPossibleByType($action, $type): bool {}

    /**
     * @param $forumId
     * @return int
     */
    public function thisForumCanDoAction($forumId): int
    {
        $sql = 'SELECT forum_discord_id FROM cmw_forums_discord WHERE forum_id = :forumId';
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if (!$req->execute(array('forumId' => $forumId))) {
            return 0;
        }

        $res = $req->fetch();

        if (!$res) {
            return 0;
        }

        return $res['forum_discord_id'] ?? 0;
    }

    /**
     * @param $forumId
     * @param $topicName
     * @param $topicForumName
     * @param $topicLink
     * @param $topicUserPicture
     * @param $topicUserName
     * @return void
     * @throws \JsonException
     */
    public function sendDiscordMsgNewTopic($forumId, $topicName, $topicForumName, $topicLink, $topicUserPicture, $topicUserName): void
    {
        $discordId = $this->thisForumCanDoAction($forumId);
        if ($discordId) {
            $webhook = $this->getDiscordById($discordId)->getWebhook();
            $embedColor = $this->getDiscordById($discordId)->getDiscordEmbedColor();

            $timestamp = date('c', strtotime('now'));
            $json = [
                'tts' => false,
                'embeds' => [
                    [
                        'title' => "[FORUM] '" . $topicName . "' viens d'être poster dans " . $topicForumName,
                        'type' => 'article',
                        'url' => Website::getProtocol() . '://' . $_SERVER['SERVER_NAME'] . EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . $topicLink,
                        'timestamp' => $timestamp,
                        'color' => hexdec($embedColor),
                        'footer' => [
                            'text' => $_SERVER['SERVER_NAME'] . ' - CMW',
                        ],
                        'thumbnail' => ['url' => Website::getProtocol() . '://' . $_SERVER['SERVER_NAME'] . EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'Public/Uploads/Users/' . $topicUserPicture],
                        'fields' => [
                            [
                                'name' => 'Allez le voir dès maintenant !',
                                'value' => 'Écrit par ' . $topicUserName,
                                'inline' => false
                            ]
                        ]
                    ]
                ]
            ];
            $msg = json_encode($json, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            if ($webhook !== '') {
                $ch = curl_init($webhook);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $msg);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_exec($ch);
                curl_close($ch);
            }
        }
    }
}
