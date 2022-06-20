<?php

namespace CMW\Entity\Forums;

class categoryEntity
{

    private int $categoryId;
    private string $categoryName;
    private string $categoryDescription;

    public function __construct(int $id, string $name, string $desc = "")
    {
        $this->categoryId = $id;
        $this->categoryName = $name;
        $this->categoryDescription = $desc;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->categoryId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->categoryName;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->categoryDescription;
    }

    public function getAdminDeleteLink(): string
    {
        return "./delete/$this->categoryId";
    }

}