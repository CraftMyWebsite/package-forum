<?php

namespace CMW\Entity\Forum;

use CMW\Controller\Core\CoreController;

class ForumEntity
{

    private int $forumId;
    private string $forumName;
    private string $forumIcon;
    private string $forumDescription;
    private string $forumSlug;
    private string $forumCreated;
    private string $forumUpdated;
    private ForumEntity|CategoryEntity $forumParent;

    public function __construct(int $id, string $name, string $icon, string $desc, string $forumSlug, string $forumCreated, string $forumUpdated, ForumEntity|CategoryEntity $parent)
    {
        $this->forumId = $id;
        $this->forumName = $name;
        $this->forumIcon = $icon;
        $this->forumDescription = $desc;
        $this->forumSlug = $forumSlug;
        $this->forumCreated = $forumCreated;
        $this->forumUpdated = $forumUpdated;
        $this->forumParent = $parent;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->forumId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->forumName;
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return $this->forumIcon;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->forumDescription;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->forumSlug;
    }

    /**
     * @return string
     */
    public function getCreated(): string
    {
        return CoreController::formatDate($this->forumCreated);
    }

    /**
     * @return string
     */
    public function getUpdate(): string
    {
        return CoreController::formatDate($this->forumUpdated);
    }

    public function getLink(): string
    {
        return "forum/f/$this->forumSlug";
    }

    public function getParent(): ForumEntity|CategoryEntity
    {
        return $this->forumParent;
    }

    public function isParentCategory(): bool
    {
        return get_class($this->forumParent) !== get_class($this);
    }

    public function getAdminDeleteLink(): string
    {
        return "./delete/$this->forumId";
    }


}