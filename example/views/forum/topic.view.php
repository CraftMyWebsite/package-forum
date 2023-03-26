<?php
/** @var \CMW\Model\Forum\ResponseModel $responseModel */

/** @var \CMW\Entity\Forum\topicEntity $topic
 */

use CMW\Manager\Security\SecurityManager;

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
            <?php if (!$responseModel->countResponseInTopic($topic->getId())): ?>
                <h4 style="text-align: center">Aucune réponse...</h4>
            <?php endif; ?>
            <?php foreach ($responseModel->getResponseByTopic($topic->getId()) as $response) : ?>
                <h4><?= $response->getContent() ?></h4>
                <h6><?= $response->getUser()->getUsername() ?></h6>
                <button></button>
            <?php endforeach; ?>
        </div>
        <hr>
    </section>

<?php if($topic->isDisallowReplies()): ?>

    <section>
        Les réponses sont désactivés sur ce topic.
    </section>

<?php else: ?>

    <section>
        <form action="" method="post">
            <?php (new SecurityManager())->insertHiddenToken() ?>
            <label style="display:block;" for="topicResponse">Votre réponse : </label>
            <input hidden type="text" name="topicId" value="<?= $topic->getId() ?>">
            <textarea required name="topicResponse" id="topicResponse" cols="30" rows="10"></textarea>
            <input type="submit" value="Envoyer !">
        </form>
    </section>

<?php endif; ?>