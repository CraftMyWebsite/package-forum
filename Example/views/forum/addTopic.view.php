<?php

use CMW\Controller\Forum\Admin\ForumPermissionController;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Controller\Users\UsersController;
use CMW\Utils\Website;

/** @var \CMW\Entity\Forum\ForumCategoryEntity $category */
/** @var \CMW\Entity\Forum\ForumEntity $forum */
/* @var CMW\Model\Forum\ForumModel $forumModel */
/* @var CMW\Controller\Forum\ForumSettingsController $iconNotRead */
/* @var CMW\Controller\Forum\ForumSettingsController $iconImportant */
/* @var CMW\Controller\Forum\ForumSettingsController $iconPin */
/* @var CMW\Controller\Forum\ForumSettingsController $iconClosed */

Website::setTitle("Forum");
Website::setDescription("Ajouter un sujet");
?>
    <!--optional for breadcrumbs-->
    <nav class="flex" aria-label="Breadcrumb">
        <ol>
            <li>
                <a href="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>forum">Home</a>
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


<?php if(UsersController::isUserLogged()): ?>
    <section>
        <h4>Nouveau topic dans : <b><?= $forum->getName() ?></b></h4>
        <form action="" method="post">
            <?php (new SecurityManager())->insertHiddenToken() ?>
            <?php if (UsersController::isAdminLogged() || ForumPermissionController::getInstance()->hasPermission("operator")) : ?>
            <!--ADMIN-->
                <input name="important" value="1" id="important" type="checkbox" >
                <label for="important"><i class="<?= $iconImportant ?> text-orange-500 fa-sm"></i> Important</label>
                <input name="pin" id="pin" type="checkbox" value="">
                <label for="pin"><i class="<?= $iconPin ?> text-red-600 fa-sm"></i> Pin</label>
                <input name="disallow_replies" value="1" id="closed" type="checkbox" >
                <label for="closed" ><i class="<?= $iconClosed ?> text-yellow-300 fa-sm"></i> Closed</label>
                <?php endif; ?>
            <!--PUBLIC-->
            <label for="title" >Title :</label>
            <input name="name" id="title" type="text" required>
            <label for="last_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tags : <small>Separate by ','</small></label>
            <input name="tags" type="text" id="last_name" placeholder="Tag1,Tag2,Tag3">
            <input id="follow" type="checkbox" name="followTopic" checked>
            <label for="follow" >Follow (mail alert)</label>
            <label>Content :</label>
            <textarea minlength="20" name="content" class="tinymce"></textarea>
            <button type="submit" >POST</button>
        </form>
    </section>

<?php else: ?>
    <p>Login before</p>
    <a href="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>login">Login</a>
<?php endif; ?>