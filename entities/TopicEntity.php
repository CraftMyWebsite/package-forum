<?php

namespace CMW\Entity\Forum;

use CMW\Entity\Users\userEntity;
use CMW\Controller\Core\CoreController;
use CMW\Model\Forum\ResponseModel;
use CMW\Model\Forum\TopicModel;
use CMW\Model\Users\UsersModel;

class TopicEntity
{

    private int $topicId;
    private string $topicName;
    private string $topicSlug;
    private string $topicContent;
    private int $topicIsTrash;
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
     * @param int $topicIsTrash
     * @param string $topicCreated
     * @param string $topicUpdate
     * @param bool $topicPinned
     * @param bool $disallowReplies
     * @param bool $important
     * @param \CMW\Entity\Users\userEntity $topicUser
     * @param \CMW\Entity\Forum\ForumEntity $topicForum
     * @param \CMW\Entity\Forum\TopicTagEntity[] $tags
     */
    public function __construct(int $topicId, string $topicName, string $topicSlug, string $topicContent, int $topicIsTrash, string $topicCreated, string $topicUpdate,
                                bool        $topicPinned, bool $disallowReplies, bool $important, userEntity $topicUser,
                                ForumEntity $topicForum, array $tags)
    {
        $this->topicId = $topicId;
        $this->topicName = $topicName;
        $this->topicSlug = $topicSlug;
        $this->topicContent = $topicContent;
        $this->topicIsTrash = $topicIsTrash;
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
     * @return int
     */
    public function getIsTrash(): int
    {
        return $this->topicIsTrash;
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

    public function getIsImportantLink(): string
    {
        return "forum/t/$this->topicSlug/isimportant";
    }

    /**
     * @return string
     */
    public function trashLink(): string
    {
        return "forum/t/$this->topicSlug/trash";
    }

    /**
     * @return \CMW\Entity\Forum\TopicTagEntity[]
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @return string
     * @desc Return formatted tags for input: "tag1, tag2, tag3"
     */
    public function getTagsFormatted(): string
    {
        return implode(", ", $this->tags);
    }

    /**
     * @return \CMW\Entity\Forum\ResponseEntity|null
     */
    public function getLastResponse(): ?ResponseEntity
    {
        return (new ResponseModel())->getLatestResponseInTopic($this->topicId);
    }

    public function isSelfTopic(): bool
    {
        return $this->getUser()->getId() === UsersModel::getLoggedUser();
    }

    public function editTopicLink(): string
    {
        return "$this->topicSlug/edit";
    }
}