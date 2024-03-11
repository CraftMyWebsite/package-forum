<?php

use CMW\Manager\Security\SecurityManager;
use CMW\Model\Core\ThemeModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Website;

/** @var \CMW\Entity\Forum\ForumTopicEntity[] $results */
/* @var CMW\Model\Forum\ForumResponseModel $responseModel */

Website::setTitle("Forum");
Website::setDescription("Recherchez un sujet dans le forum");
?>

<section>
    <p>Result for : <span class="font-bold"><?= $for ?></span></p>
    <form action="/forum/search" method="POST">
        <?php (new SecurityManager())->insertHiddenToken() ?>
        <input type="text" name="for" placeholder="Rechercher">
        <button type="submit">Search</button>
    </form>
</section>

<section>
        <?php if (empty($results)): ?>
                <h1 >Nothing founded</h1>
        <?php else: ?>
            <div class="w-full shadow-md">
                <div class="flex py-4 bg-gray-100">
                    <div class="md:w-[25%] px-4 font-bold">Topics</div>
                    <div class="hidden md:block w-[40%] font-bold text-center">Content</div>
                    <div class="hidden md:block w-[10%] font-bold text-center">Response</div>
                    <div class="hidden md:block w-[25%] font-bold text-center">by</div>
                </div>
                <?php foreach ($results as $result): ?>

                            <a href="<?= $result->getLink() ?>">
                                <p><?php if ($result->getPrefixId()): ?><span style="color: <?= $topic->getPrefixTextColor() ?>; background: <?= $topic->getPrefixColor() ?>"><?= $result->getPrefixName() ?></span> <?php endif; ?>
                                        <?= mb_strimwidth($result->getName(), 0, 65, '...') ?>
                                        <?= $result->isImportant() ? "
                            <i class='<?= $iconImportant ?>'></i>
                            " : "" ?>

                                    <?= $result->isPinned() ? "
                            <i class='<?= $iconPin ?>'></i>
                             " : "" ?>
                                        <?= $result->isDisallowReplies() ? "
                            <i class='<?= $iconClosed ?>'></i>
                             " : "" ?>
                                    </p>
                            </a>
                        <div><?= mb_strimwidth($result->getContent(), 0, 150, '...') ?></div>
                        <div><?= $responseModel->countResponseInTopic($result->getId()) ?></div>
                        <div>
                            <img src="<?= $result->getUser()->getUserPicture()->getImage() ?>" />
                            <?= $result->getUser()->getPseudo() ?>
                            <?= $result->getCreated() ?>
                        </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
</section>