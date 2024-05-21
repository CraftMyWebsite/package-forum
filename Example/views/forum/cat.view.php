<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Utils\Website;

/** @var \CMW\Model\Forum\ForumModel $forumModel */
/** @var \CMW\Entity\Forum\ForumCategoryEntity $category */

Website::setTitle("Forum");
Website::setDescription("Consulter les catégorie du Forum");
?>

<section>
    <div>
        <!--Search input-->
        <form action="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>forum/search" method="POST">
            <?php (new SecurityManager())->insertHiddenToken() ?>
            <input type="text" name="for" placeholder="Search">
            <button type="submit"> Search</button>
        </form>
    </div>

    <div>
        <?php if ($category->isUserAllowed()): ?>
            <div><?= $category->getFontAwesomeIcon() ?> <?= $category->getName() ?></div>
            <div>Topics</div>
            <div>Messages</div>
            <div>Last message</div>
            <?php foreach ($forumModel->getForumByCat($category->getId()) as $forumObj): ?>
                <?php if ($forumObj->isUserAllowed()): ?>
                    <a class="flex" href="<?= $forumObj->getLink() ?>">
                        <div><?= $forumObj->getFontAwesomeIcon("fa-xl") ?></div>
                        <div><?= $forumObj->getName() ?></div>
                        <div><?= $forumObj->getDescription() ?></div>
                    </a>
                    <div><?= $forumModel->countTopicInForum($forumObj->getId()) ?></div>
                    <div><?= $forumModel->countMessagesInForum($forumObj->getId()) ?></div>
                    <!--Last Message-->
                    <?php if ($forumObj->getLastResponse() !== null) : ?>
                        <a href="<?= $forumObj->getParent()->getLink() ?>/f/<?= $forumObj->getLastResponse()->getResponseTopic()->getForum()->getSlug() ?>/t/<?= $forumObj->getLastResponse()->getResponseTopic()->getSlug() ?>/p<?= $forumObj->getLastResponse()->getPageNumber() ?>/#<?= $forumObj->getLastResponse()?->getId() ?>">
                            <img src="<?= $forumObj->getLastResponse()?->getUser()->getUserPicture()->getImage()?>"/>
                            <div><?= $forumObj->getLastResponse()?->getUser()->getPseudo() ?? "" ?></div>
                            <div><?= $forumObj->getLastResponse()?->getCreated() ?? "" ?></div>
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>