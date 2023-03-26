<?php

namespace CMW\Entity\Forum;

class TopicTagEntity
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