<?php

namespace CMW\Entity\Forum;

use CMW\Entity\Users\userEntity;

class topicEntity
{

    private int $topicId;
    private string $topicName;
    private string $topicContent;
    private string $topicSlug;
    private bool $topicPinned;
    private bool $disallowReplies;
    private bool $important;
    private forumEntity $topicForum;
    private userEntity $topicUser;

    /**
     * @param int $topicId
     * @param string $topicName
     * @param string $topicContent
     * @param string $topicSlug
     * @param bool $topicPinned
     * @param bool $disallowReplies
     * @param bool $important
     * @param \CMW\Entity\Forum\forumEntity $topicForum
     * @param \CMW\Entity\Users\userEntity $topicUser
     */
    public function __construct(int $topicId, string $topicName, string $topicContent, string $topicSlug, bool $topicPinned, bool $disallowReplies, bool $important, forumEntity $topicForum, userEntity $topicUser)
    {
        $this->topicId = $topicId;
        $this->topicName = $topicName;
        $this->topicContent = $topicContent;
        $this->topicSlug = $topicSlug;
        $this->topicPinned = $topicPinned;
        $this->disallowReplies = $disallowReplies;
        $this->important = $important;
        $this->topicForum = $topicForum;
        $this->topicUser = $topicUser;
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
     * @return \CMW\Entity\Forum\forumEntity
     */
    public function getForum(): forumEntity
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
}