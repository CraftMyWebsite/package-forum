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
    private forumEntity $topicForum;
    private userEntity $topicUser;

    public function __construct(int $id, string $name, string $content, string $topicSlug, bool $topicPinned, forumEntity $forum, userEntity $user)
    {
        $this->topicId = $id;
        $this->topicName = $name;
        $this->topicContent = $content;
        $this->topicSlug = $topicSlug;
        $this->topicPinned = $topicPinned;
        $this->topicForum = $forum;
        $this->topicUser = $user;
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

    public function getLink(): string
    {
        return "forum/t/$this->topicSlug";
    }

}