<?php

use CMW\Controller\Users\UsersController;
use CMW\Controller\Users\UsersSessionsController;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Forum\ForumFollowedModel;
use CMW\Model\Forum\ForumPermissionRoleModel;
use CMW\Utils\Website;

/* @var \CMW\Entity\Forum\ForumCategoryEntity $category */
/* @var CMW\Model\Forum\ForumModel $forumModel */
/* @var CMW\Entity\Forum\ForumEntity $forum */
/* @var CMW\Controller\Forum\ForumSettingsController $iconNotRead */
/* @var CMW\Controller\Forum\ForumSettingsController $iconImportant */
/* @var CMW\Controller\Forum\ForumSettingsController $iconPin */
/* @var CMW\Controller\Forum\ForumSettingsController $iconClosed */
/* @var CMW\Model\Forum\ForumFeedbackModel $feedbackModel */
/* @var CMW\Entity\Forum\ForumTopicEntity $topic */
/* @var CMW\Entity\Forum\ForumResponseEntity[] $responses */
/* @var CMW\Controller\Forum\ForumSettingsController $iconNotReadColor */
/* @var CMW\Controller\Forum\ForumSettingsController $iconImportantColor */
/* @var CMW\Controller\Forum\ForumSettingsController $iconPinColor */
/* @var CMW\Controller\Forum\ForumSettingsController $iconClosedColor */

Website::setTitle('Forum');
Website::setDescription('Lisez les sujets et les réponses de la communauté');
$i = 0;
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
            <a href="<?= $parent->getLink() ?>"><?= $parent->getName() ?></a>
        <?php endforeach; ?>
        <i class="fa-solid fa-chevron-right"></i>
        <a href=""><?= $topic->getName() ?></a>
    </div>
    <form action="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>forum/search" method="POST">
        <?php SecurityManager::getInstance()->insertHiddenToken() ?>
        <input type="text" name="for" placeholder="Rechercher">
        <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
    </form>
</div>

