<?php

namespace CMW\Implementation\Forum\Core;

use CMW\Interface\Core\IMenus;
use CMW\Model\Forum\ForumModel;

class ForumMenusImplementations implements IMenus {

    public function getRoutes(): array
    {
        $forums = [];
        $forums['Forum'] = 'forum';

        foreach ((new ForumModel())->getForums() as $forum) {
            $forums["Forum : ".$forum->getName()] = 'f/' . $forum->getSlug();
        }

        return $forums;
    }

    public function getPackageName(): string
    {
        return 'Forum';
    }
}