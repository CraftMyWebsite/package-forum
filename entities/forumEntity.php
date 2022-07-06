<?php

namespace CMW\Entity\Forums;

class forumEntity
{

    private int $forumId;
    private string $forumName;
    private string $forumDescription;
    private string $forumSlug;
    private forumEntity|categoryEntity $forumParent;

    public function __construct(int $id, string $name, string $desc, string $forumSlug, forumEntity|categoryEntity $parent)
    {
        $this->forumId = $id;
        $this->forumName = $name;
        $this->forumDescription = $desc;
        $this->forumSlug = $forumSlug;
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

    public function getLink(): string
    {
        return "forum/f/$this->forumSlug";
    }

    public function getParent(): forumEntity|categoryEntity
    {
        return $this->forumParent;
    }

    public function isParentCategory(): bool
    {
        return get_class($this->forumParent) !== get_class($this);
    }

    public function getAdminDeleteLink(): string {
        return "./delete/$this->forumId";
    }


}