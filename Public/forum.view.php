<?php

use CMW\Controller\Forum\Admin\ForumPermissionController;
use CMW\Controller\Users\UsersController;
use CMW\Manager\Env\EnvManager;
use CMW\Utils\Website;

/* @var CMW\Model\Forum\ForumModel $forumModel */
/* @var \CMW\Entity\Forum\ForumCategoryEntity $category */
/* @var CMW\Entity\Forum\ForumEntity $forum */
/* @var CMW\Entity\Forum\ForumTopicEntity[] $topics */
/* @var CMW\Model\Forum\ForumTopicModel $topicModel */
/* @var CMW\Entity\Forum\ForumTopicEntity $topic */
/* @var CMW\Model\Forum\ForumResponseModel $responseModel */
/* @var CMW\Controller\Forum\Admin\ForumSettingsController $iconNotRead */
/* @var CMW\Controller\Forum\Admin\ForumSettingsController $iconImportant */
/* @var CMW\Controller\Forum\Admin\ForumSettingsController $iconPin */
/* @var CMW\Controller\Forum\Admin\ForumSettingsController $iconClosed */
/* @var CMW\Controller\Forum\ForumSettingsController $iconNotReadColor */
/* @var CMW\Controller\Forum\ForumSettingsController $iconImportantColor */
/* @var CMW\Controller\Forum\ForumSettingsController $iconPinColor */
/* @var CMW\Controller\Forum\ForumSettingsController $iconClosedColor */

Website::setTitle('Forum');
Website::setDescription('Consultez les sujets de discussion et répondez aux questions posées par les membres de votre communauté.');
?>
<?php if (\CMW\Controller\Users\UsersController::isAdminLogged()): ?>
    <div style="background-color: orange; padding: 6px; margin-bottom: 10px">
        <span>Votre thème ne gère pas cette page !</span>
        <br>
        <small>Seuls les administrateurs voient ce message !</small>
    </div>
<?php endif; ?>

<div style="display: flex; flex-wrap: wrap; justify-content: space-between">
    <div>
        <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>forum">
            Home
        </a>
        <i class="fa-solid fa-chevron-right"></i>
        <a href="<?= $category->getLink() ?>"><?= $category->getName() ?></a>
        <?php foreach ($forumModel->getParentByForumId($forum->getId()) as $parent): ?>
            <i class="fa-solid fa-chevron-right"></i>
            <a href="<?= $parent->getLink() ?>"><?= $parent->getName() ?></a>
        <?php endforeach; ?>
    </div>
    <?php if (UsersController::isUserLogged()): ?>
        <?php if (!$forum->disallowTopics() && ForumPermissionController::getInstance()->hasPermission('user_create_topic') || ForumPermissionController::getInstance()->hasPermission('operator') || ForumPermissionController::getInstance()->hasPermission('admin_bypass_forum_disallow_topics')): ?>
            <a href="<?= $forum->getLink() ?>/add">Créer un topic</a>
        <?php endif; ?>
    <?php endif; ?>
</div>


<?php if ($forumModel->getSubforumByForum($forum->getId(), true)): ?>
<div style="width: 100%; margin-bottom: 19px; border: 1px #b4aaaa solid; border-radius: 9px; margin-top: 10px">
    <div style="display: flex; flex-wrap: wrap; gap: 1rem; padding: .5rem;">
        <a href="<?= $category->getLink() ?>"
           style="flex: 0 0 50%;"><?= $category->getFontAwesomeIcon() ?> <?= $category->getName() ?></a>
        <div style="flex: 0 0 9%; text-align: center">Topics</div>
        <div style="flex: 0 0 9%; text-align: center">Messages</div>
        <div style="flex: 0 0 28%; text-align: center">Dernier message</div>
    </div>
    <?php foreach ($forumModel->getSubforumByForum($forum->getId()) as $forumObj): ?>
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
                    style="flex: 0 0 9%; text-align: center"><?= $forumModel->countMessagesInForum($forumObj->getId()) ?></div>
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

