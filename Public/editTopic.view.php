<?php

use CMW\Controller\Users\UsersController;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Utils\Website;

/* @var \CMW\Entity\Forum\ForumCategoryEntity $category */
/* @var CMW\Entity\Forum\ForumEntity $forum */
/* @var CMW\Entity\Forum\ForumTopicEntity $topic */

Website::setTitle('Forum');
Website::setDescription('Éditez un topic');
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
            <i class="fa-solid fa-chevron-right"></i>
            <a href="<?= $forum->getLink() ?>"><?= $forum->getName() ?></a>
            <i class="fa-solid fa-chevron-right"></i>
            <a href="<?= $topic->getLink($category->getLink(), $forum->getSlug()) ?>"><?= $topic->getName() ?></a>
        </div>
    </div>

<?php if (UsersController::isUserLogged()): ?>
    <section style="border: 1px #b4aaaa solid; border-radius: 9px; margin-top: 10px; padding: .5rem">
        <h4>Edition du topic : <?= $topic->getName() ?></b></h4>
        <form action="" method="post">
            <?php (new SecurityManager())->insertHiddenToken() ?>
            <input type="text" name="topicId" hidden value="<?= $topic->getId() ?>">
            <label for="title">Titre du topic* :</label>
            <input name="name" id="title" type="text" style="display: block; width: 100%" placeholder="Titre du topic" <?= $topic->getName() ?> required>

            <label for="last_name">Tags : <small>Séparez vos tags par ','</small></label>
            <input name="tags" type="text" id="last_name" style="display: block; width: 100%" value="<?php foreach ($topic->getTags() as $tag) { echo '' . $tag->getContent() . ','; } ?>" placeholder="Tag1,Tag2,Tag3">

            <label>Options :</label>
            <div style="display: flex; gap: .4rem">
                <input id="follow" type="checkbox" name="followTopic" class="w-4 h-4 border border-gray-300 rounded bg-gray-50" checked>
                <label for="follow" class="ml-2 text-sm font-medium">Suivre la discussion (alérter par mail)</label>
            </div>
            <label>Contenue* :</label>
            <textarea minlength="20" name="content"  class="tinymce"><?= $topic->getContent() ?></textarea>
            <div class="text-center mt-2">
                <button type="submit"><i class="fa-solid fa-pen-to-square"></i> Éditer</button>
            </div>
        </form>
    </section>
<?php else: ?>
    <h4>Vous devez vous connecter pour pouvoir modifier ce topic !</h4>
<?php endif; ?>