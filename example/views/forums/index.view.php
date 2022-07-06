<?php
/** @var \CMW\Model\Forums\forumsModel $forum */
$title = "Titre de la page";
$description = "Description de votre page";
ob_start(); ?>
    <section>
        <div class="container">
            <?php foreach ($forum->getCategories() as $category) : ?>
                <h1><?= $category->getId() . ". " . $category->getName() ?></h1>
                <div class="container">
                    <?php foreach ($forum->getForumByParent($category->getId()) as $forumObj): ?>
                        <h3 style="margin-left: 3em">
                            <?= $forumObj->getId() . ". " . $forumObj->getName() ?>
                            <?= $forum->countTopicInForum($forumObj->getId()) ?>
                            <a href="/<?= $forumObj->getLink() ?>">Aller vers ce Forum</a>
                        </h3>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
<?php $content = ob_get_clean(); ?>