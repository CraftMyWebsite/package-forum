<?php

namespace CMW\Entity\Forum;

use CMW\Manager\Package\AbstractEntity;

class ForumTopicTagEntity extends AbstractEntity
{
    private int $id;
    private string $content;

    /**
     * @param int $id
     * @param string $content
     */
    public function __construct(int $id, string $content)
    {
        $this->id = $id;
        $this->content = $content;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }
}
