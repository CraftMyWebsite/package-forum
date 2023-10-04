<?php
namespace CMW\Controller\Forum;

use CMW\Controller\Core\CoreController;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Requests\Request;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Forum\ForumCategoryModel;
use CMW\Model\Forum\ForumDiscordModel;
use CMW\Model\Forum\ForumModel;
use CMW\Model\Forum\ForumPermissionModel;
use CMW\Model\Forum\ForumResponseModel;
use CMW\Model\Forum\ForumSettingsModel;
use CMW\Model\Forum\ForumTopicModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use CMW\Utils\Website;

/**
 * Class: @PublicForumController
 * @package Forum
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class PublicForumController extends CoreController
{
    #[Link("/c/:catSlug/f/:forumSlug", Link::GET, ['.*?'], "/forum")]
    public function publicForumView(Request $request, string $catSlug, string $forumSlug): void
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

        $forumModel = forumModel::getInstance();
        $categoryModel = ForumCategoryModel::getInstance();
        $iconNotRead = ForumSettingsModel::getInstance()->getOptionValue("IconNotRead");
        $iconImportant = ForumSettingsModel::getInstance()->getOptionValue("IconImportant");
        $iconPin = ForumSettingsModel::getInstance()->getOptionValue("IconPin");
        $iconClosed = ForumSettingsModel::getInstance()->getOptionValue("IconClosed");

        $view = new View("Forum", "forum");
        $view->addVariableList(["forumModel" => $forumModel, "categoryModel" => $categoryModel, "forum" => $forum, "topicModel" => ForumTopicModel::getInstance(), "responseModel" => ForumResponseModel::getInstance(), "iconNotRead" => $iconNotRead, "iconImportant" => $iconImportant, "iconPin" => $iconPin, "iconClosed" => $iconClosed, "category" => $category]);
        $view->view();
    }

    #[Link("/c/:catSlug/f/:forumSlug/add", Link::GET, ['.*?'], "/forum")]
    public function publicForumAddTopicView(Request $request, string $catSlug, string $forumSlug): void
    {
        ForumPermissionController::getInstance()->redirectIfNotHavePermissions("user_create_topic");

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
        if ($forum->disallowTopics() && !ForumPermissionController::getInstance()->hasPermission("operator") || !ForumPermissionController::getInstance()->hasPermission("admin_bypass_forum_disallow_topics")) {
            Flash::send(Alert::ERROR, "Forum", "Ce forum n'autorise pas la création de nouveau topics");
            Redirect::redirectPreviousRoute();
        }

        $iconNotRead = ForumSettingsModel::getInstance()->getOptionValue("IconNotRead");
        $iconImportant = ForumSettingsModel::getInstance()->getOptionValue("IconImportant");
        $iconPin = ForumSettingsModel::getInstance()->getOptionValue("IconPin");
        $iconClosed = ForumSettingsModel::getInstance()->getOptionValue("IconClosed");

        $view = new View("Forum", "addTopic");
        $view->addVariableList(["forum" => $forum, "iconNotRead" => $iconNotRead, "iconImportant" => $iconImportant, "iconPin" => $iconPin, "iconClosed" => $iconClosed, "category" => $category]);
        $view->addScriptBefore("Admin/Resources/Vendors/Tinymce/tinymce.min.js", "Admin/Resources/Vendors/Tinymce/Config/full.js");
        $view->addStyle("Admin/Resources/Vendors/Fontawesome-free/Css/fa-all.min.css");
        $view->view();
    }

    #[Link("/c/:catSlug/f/:forumSlug/add", Link::POST, ['.*?'], "/forum")]
    public function publicForumAddTopicPost(Request $request, string $catSlug, string $forumSlug): void
    {
        ForumPermissionController::getInstance()->redirectIfNotHavePermissions("user_create_topic");

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
        if ($forum->disallowTopics() && !ForumPermissionController::getInstance()->hasPermission("operator") || !ForumPermissionController::getInstance()->hasPermission("admin_bypass_forum_disallow_topics")) {
            Flash::send(Alert::ERROR, "Forum", "Ce forum n'autorise pas la création de nouveau topics");
            Redirect::redirectPreviousRoute();
        }

        $userId = UsersModel::getCurrentUser()->getId();

        [$name, $content, $disallowReplies, $important, $pin, $tags] = Utils::filterInput('name', 'content', 'disallow_replies', 'important', 'pin', 'tags');

        if (!ForumPermissionModel::getInstance()->hasForumPermission($userId, "user_create_topic_tag") && $tags !== "") {
            ForumPermissionController::getInstance()->alertNotHavePermissions("user_create_topic_tag");
            $tags = "";
        }

        $forum = forumModel::getInstance()->getForumBySlug($forumSlug);

        if (is_null($forum) || Utils::containsNullValue($name, $content)) {
            Flash::send("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.internalError"));
            Website::refresh();
            return;
        }

        $res = ForumTopicModel::getInstance()->createTopic($name, $content, $userId, $forum->getId(),
            (is_null($disallowReplies) ? 0 : 1), (is_null($important) ? 0 : 1), (is_null($pin) ? 0 : 1));

        if (is_null($res)) {
            Flash::send("error", LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.internalError"));
            Website::refresh();
            return;
        }

        // Add tags

        $tags = explode(",", $tags);

        foreach ($tags as $tag) {
            //Clean tag
            $tag = mb_strtolower(trim($tag));

            if (empty($tag)) {
                continue;
            }

            ForumTopicModel::getInstance()->addTag($tag, $res->getId());
        }

        Flash::send("success", LangManager::translate("core.toaster.success"),
            LangManager::translate("forum.topic.add.success"));

        ForumDiscordModel::getInstance()->sendDiscordMsgNewTopic($forum->getId(), $name, $forum->getName(), "test", UsersModel::getCurrentUser()->getUserPicture()->getImageName(), UsersModel::getCurrentUser()->getPseudo());

        header("location: ../$forumSlug");
    }
}
