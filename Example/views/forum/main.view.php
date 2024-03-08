<?php

/** @var \CMW\Model\Forum\ForumModel $forumModel */

/** @var \CMW\Model\Forum\ForumCategoryModel $categoryModel */

use CMW\Controller\Users\UsersController;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Utils\Website;

Website::setTitle("Forum");
Website::setDescription("Consultez les sujets de discussion et répondez aux questions posées par les membres de votre communauté.");
?>

<section class="px-4 lg:px-24 2xl:px-60 py-6">
    <?php if ($categoryModel->getCategories() == null): ?>
        <div>
        <?php if (UsersController::isAdminLogged()): ?>
            <a href="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>cmw-admin/forum/manage" target="_blank"> Manage forum</a>
        <?php else: ?>
            <p>No forum found</p>
        <?php endif; ?>
    <?php else: ?>
        <!--Search input-->
        <form action="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>forum/search" method="POST">
            <?php (new SecurityManager())->insertHiddenToken() ?>
            <input type="text" name="for" placeholder="Search">
            <button type="submit"> Search</button>
        </form>
        </div>

        <!--Listing cat & topics-->
        <div>
            <?php foreach ($categoryModel->getCategories() as $category) : ?>
                <?php if ($category->isUserAllowed()): ?>
                    <a href="<?= $category->getLink() ?>"><?= $category->getFontAwesomeIcon() ?> <?= $category->getName() ?></a>
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
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

