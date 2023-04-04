<?php
/** @var \CMW\Model\Forum\ForumModel $forumModel */
/** @var \CMW\Model\Forum\CategoryModel $categoryModel */
$title = "Titre de la page";
$description = "Description de votre page";
?>
<section>
    <div class="container">
        <?php foreach ($categoryModel->getCategories() as $category) : ?>
            <h1><i class="<?= $category->getIcon() ?>"></i> <?= $category->getId() . ". " . $category->getName() ?></h1>
            <small><?= $category->getCreated() . ". " . $category->getUpdate() ?></small>
            <div class="container">
                <?php foreach ($forumModel->getForumByParent($category->getId()) as $forumObj): ?>
                    <h3 style="margin-left: 3em">
                        <i class="<?= $forumObj->getIcon() ?>"></i> <?= $forumObj->getId() . ". " . $forumObj->getName() ?>
                        Nombre de topic :<?= $forumModel->countTopicInForum($forumObj->getId()) ?><br>
                        <a href="/<?= $forumObj->getLink() ?>">Aller vers ce Forum</a>
                    </h3>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>