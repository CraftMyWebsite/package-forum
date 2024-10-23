<?php

use CMW\Controller\Forum\Admin\ForumPermissionController;
use CMW\Controller\Users\UsersController;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Utils\Website;

/** @var \CMW\Entity\Forum\ForumCategoryEntity $category */
/** @var \CMW\Entity\Forum\ForumEntity $forum */
/* @var CMW\Model\Forum\ForumModel $forumModel */
/* @var CMW\Controller\Forum\ForumSettingsController $iconNotRead */
/* @var CMW\Controller\Forum\ForumSettingsController $iconImportant */
/* @var CMW\Controller\Forum\ForumSettingsController $iconPin */
/* @var CMW\Controller\Forum\ForumSettingsController $iconClosed */
/* @var CMW\Controller\Forum\ForumSettingsController $iconNotReadColor */
/* @var CMW\Controller\Forum\ForumSettingsController $iconImportantColor */
/* @var CMW\Controller\Forum\ForumSettingsController $iconPinColor */
/* @var CMW\Controller\Forum\ForumSettingsController $iconClosedColor */

Website::setTitle('Forum');
Website::setDescription('Ajouter un sujet');
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
                <a href="../../<?= $parent->getLink() ?>"><?= $parent->getName() ?></a>
            <?php endforeach; ?>
        </div>
    </div>
<?php if (UsersController::isUserLogged()): ?>
    <section style="border: 1px #b4aaaa solid; border-radius: 9px; margin-top: 10px; padding: .5rem">
        <h4>Nouveau topic dans : <b><?= $forum->getName() ?></b></h4>
        <form action="" method="post">
            <?php (new SecurityManager())->insertHiddenToken() ?>
            <?php if (UsersController::isAdminLogged() || ForumPermissionController::getInstance()->hasPermission('operator')): ?>
                <!--
                ADMINISTRATION
                -->
                        <h5>Administration</h5>
                        <div style="display: flex">
                            <div style="display: flex ; gap: 0.8rem">
                                <div style="display: flex; gap: 0.2rem">
                                    <div>
                                        <input name="important" value="1" id="important" type="checkbox">
                                    </div>
                                    <label for="important"><i style='color: <?= $iconImportantColor ?>' class="<?= $iconImportant ?> fa-sm"></i> Important</label>
                                </div>
                                <div style="display: flex; gap: 0.2rem">
                                    <div>
                                        <input name="pin" id="pin" type="checkbox" value="">
                                    </div>
                                    <label for="pin"><i style='color: <?= $iconPinColor ?>' class="<?= $iconPin ?> fa-sm"></i> Épingler</label>
                                </div>
                                <div style="display: flex; gap: 0.2rem">
                                    <div>
                                        <input name="disallow_replies" value="1" id="closed" type="checkbox">
                                    </div>
                                    <label for="closed"><i style='color: <?= $iconClosedColor ?>' class="<?= $iconClosed ?> fa-sm"></i> Fermer</label>
                                </div>
                            </div>
                        </div>
            <?php endif; ?>
            <!--
            PUBLIC
            -->
            <h5>Topic*</h5>
            <label for="title">Titre du topic* :</label>
            <input name="name" id="title" type="text" style="display: block; width: 100%" placeholder="Titre du topic" required>

            <label for="last_name">Tags : <small>Séparez vos tags par ','</small></label>
            <input name="tags" type="text" id="last_name" style="display: block; width: 100%" placeholder="Tag1,Tag2,Tag3">

            <label>Options :</label>
            <div style="display: flex; gap: .4rem">
                <input id="follow" type="checkbox" name="followTopic" class="w-4 h-4 border border-gray-300 rounded bg-gray-50" checked>
                <label for="follow" class="ml-2 text-sm font-medium">Suivre la discussion (alérter par mail)</label>
            </div>
            <label>Contenue* :</label>
            <textarea minlength="20" name="content"  class="tinymce"></textarea>
            <div class="text-center mt-2">
                <button type="submit"><i class="fa-solid fa-pen-to-square"></i> Poster</button>
            </div>
        </form>
    </section>
<?php else: ?>
    <h4>Vous devez vous connecter pour pouvoir poster un nouveau topic !</h4>
<?php endif; ?>