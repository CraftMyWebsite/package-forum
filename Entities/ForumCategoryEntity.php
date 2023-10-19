<?php

namespace CMW\Entity\Forum;
use CMW\Controller\Core\CoreController;
use CMW\Controller\Users\UsersController;
use CMW\Manager\Env\EnvManager;
use CMW\Model\Forum\ForumCategoryModel;
use CMW\Model\Forum\ForumPermissionRoleModel;
use CMW\Model\Users\RolesModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Website;

class ForumCategoryEntity
{

    private int $categoryId;
    private string $categoryName;
    private string $categorySlug;
    private string $categoryIcon;
    private ?string $categoryDescription;
    private int $categoryIsRestricted;
    private string $categoryCreated;
    private string $categoryUpdate;
    private ?array $restrictedRoles;

    /**
     * @param int $id
     * @param string $name
     * @param string $categorySlug
     * @param string $icon
     * @param string $categoryCreated
     * @param string $categoryUpdate
     * @param ?string $desc
     * @param int $categoryIsRestricted
     * @param \CMW\Entity\Forum\ForumPermissionRoleEntity[]|null $restrictedRoles
     */

    public function __construct(int $id, string $name, string $categorySlug, string $icon,string $categoryCreated, string $categoryUpdate, ?string $desc, int $categoryIsRestricted, ?array $restrictedRoles)
    {
        $this->categoryId = $id;
        $this->categoryName = $name;
        $this->categorySlug = $categorySlug;
        $this->categoryIcon = $icon;
        $this->categoryDescription = $desc;
        $this->categoryIsRestricted = $categoryIsRestricted;
        $this->categoryCreated = $categoryCreated;
        $this->categoryUpdate = $categoryUpdate;
        $this->restrictedRoles = $restrictedRoles;
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
    public function getSlug(): string
    {
        return $this->categorySlug;
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return Website::getProtocol() . '://' . $_SERVER['SERVER_NAME'] . EnvManager::getInstance()->getValue("PATH_SUBFOLDER")."forum/c/".$this->categorySlug;
    }

    /**
     * @param string|null $param
     * @return ?string
     */
    public function getFontAwesomeIcon(?string $param = null): ?string
    {
        return '<i class="' . $this->categoryIcon . '  ' . $param . '"></i>';
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
     * @return bool
     */
    public function isRestricted(): bool
    {
        return $this->categoryIsRestricted;
    }


    /**
     * @return bool
     */
    public function isUserAllowed(): bool
    {
        if (!$this->isRestricted()) {
            return true;
        }

        if (!UsersController::isUserLogged() && $this->isRestricted()) {
            return false;
        }

        if ($this->restrictedRoles === null) {
            return true;
        }

        foreach ($this->restrictedRoles as $restrictedRole) {
            if (ForumPermissionRoleModel::playerHasForumRole($restrictedRole?->getId())) {
                return true;
            }
        }

        return false;
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

    /**
     * @return int
     * @desc Return the number of topics inside the category
     */
    public function getNumberOfTopics(): int
    {
        return (new ForumCategoryModel())->getNumberOfTopics($this->categoryId);
    }

    /**
     * @return int
     * @desc Return the number of responses inside the topics on the category
     */
    public function getNumberOfMessages(): int
    {
        return (new ForumCategoryModel())->getNumberOfMessages($this->categoryId);
    }

}