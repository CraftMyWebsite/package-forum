<?php

namespace CMW\Entity\Forum;

use CMW\Entity\Users\userEntity;
use CMW\Controller\Core\CoreController;

class TopicEntity
{

    private int $topicId;
    private string $topicName;
    private string $topicSlug;
    private string $topicContent;
    private string $topicCreated;
    private string $topicUpdate;
    private bool $topicPinned;
    private bool $disallowReplies;
    private bool $important;
    private userEntity $topicUser;
    private ForumEntity $topicForum;
    /* @var \CMW\Entity\Forum\TopicTagEntity[] $tags */
    private array $tags;

    /**
     * @param int $topicId
     * @param string $topicName
     * @param string $topicSlug
     * @param string $topicContent
     * @param string $topicCreated
     * @param string $topicUpdate
     * @param bool $topicPinned
     * @param bool $disallowReplies
     * @param bool $important
     * @param \CMW\Entity\Users\userEntity $topicUser
     * @param \CMW\Entity\Forum\ForumEntity $topicForum
     * @param \CMW\Entity\Forum\TopicTagEntity[] $tags
     */
    public function __construct(int         $topicId, string $topicName, string $topicSlug, string $topicContent, string $topicCreated, string $topicUpdate,
                                bool        $topicPinned, bool $disallowReplies, bool $important, userEntity $topicUser,
                                ForumEntity $topicForum, array $tags)
    {
        $this->topicId = $topicId;
        $this->topicName = $topicName;
        $this->topicSlug = $topicSlug;
        $this->topicContent = $topicContent;
        $this->topicCreated = $topicCreated;
        $this->topicUpdate = $topicUpdate;
        $this->topicPinned = $topicPinned;
        $this->disallowReplies = $disallowReplies;
        $this->important = $important;
        $this->topicUser = $topicUser;
        $this->topicForum = $topicForum;
        $this->tags = $tags;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->topicId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->topicName;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->topicContent;
    }

    /**
     * @return string
     */
    public function getCreated(): string
    {
        return CoreController::formatDate($this->topicCreated);
    }

    /**
     * @return string
     */
    public function getUpdate(): string
    {
        return CoreController::formatDate($this->topicUpdate);
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->topicSlug;
    }

    /**
     * @return bool
     */
    public function isPinned(): bool
    {
        return $this->topicPinned;
    }

    /**
     * @return bool
     */
    public function isDisallowReplies(): bool
    {
        return $this->disallowReplies;
    }

    /**
     * @return bool
     */
    public function isImportant(): bool
    {
        return $this->important;
    }

    /**
     * @return \CMW\Entity\Forum\ForumEntity
     */
    public function getForum(): ForumEntity
    {
        return $this->topicForum;
    }

    /**
     * @return \CMW\Entity\Users\userEntity
     */
    public function getUser(): userEntity
    {
        return $this->topicUser;
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return "forum/t/$this->topicSlug";
    }

    public function getPinnedLink(): string
    {
        return "forum/t/$this->topicSlug/pinned";
    }

    public function getDisallowRepliesLink(): string
    {
        return "forum/t/$this->topicSlug/disallowreplies";
    }

    /**
     * @return \CMW\Entity\Forum\TopicTagEntity[]
     */
    public function getTags(): array
    {
        return $this->tags;
    }
}