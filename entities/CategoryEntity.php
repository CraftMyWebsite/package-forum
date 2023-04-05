<?php

namespace CMW\Entity\Forum;
use CMW\Controller\Core\CoreController;

class CategoryEntity
{

    private int $categoryId;
    private string $categoryName;
    private string $categoryIcon;
    private string $categoryDescription;
    private string $categoryCreated;
    private string $categoryUpdate;

    public function __construct(int $id, string $name, string $icon,string $categoryCreated, string $categoryUpdate, string $desc = "")
    {
        $this->categoryId = $id;
        $this->categoryName = $name;
        $this->categoryIcon = $icon;
        $this->categoryDescription = $desc;
        $this->categoryCreated = $categoryCreated;
        $this->categoryUpdate = $categoryUpdate;
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
    public function getFontAwesomeIcon(): string
    {
        return '<i class="' . $this->categoryIcon . '"></i>';
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return $this->categoryIcon;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->categoryDescription;
    }

    /**
     * @return string
     */
    public function getCreated(): string
    {
        return CoreController::formatDate($this->categoryCreated);
    }

    /**
     * @return string
     */
    public function getUpdate(): string
    {
        return CoreController::formatDate($this->categoryUpdate);
    }

    public function getAdminDeleteLink(): string
    {
        return "./delete/$this->categoryId";
    }

}