<?php

use CMW\Controller\Forum\Admin\ForumPermissionController;
use CMW\Controller\Users\UsersController;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Forum\ForumPrefixModel;
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

Website::setTitle('Forum');
Website::setDescription('Consultez les sujets de discussion et répondez aux questions posées par les membres de votre communauté.');
?>

<!--optional for breadcrumbs-->
<nav class="flex" aria-label="Breadcrumb">
    <ol>
        <li>
            <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>forum">Home</a>
        </li>
        <li>
            <a href="<?= $category->getLink() ?>"><?= $category->getName() ?></a>
        </li>
        <?php foreach ($forumModel->getParentByForumId($forum->getId()) as $parent): ?>
            <li>
                <a href="<?= $parent->getLink() ?>"><?= $parent->getName() ?></a>
            </li>
        <?php endforeach; ?>
    </ol>
</nav>

<!--Create topic-->
<?php if (UsersController::isUserLogged()): ?>
    <?php if (!$forum->disallowTopics() && ForumPermissionController::getInstance()->hasPermission('user_create_topic') || ForumPermissionController::getInstance()->hasPermission('operator') || ForumPermissionController::getInstance()->hasPermission('admin_bypass_forum_disallow_topics')): ?>
        <a href="<?= $forum->getLink() ?>/add">New topic</a>
    <?php endif; ?>
<?php endif; ?>

<!--Get sub forums-->
<?php if ($forumModel->getSubforumByForum($forum->getId(), true)): ?>
    <div>
        <div>SubForums</div>
        <div>Topics</div>
        <div>Messages</div>
        <div>Last message</div>
    </div>
    <?php foreach ($forumModel->getSubforumByForum($forum->getId(), true) as $forumObj): ?>
        <?php if ($forumObj->isUserAllowed()): ?>
            <div>
                <a href="<?= $forumObj->getLink() ?>">
                    <div><?= $forumObj->getFontAwesomeIcon('fa-xl') ?></div>
                    <div><?= $forumObj->getName() ?></div>
                    <div><?= $forumObj->getDescription() ?></div>
                </a>
            </div>
            <div><?= $forumModel->countTopicInForum($forumObj->getId()) ?></div>
            <div><?= $forumModel->countMessagesInForum($forumObj->getId()) ?></div>
            <!--Last Message-->
            <?php if ($forumObj->getLastResponse() !== null): ?>
                <a href="<?= $forumObj->getParent()->getLink() ?>/f/<?= $forumObj->getLastResponse()->getResponseTopic()->getForum()->getSlug() ?>/t/<?= $forumObj->getLastResponse()->getResponseTopic()->getSlug() ?>/p<?= $forumObj->getLastResponse()->getPageNumber() ?>/#<?= $forumObj->getLastResponse()?->getId() ?>">
                    <img src="<?= $forumObj->getLastResponse()?->getUser()->getUserPicture()->getImage() ?>"/>
                    <div><?= $forumObj->getLastResponse()?->getUser()->getPseudo() ?? '' ?></div>
                    <div><?= $forumObj->getLastResponse()?->getCreated() ?? '' ?></div>
                </a>
            <?php endif; ?>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif ?>

