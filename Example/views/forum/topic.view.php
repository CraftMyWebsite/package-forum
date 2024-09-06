<?php

use CMW\Controller\Users\UsersController;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Forum\ForumFollowedModel;
use CMW\Model\Forum\ForumPermissionRoleModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Website;

/* @var CMW\Entity\Forum\ForumCategoryEntity $category */
/* @var CMW\Model\Forum\ForumModel $forumModel */
/* @var CMW\Entity\Forum\ForumEntity $forum */
/* @var CMW\Controller\Forum\ForumSettingsController $iconNotRead */
/* @var CMW\Controller\Forum\ForumSettingsController $iconImportant */
/* @var CMW\Controller\Forum\ForumSettingsController $iconPin */
/* @var CMW\Controller\Forum\ForumSettingsController $iconClosed */
/* @var CMW\Model\Forum\ForumFeedbackModel $feedbackModel */
/* @var CMW\Entity\Forum\ForumTopicEntity $topic */
/* @var CMW\Entity\Forum\ForumResponseEntity[] $responses */
Website::setTitle('Forum');
Website::setDescription('Lisez les sujets et les réponses de la communauté');
?>

<!--optional for breadcrumbs-->
<nav class="flex" aria-label="Breadcrumb">
    <ol>
        <li>
            <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>forum">Home</a>
        </li>
        <li>
            <a href="<?= $category->getLink() ?>"><?= $category->getName() ?></a>
        </li>
        <?php foreach ($forumModel->getParentByForumId($forum->getId()) as $parent): ?>
            <li>
                <a href="<?= $parent->getLink() ?>"><?= $parent->getName() ?></a>
            </li>
        <?php endforeach; ?>
        <li>
            <a href=""><?= $topic->getName() ?></a>
        </li>
    </ol>
</nav>

<!--page selector-->
<?php if ($totalPage > '1'): ?>
    <div>
        <?php if ($currentPage !== '1'): ?>
            <a href="p1"> << </a>
            <a href="p<?= $currentPage - 1 ?>"> < </a>
        <?php endif; ?>
        <p><?= $currentPage ?>/<?= $totalPage ?></p>
        <?php if ($currentPage !== $totalPage): ?>
            <a href="p<?= $currentPage + 1 ?>"> > </a>
            <a href="p<?= $totalPage ?>"> >> </a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<!--topic header-->
<section>
    <div>
        <h4>
            <?php if ($topic->getPrefixId()): ?><span
                style="color: <?= $topic->getPrefixTextColor() ?>; background: <?= $topic->getPrefixColor() ?>"><?= $topic->getPrefixName() ?></span> <?php endif; ?><?= $topic->getName() ?>
        </h4>
        <div>
            <?php if ($topic->isImportant()): ?>
                <i class="<?= $iconImportant ?>"></i>
            <?php endif; ?>
            <?php if ($topic->isPinned()): ?>
                <i class="<?= $iconPin ?>"></i>
            <?php endif; ?>
            <?php if ($topic->isDisallowReplies()): ?>
                <i class="<?= $iconClosed ?>"></i>
            <?php endif; ?>
        </div>
    </div>

    <p><small>Author <?= $topic->getUser()->getPseudo() ?>, <?= $topic->getCreated() ?></small></p>
    <?php if ($topic->getTags() === []): ?>
        <p><small>No tags</small></p>
    <?php else: ?>
        <p><small>Tags :</small>
            <?php foreach ($topic->getTags() as $tag): ?>
                <small><?= $tag->getContent() ?></small>
            <?php endforeach; ?>
        </p>
    <?php endif; ?>
</section>

