<?php

namespace CMW\Entity\Forum;

use CMW\Utils\Date;
use CMW\Entity\Users\userEntity;
use CMW\Manager\Env\EnvManager;
use CMW\Model\Forum\ForumCategoryModel;
use CMW\Model\Forum\ForumResponseModel;
use CMW\Model\Forum\ForumTopicModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Website;

class ForumTopicEntity
{
    private int $topicId;
    private string $topicName;
    private string $topicPrefix;
    private string $topicSlug;
    private string $topicContent;
    private int $topicIsTrash;
    private int $topicTrashReason;
    private string $topicCreated;
    private string $topicUpdate;
    private bool $topicPinned;
    private bool $disallowReplies;
    private bool $important;
    private userEntity $topicUser;
    private ForumEntity $topicForum;

    /* @var \CMW\Entity\Forum\ForumTopicTagEntity[] $tags */
    private array $tags;

    /**
     * @param int $topicId
     * @param string $topicName
     * @param string $topicPrefix
     * @param string $topicSlug
     * @param string $topicContent
     * @param int $topicIsTrash
     * @param int $topicTrashReason
     * @param string $topicCreated
     * @param string $topicUpdate
     * @param bool $topicPinned
     * @param bool $disallowReplies
     * @param bool $important
     * @param \CMW\Entity\Users\userEntity $topicUser
     * @param \CMW\Entity\Forum\ForumEntity $topicForum
     * @param \CMW\Entity\Forum\ForumTopicTagEntity[] $tags
     */
    public function __construct(int $topicId, string $topicName, string $topicPrefix, string $topicSlug, string $topicContent, int $topicIsTrash, int $topicTrashReason, string $topicCreated, string $topicUpdate,
        bool $topicPinned, bool $disallowReplies, bool $important, userEntity $topicUser,
        ForumEntity $topicForum, array $tags)
    {
        $this->topicId = $topicId;
        $this->topicName = $topicName;
        $this->topicPrefix = $topicPrefix;
        $this->topicSlug = $topicSlug;
        $this->topicContent = $topicContent;
        $this->topicIsTrash = $topicIsTrash;
        $this->topicTrashReason = $topicTrashReason;
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
     * @return int
     */
    public function getPrefixId(): string
    {
        return $this->topicPrefix;
    }

    /**
     * @return string
     */
    public function getPrefixName(): string
    {
        if ($this->topicPrefix === '') {
            return false;
        }
        return ForumTopicModel::getInstance()->getPrefixName($this->topicPrefix);
    }

    /**
     * @return string
     */
    public function getPrefixColor(): string
    {
        if ($this->topicPrefix === '') {
            return false;
        }
        return ForumTopicModel::getInstance()->getPrefixColor($this->topicPrefix);
    }

    /**
     * @return string
     */
    public function getPrefixTextColor(): string
    {
        if ($this->topicPrefix === '') {
            return false;
        }
        return ForumTopicModel::getInstance()->getPrefixTextColor($this->topicPrefix);
    }

    /**
     * @return string
     */
    public function countViews(): string
    {
        return ForumTopicModel::getInstance()->countViews($this->topicId);
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
    public function getTrashReason(): string
    {
        if ($this->topicTrashReason == 0) {
            return 'Supprimer par un staff';
        }
    }

    /**
     * @return string
     */
    public function getCreated(): string
    {
        return Date::formatDate($this->topicCreated);
    }

    /**
     * @return string
     */
    public function getUpdate(): string
    {
        return Date::formatDate($this->topicUpdate);
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
     * @return \CMW\Entity\Forum\ForumCategoryEntity
     */
    public function getCat(): ForumCategoryEntity
    {
        $forumId = $this->getForum()->getId();
        return ForumCategoryModel::getInstance()->getCategoryByForumId($forumId);
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
     * @param $catSlug
     * @param $forumSlug
     */
    public function getLink(): string
    {
        $baseUrl = Website::getProtocol() . '://' . $_SERVER['SERVER_NAME'] . EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'forum/';
        $catSlug = $this->getCat()->getSlug();
        $forumSlug = $this->getForum()->getSlug();
        return $baseUrl . "c/$catSlug/f/$forumSlug/t/$this->topicSlug/p1";
    }

    /**
     * @return string
     */
    public function getPinnedLink(): string
    {
        return $this->getLink() . '/pinned';
    }

    /**
     * @return string
     */
    public function getDisallowRepliesLink(): string
    {
        return $this->getLink() . '/disallowreplies';
    }

    /**
     * @return string
     */
    public function getIsImportantLink(): string
    {
        return $this->getLink() . '/isimportant';
    }

    /**
     * @return string
     */
    public function trashLink(): string
    {
        return $this->getLink() . '/trash';
    }

    /**
     * @return string
     */
    public function reportLink(): string
    {
        return $this->getLink() . "/reportTopic/$this->topicId/p1";
    }

    /**
     * @return \CMW\Entity\Forum\ForumTopicTagEntity[]
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
        return implode(', ', $this->tags);
    }

    /**
     * @return \CMW\Entity\Forum\ForumResponseEntity|null
     */
    public function getLastResponse(): ?ForumResponseEntity
    {
        return (new ForumResponseModel())->getLatestResponseInTopic($this->topicId);
    }

    public function isSelfTopic(): bool
    {
        return $this->getUser()->getId() === UsersModel::getCurrentUser()?->getId();
    }

    public function editTopicLink(): string
    {
        return $this->getLink() . '/edit';
    }

    public function followTopicLink(): string
    {
        return $this->getLink() . '/follow';
    }

    public function unfollowTopicLink(): string
    {
        return $this->getLink() . '/unfollow';
    }

    /**
     * @return string
     */
    public function getFeedbackAddTopicLink(int $feedbackId): string
    {
        return $this->getLink() . "/react/$this->topicId/$feedbackId";
    }

    /**
     * @return string
     */
    public function getFeedbackDeleteTopicLink(int $feedbackId): string
    {
        return $this->getLink() . "/un_react/$this->topicId/$feedbackId";
    }

    /**
     * @return string
     */
    public function getFeedbackChangeTopicLink(int $feedbackId): string
    {
        return $this->getLink() . "change_react/$this->topicId/$feedbackId";
    }
}