<!--Get Topics-->
<div>
    <div>
        <div>Topics</div>
        <div>Views</div>
        <div>Response</div>
        <div>Last message</div>
    </div>
    <?php foreach ($topics as $topic): ?>
        <a href="<?= $topic->getLink() ?>">
            <img style="object-fit: fill; max-height: 48px; max-width: 48px" width="48px" height="48px" src="<?= $topic->getUser()->getUserPicture()->getImage() ?>"/>
            <p>
                <?php if ($topic->getPrefixId()): ?>
                    <span style="color: <?= $topic->getPrefixTextColor() ?>; background: <?= $topic->getPrefixColor() ?>"><?= $topic->getPrefixName() ?></span>
                <?php endif; ?>
                <?= $topic->getName() ?>
            </p>
            <p><?= $topic->getUser()->getPseudo() ?> <?= $topic->getCreated() ?></p>
            <div>
                <?= $topic->isImportant() ? "<i class='<?= $iconImportant ?>></i>" : '' ?>
                <?= $topic->isPinned() ? "<i class='<?= $iconPin ?>'></i>" : '' ?>
                <?= $topic->isDisallowReplies() ? "<i class='<?= $iconClosed ?>'></i>" : '' ?>
            </div>
        </a>
        <div><?= $topic->countViews() ?></div>
        <div><?= $responseModel->countResponseInTopic($topic->getId()) ?></div>
        <!--Last message-->

        <?php if ($topic->getLastResponse() !== null): ?>
            <a href="t/<?= $topic->getLastResponse()->getResponseTopic()->getSlug() ?>/p<?= $topic->getLastResponse()->getPageNumber() ?>/#<?= $topic->getLastResponse()?->getId() ?>">
                <img src="<?= $topic->getLastResponse()?->getUser()->getUserPicture()->getImage() ?>"/>
                <div><?= $topic->getLastResponse()?->getUser()->getPseudo() ?? '' ?></div>
                <div><?= $topic->getLastResponse()?->getCreated() ?? '' ?></div>
            </a>
        <?php endif; ?>

        <!--optional but recommended-->
        <?php if (UsersController::isAdminLogged()): ?>
            <!------------------
             -- ADMIN SECTION --
            -------------------->
            <form id="<?= $topic->getId() ?>" action="<?= $forum->getLink() ?>/adminedit" method="post">
                <?php (new SecurityManager())->insertHiddenToken() ?>
                <input type="text" name="topicId" hidden value="<?= $topic->getId() ?>">
                <input name="important" value="1" id="important-<?= $topic->getId() ?>" type="checkbox"<?= $topic->isImportant() ? 'checked' : '' ?>>
                <label for="important-<?= $topic->getId() ?>"><i class="<?= $iconImportant ?>"></i>Important</label>
                <input name="pin" id="pin-<?= $topic->getId() ?>" type="checkbox" value="" <?= $topic->isPinned() ? 'checked' : '' ?>>
                <label for="pin-<?= $topic->getId() ?>"><i class="<?= $iconPin ?>"></i>Pin</label>
                <input name="disallow_replies" value="1" id="closed-<?= $topic->getId() ?>" type="checkbox"<?= $topic->isDisallowReplies() ? 'checked' : '' ?>>
                <label for="closed-<?= $topic->getId() ?>"><i class="<?= $iconClosed ?>"></i>Closed</label>

                <label for="title">Topic title* :</label>
                <input name="name" id="title" type="text" value="<?= $topic->getName() ?>" required>
                <label for="tags">Tags* :</label>
                <input name="tags" id="tags" type="text" value="<?php foreach ($topic->getTags() as $tag) { echo '' . $tag->getContent() . ','; } ?>">

                <label for="prefix">Prefix :</label>
                <select name="prefix" id="prefix">
                    <option value="">Nothing</option>
                    <?php foreach ($prefixesModel = ForumPrefixModel::getInstance()->getPrefixes() as $prefix): ?>
                        <option value="<?= $prefix->getId() ?>"
                            <?= ($topic->getPrefixName() === $prefix->getName() ? 'selected' : '') ?>>
                            <?= $prefix->getName() ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="move">Déplacer :</label>
                <select id="move" name="move">
                    <?php foreach ($categoryModel->getCategories() as $cat): ?>
                        <option disabled>──── <?= $cat->getName() ?> ────</option>
                        <?php foreach ($forumModel->getForumByCat($cat->getId()) as $forumObj): ?>
                            <option value="<?= $forumObj->getId() ?>" <?= ($forumObj->getName() === $topic->getForum()->getName() ? 'selected' : '') ?>><?= $forumObj->getName() ?></option>
                            <?php foreach ($forumModel->getSubsForums($forumObj->getId()) as $subForum): ?>
                                <option value="<?= $subForum['subforum']->getId() ?>" <?= ($subForum['subforum']->getName() === $topic->getForum()->getName() ? 'selected' : '') ?>> <?= str_repeat("\u{00A0}\u{00A0}\u{00A0}\u{00A0}\u{00A0}\u{00A0}", $subForum['depth']) ?> ↪ <?= $subForum['subforum']->getName() ?></option>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </select>
                <div>
                    <a href="<?= $topic->trashLink() ?>">Trash</a>
                    <button type="submit">Save</button>
                </div>
            </form>
        <?php endif; ?>
    <?php endforeach; ?>

    <!--page-->
    <?php if ($totalPage > '1'): ?>
        <div>
            <?php if ($currentPage !== '1'): ?>
                <a href="fp1"><<</a>
                <a href="fp<?= $currentPage - 1 ?>"><</a>
            <?php endif; ?>
            <span><?= $currentPage ?>/<?= $totalPage ?></span>
            <?php if ($currentPage !== $totalPage): ?>
                <a href="fp<?= $currentPage + 1 ?>">></a>
                <a href="fp<?= $totalPage ?>">>></a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
