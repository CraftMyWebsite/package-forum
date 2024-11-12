<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Utils\Website;

/** @var \CMW\Entity\Forum\ForumTopicEntity[] $results */
/* @var CMW\Model\Forum\ForumResponseModel $responseModel */
/* @var CMW\Controller\Forum\ForumSettingsController $iconNotReadColor */
/* @var CMW\Controller\Forum\ForumSettingsController $iconImportantColor */
/* @var CMW\Controller\Forum\ForumSettingsController $iconPinColor */
/* @var CMW\Controller\Forum\ForumSettingsController $iconClosedColor */
/* @var CMW\Controller\Forum\Admin\ForumSettingsController $iconNotRead */
/* @var CMW\Controller\Forum\Admin\ForumSettingsController $iconImportant */
/* @var CMW\Controller\Forum\Admin\ForumSettingsController $iconPin */
/* @var CMW\Controller\Forum\Admin\ForumSettingsController $iconClosed */

Website::setTitle('Forum');
Website::setDescription('Recherchez un sujet dans le forum');
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
            <p>Résultat pour : <b><?= $for ?></b></p>
        </div>
        <form action="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>forum/search" method="POST">
            <?php SecurityManager::getInstance()->insertHiddenToken() ?>
            <input type="text" name="for" placeholder="Rechercher">
            <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>
    </div>

<?php if (empty($results)): ?>
    <div>
        <h1 class="font-extrabold mb-4 text-xl md:text-6xl">Nous n'avons rien trouvé !</h1>
    </div>

<?php else: ?>
    <div style="width: 100%; margin-bottom: 19px; border: 1px #b4aaaa solid; border-radius: 9px; margin-top: 10px">
    <div style="display: flex; flex-wrap: wrap; gap: 1rem; padding: .5rem;">
        <div style="flex: 0 0 30%;">Topics</div>
        <div style="flex: 0 0 39%;">Contenue</div>
        <div style="flex: 0 0 10%; text-align: center">Réponses</div>
        <div style="flex: 0 0 16%; text-align: center">Posté par</div>
    </div>
    <?php foreach ($results as $result): ?>
        <div
            style="display: flex; flex-wrap: wrap; gap: 1rem; padding: .5rem; align-items: center; border-top: #b4aaaa 1px solid">
            <div style="flex: 0 0 30%;">
                <a style="display: flex; justify-content: space-between"
                   href="<?= $result->getLink() ?>">
                    <div style="display: flex; gap: .4rem">
                        <div>
                            <img style="object-fit: fill; max-height: 48px; max-width: 48px" width="48px"
                                 height="48px"
                                 src="<?= $result->getUser()->getUserPicture()->getImage() ?>"/>
                        </div>
                        <div>
                            <div><?php if ($result->getPrefixId()): ?><span class="px-2 text-white rounded-lg"
                                                                            style="color: <?= $result->getPrefixTextColor() ?>; background: <?= $result->getPrefixColor() ?>"><?= $result->getPrefixName() ?></span> <?php endif; ?><?= $result->getName() ?>
                            </div>
                            <div><span class="font-medium"><?= $result->getUser()->getPseudo() ?></span> <span
                                    class="text-sm">le <?= $result->getCreated() ?></span></div>
                        </div>
                    </div>

                    <div style="display: flex; gap: .9rem">
                        <?= $result->isImportant() ? "
                            <i data-tooltip-target='tooltip-important' style='color: $iconImportantColor' class='<?= $iconImportant ?> fa-sm'></i>" : '' ?>
                        <?= $result->isPinned() ? "
                            <i data-tooltip-target='tooltip-pined' style='color: $iconPinColor' class='<?= $iconPin ?> fa-sm'></i>" : '' ?>
                        <?= $result->isDisallowReplies() ? "
                            <i data-tooltip-target='tooltip-closed' style='color: $iconClosedColor' class='<?= $iconClosed ?> fa-sm'></i>" : '' ?>
                    </div>
                </a>
            </div>
            <div style="flex: 0 0 39%; text-align: center"
            "><?= mb_strimwidth($result->getContent(), 0, 150, '...') ?></div>
        <div
            style="flex: 0 0 10%; text-align: center"><?= $responseModel->countResponseInTopic($result->getId()) ?></div>
        <div style="flex: 0 0 16%;">
            <div style="display: flex; align-items: center; gap: .5rem">
                <img style="width: 3rem; border-radius: 100%" alt="user"
                     src="<?= $result->getUser()->getUserPicture()->getImage() ?>"/>
                <a href="#">
                    <div class="ml-2">
                        <div class=""><?= $result->getUser()->getPseudo() ?></div>
                        <div><?= $result->getCreated() ?></div>
                    </div>
                </a>
            </div>
        </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>