<?php if ($totalPage > '1'): ?>
    <div style="display: flex; justify-self: center">
        <div class="flex justify-center">
            <?php if ($currentPage !== '1'): ?>
                <a href="p1">
                    <i class="fa-solid fa-chevron-left"></i><i class="fa-solid fa-chevron-left"></i></a>
                <a href="p<?= $currentPage - 1 ?>">
                    <i class="fa-solid fa-chevron-left"></i></a>
            <?php endif; ?>
            <span><?= $currentPage ?>/<?= $totalPage ?></span>
            <?php if ($currentPage !== $totalPage): ?>
                <a href="p<?= $currentPage + 1 ?>"<i class="fa-solid fa-chevron-right"></i></a>
                <a href="p<?= $totalPage ?>"<i class="fa-solid fa-chevron-right"></i><i class="fa-solid fa-chevron-right"></i></a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<section style="border: 1px #b4aaaa solid; border-radius: 9px; margin-top: 10px; padding: .5rem">
    <div style="display: flex; justify-content: space-between">
        <h4>
            <?php if ($topic->getPrefixId()): ?><span class="px-2 text-white rounded-lg"
                                                      style="color: <?= $topic->getPrefixTextColor() ?>; background: <?= $topic->getPrefixColor() ?>"><?= $topic->getPrefixName() ?></span> <?php endif; ?><?= $topic->getName() ?>
        </h4>
        <div style="display: flex; gap: .9rem">
            <?php if ($topic->isImportant()): ?>
                <i style='color: <?= $iconImportantColor ?>' data-tooltip-target="tooltip-important"
                   class="<?= $iconImportant ?>"></i>
            <?php endif; ?>
            <?php if ($topic->isPinned()): ?>
                <i style='color: <?= $iconPinColor ?>' data-tooltip-target="tooltip-pined" class="<?= $iconPin ?>"></i>
            <?php endif; ?>
            <?php if ($topic->isDisallowReplies()): ?>
                <i style='color: <?= $iconClosedColor ?>' data-tooltip-target="tooltip-closed" class="<?= $iconClosed ?>"></i>
            <?php endif; ?>
        </div>
    </div>
    <p><small>Discussion dans crée par <?= $topic->getUser()->getPseudo() ?>,
            le <?= $topic->getCreated() ?></small></p>
    <?php if ($topic->getTags() === []): ?>
        <p><small>Ce topic ne possède pas de tags</small></p>
    <?php else: ?>
        <p><small>Tags :</small>
            <?php foreach ($topic->getTags() as $tag): ?>
                <small><span><?= $tag->getContent() ?></span></small>
            <?php endforeach; ?>
        </p>
    <?php endif; ?>

    <section style="border: 1px solid #b4aaaa; border-radius: 5px; padding: .6rem">
        <div style="display: flex; justify-content: space-between;">
            <p><?= $topic->getCreated() ?></p>
            <div>
                <?php if (UsersController::isUserLogged()): ?>
                    <?php if (ForumFollowedModel::getInstance()->isFollower($topic->getId(), UsersSessionsController::getInstance()->getCurrentUser()?->getId())): ?>
                        <a href="<?= $topic->unfollowTopicLink() ?>"><i
                                class="fa-solid fa-eye-slash text-blue-500 mr-2"></i></a>
                    <?php else: ?>
                        <a href="<?= $topic->followTopicLink() ?>"><i
                                class="fa-solid fa-eye text-blue-500 mr-2"></i></a>
                    <?php endif ?>
                <?php endif; ?>
            </div>
        </div>
        <div style="display: flex; flex-wrap: wrap; gap: 1rem; padding: .5rem;">
            <div style="flex: 0 0 20%; text-align: center; border: 1px solid #b4aaaa; border-radius: 5px;">
                <div>
                    <img style="object-fit: fill; max-height: 144px; max-width: 144px" width="144px" height="144px" src="<?= $topic->getUser()->getUserPicture()->getImage() ?>"/>
                </div>
                <h5><?= $topic->getUser()->getPseudo() ?></h5>
                <div>
                    <small><?= ForumPermissionRoleModel::getInstance()->getHighestRoleByUser($topic->getUser()->getId())->getName() ?></small>
                </div>
                <div style="display: flex; justify-content: center; gap: 2rem">
                    <div>
                        <p><i class="fa-solid fa-comments fa-xs text-gray-600"></i></p>
                        <small><?= $responseModel->countResponseByUser($topic->getUser()->getId()) ?></small>
                    </div>
                    <div>
                        <p><i class="fa-solid fa-thumbs-up fa-xs text-gray-600"></i></p>
                        <small><?= $feedbackModel->countTopicFeedbackByUser($topic->getUser()->getId()) ?></small>
                    </div>
                </div>
            </div>
            <div style="flex: 0 0 78%;">
                <div style="border: 1px solid #b4aaaa; border-radius: 5px; padding: .6rem">
                    <?= $topic->getContent() ?>
                    <div>
                        <small><?= $topic->getUser()->getPseudo() ?>, <?= $topic->getCreated() ?></small>
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between">
                    <div>
                        <?php foreach ($feedbackModel->getFeedbacks() as $feedback): ?>
                            <?php if ($feedback->userCanTopicReact($topic->getId())): ?>
                                <?php if (UsersController::isUserLogged()): ?>
                                    <?php if ($feedback->getFeedbackTopicReacted($topic->getId()) == $feedback->getId()): ?>
                                        <a href="<?= $topic->getFeedbackDeleteTopicLink($feedback->getId()) ?>">
                                            <img class="mr-1" alt="..." style="max-width: 20px; max-height: 20px"
                                                 src="<?= $feedback->getImage() ?>"></img>
                                            <?= $feedback->countTopicFeedbackReceived($topic->getId()) ?>
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= $topic->getFeedbackChangeTopicLink($feedback->getId()) ?>">
                                            <img class="mr-1" alt="..." style="max-width: 20px; max-height: 20px"
                                                 src="<?= $feedback->getImage() ?>"></img>
                                            <?= $feedback->countTopicFeedbackReceived($topic->getId()) ?>
                                        </a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div>
                                        <img class="mr-1" alt="..." style="max-width: 20px; max-height: 20px"
                                             src="<?= $feedback->getImage() ?>"></img>
                                        <?= $feedback->countTopicFeedbackReceived($topic->getId()) ?>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <a href="<?= $topic->getFeedbackAddTopicLink($feedback->getId()) ?>">
                                    <img class="mr-1" alt="..." style="max-width: 20px; max-height: 20px"
                                         src="<?= $feedback->getImage() ?>"></img>
                                    <?= $feedback->countTopicFeedbackReceived($topic->getId()) ?>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <div>
                        <?php if ($topic->isSelfTopic()): ?>
                            <a href="<?= $topic->editTopicLink() ?>">
                                <i class="fa-solid fa-edit"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</section>