<!--topic author-->
<section>
    <div>
        <p><?= $topic->getCreated() ?></p>
        <div>
            <?php if (UsersController::isUserLogged()): ?>
                <?php if (ForumFollowedModel::getInstance()->isFollower($topic->getId(), UsersModel::getCurrentUser()?->getId())): ?>
                    <a href="<?= $topic->unfollowTopicLink() ?>">Unfollow</a>
                <?php else: ?>
                    <a href="<?= $topic->followTopicLink() ?>">Follow</a>
                <?php endif ?>
            <?php endif; ?>
            <!--for a better user experience we advise you to use modals for report (view WIPE theme template) :-->
            <a href="<?= $topic->reportLink() ?>">Report</a>
        </div>
    </div>


    <div>
        <div>
            <img src="<?= $topic->getUser()->getUserPicture()->getImage() ?>"/>
            <h5><?= $topic->getUser()->getPseudo() ?></h5>
            <p>
                <small><?= ForumPermissionRoleModel::getInstance()->getHighestRoleByUser($topic->getUser()->getId())->getName() ?></small>
            </p>
        </div>
        <div>
            <p><small>Response :<?= $responseModel->countResponseByUser($topic->getUser()->getId()) ?></small></p>
            <p><small>Feedback :<?= $feedbackModel->countTopicFeedbackByUser($topic->getUser()->getId()) ?></small></p>
        </div>

        <div>
            <p><?= $topic->getContent() ?></p>
            <p><small><?= $topic->getUser()->getPseudo() ?>, <?= $topic->getCreated() ?></small></p>
        </div>

        <div>
            <?php foreach ($feedbackModel->getFeedbacks() as $feedback): ?>
                <?php if ($feedback->userCanTopicReact($topic->getId())): ?>
                    <?php if (UsersController::isUserLogged()): ?>
                        <?php if ($feedback->getFeedbackTopicReacted($topic->getId()) == $feedback->getId()): ?>
                            <a href="<?= $topic->getFeedbackDeleteTopicLink($feedback->getId()) ?>">
                                <img src="<?= $feedback->getImage() ?>">
                                <?= $feedback->countTopicFeedbackReceived($topic->getId()) ?>
                            </a>
                        <?php else: ?>
                            <a" href="<?= $topic->getFeedbackChangeTopicLink($feedback->getId()) ?>">
                            <img alt="..." src="<?= $feedback->getImage() ?>">
                            <?= $feedback->countTopicFeedbackReceived($topic->getId()) ?>
                            <div>
                                <?php foreach ($feedbackModel->getTopicUsersFeedbackByFeedbackId($topic->getId(), $feedback->getId()) as $userId): ?>
                                    <small>- <?= $user = UsersModel::getInstance()->getUserById($userId)->getPseudo() ?></small>
                                <?php endforeach; ?>
                                <p class="p-1"><small><?= $feedback->getName() ?></small></p>
                            </div>
                            </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <div>
                            <img src="<?= $feedback->getImage() ?>">
                            <?= $feedback->countTopicFeedbackReceived($topic->getId()) ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="<?= $topic->getFeedbackAddTopicLink($feedback->getId()) ?>">
                        <img src="<?= $feedback->getImage() ?>">
                        <?= $feedback->countTopicFeedbackReceived($topic->getId()) ?>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <div>
            <?php if ($topic->isSelfTopic()): ?>
                <a href="<?= $topic->editTopicLink() ?>">
                    Edit topic
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!--response-->
<section>
    <?php foreach ($responses as $response): ?>

        <div>
            <p><?= $response->getCreated() ?></p>
            <div>
                <span><?= $response->isTopicAuthor() ? 'Topic author' : '' ?></span>
                <!--optional (see end JS requierement):-->
                <span onclick="copyURL('<?= Website::getProtocol() . '://' . $_SERVER['HTTP_HOST'] . EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'forum/c/' . $category->getSlug() . '/f/' . $forum->getSlug() . '/t/' . $response->getResponseTopic()->getSlug() . '/p' . $currentPage . '/#' . $response->getId() ?>')">copy response ref</span>
                <!--for a better user experience we advise you to use modals for report (view WIPE theme template) :-->
                <a href="<?= $response->getReportLink() ?>">Report</a>
                <span class="ml-2">#<?= $response->getResponsePosition() ?></span>
            </div>
        </div>

        <div class="lg:grid grid-cols-5">
            <img src="<?= $response->getUser()->getUserPicture()->getImage() ?>"/>
        </div>
        <h5><?= $response->getUser()->getPseudo() ?></h5>
        <div>
            <p><small><?= ForumPermissionRoleModel::getInstance()->getHighestRoleByUser($response->getUser()->getId())->getName() ?></small></p>
        </div>
        <div>
            <p><small>Response :<?= $responseModel->countResponseByUser($response->getUser()->getId()) ?></small></p>
            <p><small>Feedback :<?= $feedbackModel->countTopicFeedbackByUser($topic->getUser()->getId()) ?></small></p>
        </div>
        <div>
            <p><?= $response->getContent() ?></p>
            <p><small><?= $response->getUser()->getPseudo() ?>, <?= $response->getCreated() ?></small></p>
        </div>

        <div>
            <?php foreach ($feedbackModel->getFeedbacks() as $responseFeedback): ?>
                <?php if ($responseFeedback->userCanResponseReact($response->getId())): ?>
                    <?php if (UsersController::isUserLogged()): ?>
                        <?php if ($responseFeedback->getFeedbackResponseReacted($response->getId()) === $responseFeedback->getId()): ?>
                            <a href="<?= $response->getFeedbackDeleteResponseLink($responseFeedback->getId()) ?>">
                                <img src="<?= $responseFeedback->getImage() ?>">
                                <?= $responseFeedback->countResponseFeedbackReceived($response->getId()) ?>
                            </a>
                        <?php else: ?>
                            <a href="<?= $response->getFeedbackChangeResponseLink($responseFeedback->getId()) ?>">
                                <img src="<?= $responseFeedback->getImage() ?>">
                                <?= $responseFeedback->countResponseFeedbackReceived($response->getId()) ?>
                            </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <div>
                            <img src="<?= $responseFeedback->getImage() ?>">
                            <?= $responseFeedback->countResponseFeedbackReceived($response->getId()) ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="<?= $response->getFeedbackAddResponseLink($responseFeedback->getId()) ?>">
                        <img src="<?= $responseFeedback->getImage() ?>">
                        <?= $responseFeedback->countResponseFeedbackReceived($response->getId()) ?>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <div class="flex justify-end p-1">
            <?php if ($response->isSelfReply()): ?>
                <a href="<?= $response->trashLink() ?>">Delete</a>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</section>

