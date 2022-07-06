<?php

global $router;

//Todo Try to remove that...

use CMW\Controller\Forums\forumsController;
use CMW\Router\router;

require_once('lang/' . getenv("LOCALE") . '.php');


/* Administration scope of package */
$router->scope('/cmw-admin/forum/categories', function (Router $router) {

    $forumController = new forumsController();

    $router->getAndPost("/add", "forums#adminAddCategoryView", "forums#adminAddCategoryPost");
    $router->get("/list", "forums#adminListCategoryView");
    $router->get('/delete/:id', function ($id) use ($forumController) {
        $forumController->adminDeleteCategoryPost($id);
    })->with('id', '[0-9]+');

});

$router->scope('/cmw-admin/forum/forums', function (Router $router) {

    $forumController = new forumsController();

    $router->getAndPost("/add", "forums#adminAddForumView", "forums#adminAddForumPost");
    $router->get("/list", "forums#adminListForumView");
    $router->get('/delete/:id', function ($id) use ($forumController) {
        $forumController->adminDeleteCategoryPost($id);
    })->with('id', '[0-9]+');

});

$router->scope("/forum", function(Router $router) {
    $forumController = new forumsController();


    $router->get("/", "forums#publicBaseView");

    $router->get("/f/:forumSlug", function($slug) use ($forumController) {
        $forumController->publicForumView($slug);
    })->with('slug', '.*?');

    $router->get("/t/:topicSlug", function($slug) use ($forumController) {
        $forumController->publicTopicView($slug);
    })->with('slug', '.*?');

    $router->post("/t/:topicSlug", function($slug) use ($forumController) {
        $forumController->publicTopicPost($slug);
    })->with('slug', '.*?');
});