<div style="width: 100%; margin-bottom: 19px; border: 1px #b4aaaa solid; border-radius: 9px; margin-top: 10px">
    <div style="display: flex; flex-wrap: wrap; gap: 1rem; padding: .5rem;">
        <div style="flex: 0 0 50%;">Topics</div>
        <div style="flex: 0 0 9%; text-align: center">Affichages</div>
        <div style="flex: 0 0 9%; text-align: center">Réponses</div>
        <div style="flex: 0 0 28%; text-align: center">Dernier messages</div>
    </div>
    <?php foreach ($topics as $topic): ?>
        <div style="display: flex; flex-wrap: wrap; gap: 1rem; padding: .5rem; align-items: center; border-top: #b4aaaa 1px solid">
<div style="flex: 0 0 50%;">
    <a  style="display: flex; justify-content: space-between"
        href="<?= $topic->getLink() ?>">
        <div style="display: flex; gap: .4rem">
            <div>
                <img style="object-fit: fill; max-height: 48px; max-width: 48px" width="48px"
                     height="48px"
                     src="<?= $topic->getUser()->getUserPicture()->getImage() ?>"/>
            </div>
            <div>
                <div><?php if ($topic->getPrefixId()): ?><span class="px-2 text-white rounded-lg"
                                                             style="color: <?= $topic->getPrefixTextColor() ?>; background: <?= $topic->getPrefixColor() ?>"><?= $topic->getPrefixName() ?></span> <?php endif; ?><?= $topic->getName() ?>
                </div>
                <div><span class="font-medium"><?= $topic->getUser()->getPseudo() ?></span> <span
                        class="text-sm">le <?= $topic->getCreated() ?></span></div>
            </div>
        </div>

        <div style="display: flex; gap: .9rem">
            <?= $topic->isImportant() ? "
                            <i data-tooltip-target='tooltip-important' style='color: $iconImportantColor' class='<?= $iconImportant ?> fa-sm'></i>" : '' ?>
            <?= $topic->isPinned() ? "
                            <i data-tooltip-target='tooltip-pined' style='color: $iconPinColor' class='<?= $iconPin ?> fa-sm'></i>" : '' ?>
            <?= $topic->isDisallowReplies() ? "
                            <i data-tooltip-target='tooltip-closed' style='color: $iconClosedColor' class='<?= $iconClosed ?> fa-sm'></i>" : '' ?>
        </div>
    </a>
</div>

        <div style="flex: 0 0 9%; text-align: center"><?= $topic->countViews() ?></div>
        <div style="flex: 0 0 9%; text-align: center"><?= $responseModel->countResponseInTopic($topic->getId()) ?></div>
        <div style="flex: 0 0 28%;">
            <div style="display: flex; gap: .5rem">
                <?php if ($topic->getLastResponse() !== null): ?>
                <a href="t/<?= $topic->getLastResponse()->getResponseTopic()->getSlug() ?>/p<?= $topic->getLastResponse()->getPageNumber() ?>/#<?= $topic->getLastResponse()?->getId() ?>">
                    <img alt="user" style="width: 3rem; border-radius: 100%" src="<?= $topic->getLastResponse()?->getUser()->getUserPicture()->getImage()?>">
                </a>
                <?php endif; ?>
                <?php if ($topic->getLastResponse() !== null): ?>
                <a href="t/<?= $topic->getLastResponse()->getResponseTopic()->getSlug() ?>/p<?= $topic->getLastResponse()->getPageNumber() ?>/#<?= $topic->getLastResponse()?->getId() ?>">
                    <?php endif; ?>
                    <div>
                        <div><?= $topic->getLastResponse()?->getUser()->getPseudo() ?></div>
                        <div><?= $topic->getLastResponse()?->getCreated() ?? 'Aucune réponse pour le moment' ?></div>
                    </div>
                </a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php if ($totalPage > '1'): ?>
    <div style="display: flex; justify-self: center">
        <div class="flex justify-center">
            <?php if ($currentPage !== '1'): ?>
                <a href="fp1">
                    <i class="fa-solid fa-chevron-left"></i><i class="fa-solid fa-chevron-left"></i></a>
                <a href="fp<?= $currentPage - 1 ?>">
                    <i class="fa-solid fa-chevron-left"></i></a>
            <?php endif; ?>
            <span><?= $currentPage ?>/<?= $totalPage ?></span>
            <?php if ($currentPage !== $totalPage): ?>
                <a href="fp<?= $currentPage + 1 ?>"<i class="fa-solid fa-chevron-right"></i></a>
                <a href="fp<?= $totalPage ?>"<i class="fa-solid fa-chevron-right"></i><i class="fa-solid fa-chevron-right"></i></a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>