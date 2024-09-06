<?php

namespace CMW\Entity\Forum;

use CMW\Controller\Core\CoreController;

class ForumPrefixesEntity
{
    private int $prefixId;
    private string $prefixName;
    private string $prefixColor;
    private string $prefixTextColor;
    private string $prefixDescription;
    private string $prefixCreated;
    private string $prefixUpdated;

    public function __construct(int $prefixId, string $prefixName, string $prefixColor, string $prefixTextColor, string $prefixDescription, string $prefixCreated, string $prefixUpdated)
    {
        $this->prefixId = $prefixId;
        $this->prefixName = $prefixName;
        $this->prefixColor = $prefixColor;
        $this->prefixTextColor = $prefixTextColor;
        $this->prefixDescription = $prefixDescription;
        $this->prefixCreated = $prefixCreated;
        $this->prefixUpdated = $prefixUpdated;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->prefixId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->prefixName;
    }

    /**
     * @return string
     */
    public function getColor(): string
    {
        return $this->prefixColor;
    }

    /**
     * @return string
     */
    public function getTextColor(): string
    {
        return $this->prefixTextColor;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->prefixDescription;
    }

    /**
     * @return string
     */
    public function getCreated(): string
    {
        return CoreController::formatDate($this->prefixCreated);
    }

    /**
     * @return string
     */
    public function getUpdated(): string
    {
        return CoreController::formatDate($this->prefixUpdated);
    }
}
