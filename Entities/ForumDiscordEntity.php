<?php

namespace CMW\Entity\Forum;

class ForumDiscordEntity
{
    private int $DiscordId;
    private string $DiscordWebhook;
    private string $DiscordDescription;

    private string $DiscordEmbedColor;

    public function __construct(int $DiscordId, string $DiscordWebhook, string $DiscordDescription, string $DiscordEmbedColor)
    {
        $this->DiscordId = $DiscordId;
        $this->DiscordWebhook = $DiscordWebhook;
        $this->DiscordDescription = $DiscordDescription;
        $this->DiscordEmbedColor = $DiscordEmbedColor;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->DiscordId;
    }

    /**
     * @return string
     */
    public function getWebhook(): string
    {
        return $this->DiscordWebhook;
    }

    /**
     * @return string
     */
    public function getWebhookDescription(): string
    {
        return $this->DiscordDescription;
    }

    /**
     * @return string
     */
    public function getDiscordEmbedColor(): string
    {
        return $this->DiscordEmbedColor;
    }
}