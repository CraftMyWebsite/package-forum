<?php
/** @var \CMW\Model\Forum\forumModel $forum */
$title = "Titre de la page";
$description = "Description de votre page";
?>
<section>
    <div class="container">
        <?php foreach ($forum->getCategories() as $category) : ?>
            <h1><?= $category->getId() . ". " . $category->getName() ?></h1>
            <div class="container">
                <?php foreach ($forum->getForumByParent($category->getId()) as $forumObj): ?>
                    <h3 style="margin-left: 3em">
                        <?= $forumObj->getId() . ". " . $forumObj->getName() ?>
                        Nombre de topic :<?= $forum->countTopicInForum($forumObj->getId()) ?><br>
                        <a href="/<?= $forumObj->getLink() ?>">Aller vers ce Forum</a>
                    </h3>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>