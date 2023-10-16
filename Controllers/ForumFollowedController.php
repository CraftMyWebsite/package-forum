<?php

namespace CMW\Controller\Forum;

use CMW\Controller\Core\MailController;
use CMW\Controller\Users\UsersController;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Requests\Request;
use CMW\Manager\Router\Link;
use CMW\Model\Forum\ForumFollowedModel;
use CMW\Model\Forum\ForumTopicModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Redirect;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class: @ForumFollowedController
 * @package Forum
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ForumFollowedController extends AbstractController
{
    /**
     * @param string $userMail
     * @return void
     */
    public function sendMailToFollower(string $userMail, string $responseUrl, string $topicName, string $responseUser, string $responseContent): void
    {
        MailController::getInstance()->sendMail($userMail,"Nouvelle réponse sur $topicName !", "Le topic $topicName que vous suivez à reçu un nouveau méssage : <br> $responseContent <br><br>Envoyé par $responseUser<br><a href='$responseUrl' target='_blank'>Lire la réponse sur le fourm</a>");
    }

    #[NoReturn] #[Link("/c/:catSlug/f/:forumSlug/t/:topicSlug/p:page/follow", Link::GET, ["id" => "[0-9]+"], "/forum")]
    private function followThisTopic(Request $request, string $catSlug, string $forumSlug, string $topicSlug): void
    {
        if (!UsersController::isUserLogged()) {
            Flash::send(Alert::ERROR, "Forum", "Connectez-vous avant de suivre topic.");
            Redirect::redirect('login');
        }

        $topicId = ForumTopicModel::getInstance()->getTopicBySlug($topicSlug)->getId();
        $userId = UsersModel::getCurrentUser()?->getId();

        ForumFollowedModel::getInstance()->addFollower($topicId,$userId);

        Flash::send(Alert::SUCCESS, "Forum","Vous suivez ce topic");

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link("/c/:catSlug/f/:forumSlug/t/:topicSlug/p:page/unfollow", Link::GET, ["id" => "[0-9]+"], "/forum")]
    private function unfollowThisTopic(Request $request, string $catSlug, string $forumSlug, string $topicSlug): void
    {
        if (!UsersController::isUserLogged()) {
            Flash::send(Alert::ERROR, "Forum", "Connectez-vous avant de suivre topic.");
            Redirect::redirect('login');
        }

        $topicId = ForumTopicModel::getInstance()->getTopicBySlug($topicSlug)->getId();
        $userId = UsersModel::getCurrentUser()?->getId();

        ForumFollowedModel::getInstance()->removeFollower($topicId, $userId);

        Flash::send(Alert::SUCCESS, "Forum","Vous ne suivez plus ce topic");

        Redirect::redirectPreviousRoute();
    }
}