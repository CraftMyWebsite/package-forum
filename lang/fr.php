<?php

return [
    "category" => [
        "add" => [
            "title" => "Forum | Catégorie | Ajouter",
            "description" => "Ajouter une nouvelle catégorie sur votre site",
            "card_title" => "Ajouter une catégorie",
        ],
        "list" => [
            "title" => "Forum | Catégorie",
            "description" => "Gérez les catégories de votre forum",
            "card_title" => "Liste de toutes vos catégories",
        ],
        "delete" => [
            "success" => "Catégorie supprimée avec succès !",
        ],
        "name" => "Nom de la catégorie",
        "description" => "Description de la catégorie",
        "toaster" => [
            "success" => "Catégorie ajoutée avec succès !",
            "error" => [
                "empty_input" => "Un champ est manquant !",
            ],
        ],
    ],

    "forum" => [
        "list" => [
            "title" => "Forum | Forum",
            "desc" => "Gérez les forums de votre forum",
            "card_title" => "Liste de tous vos forums",
        ],
        "add" => [
            "title" => "Forum | Ajout",
            "desc" => "Ajoutez des forums à votre forum",
            "card_title" => "Ajouter un forum",
            "toaster" => [
                "success" => "Forum ajouté avec succès !"
            ],
        ],
        "delete" => [
            "success" => "Forum supprimé avec succès !",
        ],
        "name" => "Nom du forum",
        "description" => "Description du forum"
    ],

    "topic" => [
        "add" => [
            "success" => "Topic ajouté avec succès !",
        ],
        "pinned" => [
            "success" => "Vous avez épinglé ce topic avec succès !"
        ],
        "unpinned" => [
            "success" => "Vous avez désépinglé ce topic avec succès !"
        ],
        "replies" => [
            "success" => "Réponse ajoutée !",
            "errors" => [
                "disallow_replies" => "Les réponses sont désactivées sur ce topic !",
            ],
        ],
    ],

    "reply" => [
        "delete" => [
            "success" => "Réponse supprimée avec succès !",
            "errors" => [
                "no_access" => "Vous ne pouvez pas supprimer cette réponse !"
            ],
        ],
    ],

    "id" => "ID",
    "name" => "Nom",
    "description" => "Description",
    "action" => "Action",
    "parent" => "Parent",
    "categories" => "Catégories",

    "btn" => [
        "add_category" => "Ajouter une catégorie",
        "add_forum" => "Ajouter un forum",
    ],
];