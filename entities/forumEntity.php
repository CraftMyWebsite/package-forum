<?php

namespace CMW\Entity\Forums;

class forumEntity
{

    private int $forumId;
    private string $forumName;
    private string $forumDescription;
    private forumEntity|categoryEntity $forumParent;

    public function __construct(int $id, string $name, string $desc, forumEntity|categoryEntity $parent)
    {
        $this->forumId = $id;
        $this->forumName = $name;
        $this->forumDescription = $desc;
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

    public function getParent(): forumEntity|categoryEntity
    {
        return $this->forumParent;
    }

    public function isParentCategory(): bool
    {
        $categoryClassName = substr(strrchr(get_class((object)categoryEntity::class), "\\"), 1);
        return get_class($this->forumParent) === $categoryClassName;
    }


}