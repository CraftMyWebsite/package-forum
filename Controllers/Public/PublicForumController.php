<?php
namespace CMW\Controller\Forum\Public;

use CMW\Controller\Forum\Admin\ForumPermissionController;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Requests\Request;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Forum\ForumCategoryModel;
use CMW\Model\Forum\ForumModel;
use CMW\Model\Forum\ForumResponseModel;
use CMW\Model\Forum\ForumSettingsModel;
use CMW\Model\Forum\ForumTopicModel;
use CMW\Utils\Redirect;

/**
 * Class: @PublicForumController
 * @package Forum
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class PublicForumController extends AbstractController
{
    #[Link("/c/:catSlug/f/:forumSlug/fp:forumPage", Link::GET, ['.*?'], "/forum")]
    public function publicForumView(Request $request, string $catSlug, string $forumSlug, int $forumPage): void
    {
        $visitorCanViewForum = ForumSettingsModel::getInstance()->getOptionValue("visitorCanViewForum");

        if ($visitorCanViewForum === "0") {
            ForumPermissionController::getInstance()->redirectIfNotHavePermissions("user_view_forum");
        }

        $category = ForumCategoryModel::getInstance()->getCatBySlug($catSlug);
        $forum = forumModel::getInstance()->getForumBySlug($forumSlug);

        if (!$category->isUserAllowed()) {
            Flash::send(Alert::ERROR, "Forum", "Cette catégorie est privé !");
            Redirect::redirect("forum");
        }
        if (!$forum->isUserAllowed()) {
            Flash::send(Alert::ERROR, "Forum", "Ce forum est privé !");
            Redirect::redirect("forum");
        }

        $responsePerPage = ForumSettingsModel::getInstance()->getOptionValue("topicPerPage");
        $offset = ($forumPage-1)*$responsePerPage;
        $totalPage = strval(ceil( ForumModel::getInstance()->countTopicInForum($forum->getId())/$responsePerPage));
        $topics = ForumTopicModel::getInstance()->getTopicByForumAndOffset($forum->getId(), $offset, $responsePerPage);
        preg_match("/\/fp(\d+)/", $_SERVER['REQUEST_URI'], $matches);
        $currentPage = $matches[1];

        $forumModel = forumModel::getInstance();
        $categoryModel = ForumCategoryModel::getInstance();
        $iconNotRead = ForumSettingsModel::getInstance()->getOptionValue("IconNotRead");
        $iconNotReadColor = ForumSettingsModel::getInstance()->getOptionValue("IconNotReadColor");
        $iconImportant = ForumSettingsModel::getInstance()->getOptionValue("IconImportant");
        $iconImportantColor = ForumSettingsModel::getInstance()->getOptionValue("IconImportantColor");
        $iconPin = ForumSettingsModel::getInstance()->getOptionValue("IconPin");
        $iconPinColor = ForumSettingsModel::getInstance()->getOptionValue("IconPinColor");
        $iconClosed = ForumSettingsModel::getInstance()->getOptionValue("IconClosed");
        $iconClosedColor = ForumSettingsModel::getInstance()->getOptionValue("IconClosedColor");

        $view = new View("Forum", "forum");
        $view->addVariableList(["currentPage" => $currentPage,"totalPage" => $totalPage,"forumModel" => $forumModel, "categoryModel" => $categoryModel,"topics" => $topics, "forum" => $forum, "topicModel" => ForumTopicModel::getInstance(), "responseModel" => ForumResponseModel::getInstance(), "iconNotRead" => $iconNotRead, "iconImportant" => $iconImportant, "iconPin" => $iconPin, "iconClosed" => $iconClosed, "category" => $category , "iconNotReadColor" => $iconNotReadColor, "iconImportantColor" => $iconImportantColor, "iconPinColor" => $iconPinColor, "iconClosedColor" => $iconClosedColor]);
        $view->view();
    }
}