<section style="border: 1px #b4aaaa solid; border-radius: 9px; margin-top: 10px; padding: .5rem">
    <h4 style="text-align: center">Réponses</h4>
    <?php foreach ($responses as $response): ?>
    <div id="<?= $response->getId() ?>" style="border: 1px solid #b4aaaa; border-radius: 5px; padding: .6rem; margin-bottom: 10px">
        <div style="display: flex; justify-content: space-between">
            <p><?= $response->getCreated() ?></p>
            <div>
                <span class="mr-2"><?= $response->isTopicAuthor() ? 'Auteur du topic' : '' ?></span>
                <span
                    onclick="copyURL('<?= Website::getProtocol() . '://' . $_SERVER['HTTP_HOST'] . EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'forum/c/' . $category->getSlug() . '/f/' . $forum->getSlug() . '/t/' . $response->getResponseTopic()->getSlug() . '/p' . $currentPage . '/#' . $response->getId() ?>')"
                    class="text-gray-700 hover:text-blue-600"><i class="fa-solid fa-share-nodes"></i></span>
                <span class="ml-2">#<?= $response->getResponsePosition() ?></span>
            </div>
        </div>

        <div style="display: flex; flex-wrap: wrap; gap: 1rem; padding: .5rem;">
            <div style="flex: 0 0 20%; text-align: center; border: 1px solid #b4aaaa; border-radius: 5px;">
                <div>
                    <img style="object-fit: fill; max-height: 144px; max-width: 144px" width="144px" height="144px" src="<?= $response->getUser()->getUserPicture()->getImage() ?>"/>
                </div>
                <h5><?= $response->getUser()->getPseudo() ?></h5>
                <div>
                    <small><?= ForumPermissionRoleModel::getInstance()->getHighestRoleByUser($response->getUser()->getId())->getName() ?></small>
                </div>
                <div style="display: flex; justify-content: center; gap: 2rem">
                    <div>
                        <p><i class="fa-solid fa-comments fa-xs text-gray-600"></i></p>
                        <small><?= $responseModel->countResponseByUser($response->getUser()->getId()) ?></small>
                    </div>
                    <div>
                        <p><i class="fa-solid fa-thumbs-up fa-xs text-gray-600"></i></p>
                        <small><?= $feedbackModel->countTopicFeedbackByUser($response->getUser()->getId()) ?></small>
                    </div>
                </div>
            </div>
            <div style="flex: 0 0 78%;">
                <div style="border: 1px solid #b4aaaa; border-radius: 5px; padding: .6rem">
                    <?= $response->getContent() ?>
                    <div>
                        <small><?= $response->getUser()->getPseudo() ?>, <?= $response->getCreated() ?></small>
                    </div>
                </div>
                <div style="display: flex; justify-content: space-between">
                    <div>
                        <?php foreach ($feedbackModel->getFeedbacks() as $responseFeedback): ?>
                            <?php if ($responseFeedback->userCanResponseReact($response->getId())): ?>
                                <?php if (UsersController::isUserLogged()): ?>
                                    <?php if ($responseFeedback->getFeedbackResponseReacted($response->getId()) === $responseFeedback->getId()): ?>
                                        <a href="<?= $response->getFeedbackDeleteResponseLink($responseFeedback->getId()) ?>">
                                            <img class="mr-1" alt="..." style="max-width: 20px; max-height: 20px"
                                                 src="<?= $responseFeedback->getImage() ?>"></img>
                                            <?= $responseFeedback->countResponseFeedbackReceived($response->getId()) ?>
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= $response->getFeedbackChangeResponseLink($responseFeedback->getId()) ?>">
                                            <img class="mr-1" alt="..." style="max-width: 20px; max-height: 20px"
                                                 src="<?= $responseFeedback->getImage() ?>"></img>
                                            <?= $responseFeedback->countResponseFeedbackReceived($response->getId()) ?>
                                        </a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div>
                                        <img class="mr-1" alt="..." style="max-width: 20px; max-height: 20px"
                                             src="<?= $responseFeedback->getImage() ?>"></img>
                                        <?= $responseFeedback->countResponseFeedbackReceived($response->getId()) ?>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <a href="<?= $response->getFeedbackAddResponseLink($responseFeedback->getId()) ?>">
                                    <img class="mr-1" alt="..." style="max-width: 20px; max-height: 20px"
                                         src="<?= $responseFeedback->getImage() ?>"></img>
                                    <?= $responseFeedback->countResponseFeedbackReceived($response->getId()) ?>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <div>
                        <?php if ($response->isSelfReply()): ?>
                            <a href="<?= $response->trashLink() ?>">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</section>

