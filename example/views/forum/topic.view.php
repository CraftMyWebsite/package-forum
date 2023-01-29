<?php
/** @var \CMW\Model\Forum\forumModel $forumModel */
/** @var \CMW\Entity\Forum\topicEntity $topic
 */

use CMW\Utils\SecurityService;

$title = "Titre de la page";
$description = "Description de votre page";
?>
<section>
    <div class="container">
        <h2><?= "{$topic->getId()}. {$topic->getName()}" ?></h2>
        <p>
            <?= $topic->getContent() ?>
        </p>
    </div>
    <div class="container">
        <h3>Réponses :</h3>
        <?php if (!$forumModel->countResponseInTopic($topic->getId())): ?>
            <h4 style="text-align: center">Aucune réponse...</h4>
        <?php endif; ?>
        <?php foreach ($forumModel->getResponseByTopic($topic->getId()) as $response) : ?>
            <h4><?= $response->getContent() ?></h4>
            <h6><?= $response->getUser()->getUsername() ?></h6>
            <button></button>
        <?php endforeach; ?>
    </div>
    <hr>
</section>

<section>
    <form action="" method="post">
        <?php (new SecurityService())->insertHiddenToken() ?>
        <label style="display:block;" for="topicResponse">Votre réponse : </label>
        <input hidden type="text" name="topicId" value="<?= $topic->getId()?>">
        <textarea required name="topicResponse" id="topicResponse" cols="30" rows="10"></textarea>
        <input type="submit" value="Envoyer !">
    </form>
</section>