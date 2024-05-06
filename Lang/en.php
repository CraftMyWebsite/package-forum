<?php

return [
    "category" => [
        "add" => [
            "title" => "Forum | Category | Add",
            "description" => "Add a new category to your site",
            "card_title" => "Add a category",
        ],
        "list" => [
            "title" => "Forum | Category",
            "description" => "Manage your forum categories",
            "card_title" => "List of all your categories",
        ],
        "delete" => [
            "success" => "Category delete with success !",
        ],
        "name" => "Category name",
        "description" => "Category Description",
        "toaster" => [
            "success" => "Category added with success !",
            "error" => [
                "empty_input" => "Empty input !",
            ],
        ],
    ],

    "forum" => [
        "list" => [
            "title" => "Forum | Forum",
            "desc" => "erate the forums of your forum",
            "card_title" => "List of all your forums",
        ],
        "add" => [
            "title" => "Forum | Add",
            "desc" => "Add forum to your forum",
            "card_title" => "Add a forum",
            "toaster" => [
                "success" => "Forum added with success !",
            ],
        ],
        "delete" => [
            "success" => "Forum delete with success !",
        ],
        "name" => "Name of forum",
        "description" => "Forum description",
    ],

    "topic" => [
        "add" => [
            "success" => "Topic added with success !",
        ],
        "pinned" => [
            "success" => "You pinned this topic with success !",
        ],
        "unpinned" => [
            "success" => "You unpinned this topic with success !",
        ],
        "replies" => [
            "success" => "Réponse ajoutée !",
            "errors" => [
                "disallow_replies" => "Replies are disabled for this topic !",
            ],
        ],
    ],

    "reply" => [
        "delete" => [
            "success" => "Reply delete with success !",
            "errors" => [
                "no_access" => "You can't delete this reply !",
            ],
        ],
    ],

    "id" => "ID",
    "name" => "Name",
    "description" => "Description",
    "action" => "Action",
    "parent" => "Parent",
    "categories" => "Catégories",

    "btn" => [
        "add_category" => "Add a catégory",
        "add_forum" => "Add a forum",
    ],

    "permissions" => [
        "forum" => [
            "categories" => [
                "list" => "Show categories",
                "add" => "Add categories",
                "delete" => "Delete categories",
            ],
            "add" => "Add",
            "delete" => "Delete",
        ],
    ],
];