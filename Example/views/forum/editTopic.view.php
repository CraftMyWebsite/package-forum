<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Controller\Users\UsersController;
use CMW\Utils\Website;

/* @var \CMW\Entity\Forum\ForumCategoryEntity $category */
/* @var CMW\Entity\Forum\ForumEntity $forum */

Website::setTitle("Forum");
Website::setDescription("Éditez un topic");
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



    <h4>Edit : <?= $topic->getName() ?></b></h4>
    <form action="" method="post">
        <?php (new SecurityManager())->insertHiddenToken() ?>
        <input type="text" name="topicId" hidden value="<?= $topic->getId() ?>">
        <label for="title">Title :</label>
        <input name="name" type="text" required value="<?= $topic->getName() ?>">
        <label for="last_name">Tags : </label>
        <input name="tags" type="text" placeholder="Tag1,Tag2,Tag3" value="<?php foreach ($topic->getTags() as $tag) {echo "" . $tag->getContent() . ",";} ?>">
        <label>Content :</label>
        <textarea name="content" class="w-full tinymce"><?= $topic->getContent() ?></textarea>
        <button type="submit">Edit</button>
    </form>