<!--page selector-->
<?php if ($totalPage > '1'): ?>
    <div>
        <?php if ($currentPage !== '1'): ?>
            <a href="p1"> << </a>
            <a href="p<?= $currentPage - 1 ?>"> < </a>
        <?php endif; ?>
        <p><?= $currentPage ?>/<?= $totalPage ?></p>
        <?php if ($currentPage !== $totalPage): ?>
            <a href="p<?= $currentPage + 1 ?>"> > </a>
            <a href="p<?= $totalPage ?>"> >> </a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if (!$topic->isDisallowReplies() && UsersController::isUserLogged()): ?>
    <section >
        <img src="<?= UsersModel::getCurrentUser()->getUserPicture()->getImage() ?>"/>
        <h5 ><?= UsersModel::getCurrentUser()->getPseudo() ?></h5>
        <form action="" method="post">
            <?php (new SecurityManager())->insertHiddenToken() ?>
            <input hidden type="text" name="topicId" value="<?= $topic->getId() ?>">
            <textarea minlength="20" class="w-full tinymce" name="topicResponse"></textarea>
            <button type="submit">Send</button>
        </form>
    </section>
<?php endif; ?>



<!--optional (JS requierement):-->
<link rel="stylesheet"
      href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'Admin/Resources/Vendors/Izitoast/iziToast.min.css' ?>">
<script
    src="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'Admin/Resources/Vendors/Izitoast/iziToast.min.js' ?>"></script>

<script>
    function copyURL(url) {
        navigator.clipboard.writeText(url)
        iziToast.show(
            {
                titleSize: '16',
                messageSize: '14',
                icon: 'fa-solid fa-check',
                title: "Forum",
                message: "Link to this response copied",
                color: "#41435F",
                iconColor: '#22E445',
                titleColor: '#22E445',
                messageColor: '#fff',
                balloon: false,
                close: false,
                position: 'bottomRight',
                timeout: 5000,
                animateInside: false,
                progressBar: false,
                transitionIn: 'fadeInLeft',
                transitionOut: 'fadeOutRight',
            });
    }
</script>