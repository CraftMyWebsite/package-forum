<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Utils\Website;

/** @var \CMW\Model\Forum\ForumModel $forumModel */
/** @var \CMW\Entity\Forum\ForumCategoryEntity $category */
Website::setTitle('Forum');
Website::setDescription('Consulter les catégorie du Forum');
?>
<?php if (\CMW\Controller\Users\UsersController::isAdminLogged()): ?>
    <div style="background-color: orange; padding: 6px; margin-bottom: 10px">
        <span>Votre thème ne gère pas cette page !</span>
        <br>
        <small>Seuls les administrateurs voient ce message !</small>
    </div>
<?php endif;?>

    <div style="display: flex; flex-wrap: wrap; justify-content: space-between">
        <div>
            <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>forum">
                Home
            </a>
            <i class="fa-solid fa-chevron-right"></i>
            <a href="<?= $category->getLink() ?>"><?= $category->getName() ?></a>
        </div>
        <form action="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>forum/search" method="POST">
            <?php (new SecurityManager())->insertHiddenToken() ?>
            <input type="text" name="for" placeholder="Rechercher">
            <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>
    </div>

    <div style="margin-top: 10px">

<?php if ($category->isUserAllowed()): ?>
    <div style="width: 100%; margin-bottom: 19px; border: 1px #b4aaaa solid; border-radius: 9px">
    <div style="display: flex; flex-wrap: wrap; gap: 1rem; padding: .5rem;">
        <a href="<?= $category->getLink() ?>"
           style="flex: 0 0 50%;"><?= $category->getFontAwesomeIcon() ?> <?= $category->getName() ?></a>
        <div style="flex: 0 0 9%; text-align: center">Topics</div>
        <div style="flex: 0 0 9%; text-align: center">Messages</div>
        <div style="flex: 0 0 28%; text-align: center">Dernier message</div>
    </div>
    <?php foreach ($forumModel->getForumByCat($category->getId()) as $forumObj): ?>
        <?php if ($forumObj->isUserAllowed()): ?>
            <div
                style="display: flex; flex-wrap: wrap; gap: 1rem; padding: .5rem; align-items: center; border-top: #b4aaaa 1px solid">
                <div style="flex: 0 0 50%;">
                    <a style="display: flex; align-items: center"
                       href="<?= $forumObj->getLink() ?>">
                        <div><?= $forumObj->getFontAwesomeIcon('fa-xl') ?></div>
                        <div style="margin-left: 5px">
                            <div>
                                <?= $forumObj->getName() ?>
                            </div>
                            <div>
                                <?= $forumObj->getDescription() ?>
                            </div>
                        </div>
                    </a>
                </div>
                <div
                    style="flex: 0 0 9%; text-align: center"><?= $forumModel->countTopicInForum($forumObj->getId()) ?></div>
                <div
                    style="flex: 0 0 9%; text-align: center"
                "><?= $forumModel->countMessagesInForum($forumObj->getId()) ?></div>
            <!--Dernier message-->
            <div style="flex: 0 0 28%;">
                <div style="display: flex; align-items: center; gap: .5rem">
                    <?php if ($forumObj->getLastResponse() !== null): ?>
                        <a href="<?= $forumObj->getParent()->getLink() ?>/f/<?= $forumObj->getLastResponse()->getResponseTopic()->getForum()->getSlug() ?>/t/<?= $forumObj->getLastResponse()->getResponseTopic()->getSlug() ?>/p<?= $forumObj->getLastResponse()->getPageNumber() ?>/#<?= $forumObj->getLastResponse()?->getId() ?>">
                            <img style="width: 3rem; border-radius: 100%" alt="user"
                                 src="<?= $forumObj->getLastResponse()?->getUser()->getUserPicture()->getImage() ?>"/>
                        </a>
                    <?php endif; ?>
                    <?php if ($forumObj->getLastResponse() !== null): ?>
                    <a href="<?= $forumObj->getParent()->getLink() ?>/f/<?= $forumObj->getLastResponse()->getResponseTopic()->getForum()->getSlug() ?>/t/<?= $forumObj->getLastResponse()->getResponseTopic()->getSlug() ?>/p<?= $forumObj->getLastResponse()->getPageNumber() ?>/#<?= $forumObj->getLastResponse()?->getId() ?>">
                        <?php endif; ?>
                        <div class="ml-2">
                            <div><?= $forumObj->getLastResponse()?->getUser()->getPseudo() ?></div>
                            <div><?= $forumObj->getLastResponse()?->getCreated() ?? 'Aucun message pour le moment' ?></div>
                        </div>
                    </a>
                </div>
            </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
    </div>
<?php endif; ?>