<?php if ($totalPage > '1'): ?>
    <div style="display: flex; justify-self: center">
        <div class="flex justify-center">
            <?php if ($currentPage !== '1'): ?>
                <a href="p1">
                    <i class="fa-solid fa-chevron-left"></i><i class="fa-solid fa-chevron-left"></i></a>
                <a href="p<?= $currentPage - 1 ?>">
                    <i class="fa-solid fa-chevron-left"></i></a>
            <?php endif; ?>
            <span><?= $currentPage ?>/<?= $totalPage ?></span>
            <?php if ($currentPage !== $totalPage): ?>
                <a href="p<?= $currentPage + 1 ?>"<i class="fa-solid fa-chevron-right"></i></a>
                <a href="p<?= $totalPage ?>"<i class="fa-solid fa-chevron-right"></i><i class="fa-solid fa-chevron-right"></i></a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>


<?php if (!$topic->isDisallowReplies() && UsersController::isUserLogged()): ?>
<section style="border: 1px #b4aaaa solid; border-radius: 9px; margin-top: 10px; padding: .5rem">
    <h4 style="text-align: center">Répondre à ce topic :</h4>
    <div style="display: flex; flex-wrap: wrap; gap: 1rem; padding: .5rem;">
        <div style="flex: 0 0 20%; text-align: center; border: 1px solid #b4aaaa; border-radius: 5px;  height: fit-content">
            <div>
                <img style="object-fit: fill; max-height: 144px; max-width: 144px" width="144px"
                     height="144px"
                     src="<?= UsersSessionsController::getInstance()->getCurrentUser()->getUserPicture()->getImage() ?>"/>
            </div>
            <h5><?= $topic->getUser()->getPseudo() ?></h5>
        </div>
        <div style="flex: 0 0 78%;">
            <form action="" method="post">
                <?php SecurityManager::getInstance()->insertHiddenToken() ?>
                <input hidden type="text" name="topicId" value="<?= $topic->getId() ?>">
                <textarea minlength="20" class="w-full tinymce" name="topicResponse"></textarea>
                <div style="display: flex; justify-content: end">
                    <button type="submit"><i class="fa-solid fa-reply"></i> Poster ma réponse</button>
                </div>
            </form>
        </div>
    </div>
</section>
<?php endif; ?>



<link rel="stylesheet"
      href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'Admin/Resources/Vendors/Izitoast/iziToast.min.css' ?>">
<script
    src="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'Admin/Resources/Vendors/Izitoast/iziToast.min.js' ?>"></script>

<script>
    function copyURL(url) {
        navigator.clipboard.writeText(url)
        iziToast.show(
            {
                titleSize: '14',
                messageSize: '12',
                icon: 'fa-solid fa-check',
                title: "Forum",
                message: "Le liens vers cette réponse à été copié !",
                color: "#20b23a",
                iconColor: '#ffffff',
                titleColor: '#ffffff',
                messageColor: '#ffffff',
                balloon: false,
                close: true,
                pauseOnHover: true,
                position: 'topCenter',
                timeout: 4000,
                animateInside: false,
                progressBar: true,
                transitionIn: 'fadeInDown',
                transitionOut: 'fadeOut',
            });
    }
</script>