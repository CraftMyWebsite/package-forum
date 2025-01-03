CREATE TABLE IF NOT EXISTS cmw_forums_categories
(
    forum_category_id          INT AUTO_INCREMENT PRIMARY KEY,
    forum_category_name        VARCHAR(50) NOT NULL,
    forum_category_slug             VARCHAR(255)        NOT NULL,
    forum_category_icon        VARCHAR(50) NULL,
    forum_category_description TEXT        NULL,
    forum_category_restricted INT(1)  DEFAULT 0,
    forum_category_created     TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    forum_category_updated     TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_forums_settings
(
    forum_settings_id      INT AUTO_INCREMENT PRIMARY KEY,
    forum_settings_name    VARCHAR(50)  NOT NULL,
    forum_settings_value   VARCHAR(200) NULL,
    forum_settings_updated TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_forums_prefixes
(
    forum_prefix_id          INT AUTO_INCREMENT PRIMARY KEY,
    forum_prefix_name        VARCHAR(50) NOT NULL,
    forum_prefix_color       VARCHAR(50) NULL,
    forum_prefix_text_color  VARCHAR(50) NULL,
    forum_prefix_description TEXT        NULL,
    forum_prefix_created     TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    forum_prefix_updated     TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_forums
(
    forum_id          INT AUTO_INCREMENT PRIMARY KEY,
    forum_name        VARCHAR(50)  NOT NULL,
    forum_icon        VARCHAR(50)  NULL,
    forum_slug        VARCHAR(255) NOT NULL,
    forum_description TEXT         NULL,
    forum_restricted INT(1)  DEFAULT 0,
    forum_disallow_topics TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    forum_subforum_id INT          NULL,
    forum_category_id INT          NULL,
    forum_created     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    forum_updated     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_forum_category_id
        FOREIGN KEY (forum_category_id) REFERENCES cmw_forums_categories (forum_category_id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_forum_forum_id
        FOREIGN KEY (forum_subforum_id) REFERENCES cmw_forums (forum_id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_forums_topics
(
    forum_topic_id               INT AUTO_INCREMENT PRIMARY KEY,
    forum_topic_name             TEXT                NOT NULL,
    forum_topic_slug             VARCHAR(255)        NOT NULL,
    forum_topic_content          MEDIUMTEXT          NULL,
    forum_topic_prefix           INT(11)             NULL,
    forum_topic_pinned           TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    forum_topic_disallow_replies TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    forum_topic_important        TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    forum_topic_is_trash         TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    forum_topic_trash_reason     INT(1)              NOT NULL DEFAULT 0,
    user_id                      INT                 NULL,
    forum_id                     INT                 NOT NULL,
    forum_topic_created          TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    forum_topic_updated          TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FULLTEXT INDEX idx_fts (forum_topic_content, forum_topic_name),
    CONSTRAINT fk_forum_id
        FOREIGN KEY (forum_id) REFERENCES cmw_forums (forum_id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_prefix_id
        FOREIGN KEY (forum_topic_prefix) REFERENCES cmw_forums_prefixes (forum_prefix_id) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_topics_user_id
        FOREIGN KEY (user_id) REFERENCES cmw_users (user_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cmw_forums_topics_tags`
(
    `forums_topics_tags_id`       INT         NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `forums_topics_tags_content`  VARCHAR(50) NOT NULL,
    `forums_topics_tags_topic_id` INT         NOT NULL,
    INDEX (`forums_topics_tags_topic_id`),
    CONSTRAINT fk_topic_id FOREIGN KEY (forums_topics_tags_topic_id)
        REFERENCES cmw_forums_topics (forum_topic_id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_forums_response
(
    forum_response_id           INT AUTO_INCREMENT PRIMARY KEY,
    forum_response_content      TEXT                NOT NULL,
    forum_topic_id              INT                 NOT NULL,
    forum_response_is_trash     TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    forum_response_trash_reason INT(1)              NOT NULL DEFAULT 0,
    forum_response_created      TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    forum_response_updated      TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    user_id                     INT                 NOT NULL,
    CONSTRAINT fk_response_forum_topic_id
        FOREIGN KEY (forum_topic_id) REFERENCES cmw_forums_topics (forum_topic_id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_response_user_id
        FOREIGN KEY (user_id) REFERENCES cmw_users (user_id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

INSERT INTO `cmw_forums_settings` (`forum_settings_name`, `forum_settings_value`)
VALUES ('IconNotRead', 'fa-solid fa-eye'),
       ('IconNotReadColor', '#2986d8'),
       ('IconImportant', 'fa-solid fa-triangle-exclamation'),
       ('IconImportantColor', '#f88920'),
       ('IconPin', 'fa-solid fa-thumbtack'),
       ('IconPinColor', '#e60a0a'),
       ('IconClosed', 'fa-solid fa-lock'),
       ('IconClosedColor', '#f4b515'),
       ('visitorCanViewForum', '0'),
       ('responsePerPage', '10'),
       ('topicPerPage', '10'),
       ('needConnectUrl', '1'),
       ('needConnectText', '<i>Vous devez être <a style=''color: #0d6efd'' href=''/login''>connecter</a> pour afficher ce contenue.</i>'),
       ('blinkResponse', '1');


CREATE TABLE IF NOT EXISTS cmw_forums_topics_views
(
    forum_topics_views_id       INT AUTO_INCREMENT PRIMARY KEY,
    forum_topics_views_topic_id INT         NOT NULL,
    forum_topics_views_ip       VARCHAR(50) NOT NULL,
    CONSTRAINT fk_views_topic_id FOREIGN KEY (forum_topics_views_topic_id)
        REFERENCES cmw_forums_topics (forum_topic_id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_forums_feedback
(
    forum_feedback_id   INT AUTO_INCREMENT PRIMARY KEY,
    forum_feedback_image VARCHAR(50) NOT NULL,
    forum_feedback_name VARCHAR(50) NOT NULL
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_forums_topics_feedback
(
    forum_topics_feedback_id INT AUTO_INCREMENT PRIMARY KEY,
    forum_topics_id          INT NOT NULL,
    forum_feedback_id        INT NOT NULL,
    user_id                  INT NOT NULL,
    CONSTRAINT fk_feedback_topics_id FOREIGN KEY (forum_topics_id)
        REFERENCES cmw_forums_topics (forum_topic_id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_feedback_id FOREIGN KEY (forum_feedback_id)
        REFERENCES cmw_forums_feedback (forum_feedback_id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_feedback_user_id FOREIGN KEY (user_id)
        REFERENCES cmw_users (user_id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_forums_response_feedback
(
    forum_response_feedback_id INT AUTO_INCREMENT PRIMARY KEY,
    forum_response_id          INT NOT NULL,
    forum_feedback_id          INT NOT NULL,
    user_id                    INT NOT NULL,
    CONSTRAINT fk_feedback_response_id FOREIGN KEY (forum_response_id)
        REFERENCES cmw_forums_response (forum_response_id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_response_feedback_id FOREIGN KEY (forum_feedback_id)
        REFERENCES cmw_forums_feedback (forum_feedback_id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_response_feedback_user_id FOREIGN KEY (user_id)
        REFERENCES cmw_users (user_id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cmw_forums_roles`
(
    `forums_role_id`          INT(11)  NOT NULL AUTO_INCREMENT,
    `forums_role_name`        TINYTEXT NOT NULL,
    `forums_role_description` TEXT,
    `forums_role_weight`      INT     DEFAULT 0,
    `forums_role_is_default`  TINYINT(1) DEFAULT 0,
    PRIMARY KEY (`forums_role_id`)
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cmw_forums_users_roles`
(
    `id`      INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) NOT NULL,
    `forums_role_id` INT(11) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    KEY `forums_role_id` (`forums_role_id`),
    CONSTRAINT `cmw_forums_users_roles_ibfk_1` FOREIGN KEY (`user_id`)
        REFERENCES `cmw_users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `cmw_forums_users_roles_ibfk_2` FOREIGN KEY (`forums_role_id`)
        REFERENCES `cmw_forums_roles` (`forums_role_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_forums_permissions
(
    forum_permission_id          INT AUTO_INCREMENT PRIMARY KEY,
    forum_permission_parent_id        INT(11) NULL,
    forum_permission_code       VARCHAR(50) NULL,
    CONSTRAINT fk_forum_permission_parent_id FOREIGN KEY (forum_permission_parent_id)
        REFERENCES cmw_forums_permissions (forum_permission_id)ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_forums_roles_permissions
(
    forum_permission_id INT NOT NULL,
    forum_role_id       INT NOT NULL,
    PRIMARY KEY (forum_permission_id, forum_role_id),
    INDEX (forum_role_id),
    CONSTRAINT fk_forum_roles_permission_id FOREIGN KEY (forum_permission_id)
        REFERENCES cmw_forums_permissions (forum_permission_id)ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_forum_roles_permissions_role_id FOREIGN KEY (forum_role_id)
        REFERENCES cmw_forums_roles (forums_role_id)ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_forums_categories_groups_allowed
(
    forums_cat_group_allowed_id INT AUTO_INCREMENT PRIMARY KEY,
    forums_role_id INT(11) NOT NULL,
    forum_category_id  INT(11) NOT NULL,
    CONSTRAINT fk_forums_categories_groups_allowed_role_id FOREIGN KEY (`forums_role_id`)
    REFERENCES cmw_forums_roles (forums_role_id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_forums_categories_groups_allowed_category_id FOREIGN KEY (`forum_category_id`)
    REFERENCES cmw_forums_categories (`forum_category_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
    CHARACTER SET = utf8mb4
    COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cmw_forums_groups_allowed
(
    forums_group_allowed_id INT AUTO_INCREMENT PRIMARY KEY,
    forums_role_id INT(11) NOT NULL,
    forum_id  INT(11) NOT NULL,
    CONSTRAINT fk_forums_groups_allowed_role_id FOREIGN KEY (`forums_role_id`)
    REFERENCES cmw_forums_roles (forums_role_id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_forums_groups_allowed_forum_id FOREIGN KEY (`forum_id`)
    REFERENCES cmw_forums (`forum_id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE = InnoDB
    CHARACTER SET = utf8mb4
    COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cmw_forums_users_blocked`
(
    forums_users_blocked_id      INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    forum_user_is_blocked INT(1)  DEFAULT 0,
    forum_blocked_reason VARCHAR(255) NULL,
    forum_blocked_updated     TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`forums_users_blocked_id`),
    KEY `user_id` (`user_id`),
    CONSTRAINT `cmw_forums_users_blocked_ibfk_1` FOREIGN KEY (`user_id`)
    REFERENCES `cmw_users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE = InnoDB
    CHARACTER SET = utf8mb4
    COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cmw_forums_topic_reported`
(
    forums_reported_topic_id      INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    forum_reported_topic_id INT(11) NOT NULL,
    forum_reported_topic_reason     INT(1)              NOT NULL DEFAULT 0,
    forum_reported_updated     TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`forums_reported_topic_id`),
    KEY `user_id` (`user_id`),
    CONSTRAINT `cmw_forums_topic_reported_user_ibfk_1` FOREIGN KEY (`user_id`)
    REFERENCES `cmw_users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `cmw_forums_topic_reported_topic_ibfk_1` FOREIGN KEY (`forum_reported_topic_id`)
    REFERENCES `cmw_forums_topics` (`forum_topic_id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE = InnoDB
    CHARACTER SET = utf8mb4
    COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cmw_forums_response_reported`
(
    forums_reported_response_id      INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    forum_reported_response_id INT(11) NOT NULL,
    forum_reported_response_reason     INT(1)              NOT NULL DEFAULT 0,
    forum_reported_updated     TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`forums_reported_response_id`),
    KEY `user_id` (`user_id`),
    CONSTRAINT `cmw_forums_response_reported_user_ibfk_1` FOREIGN KEY (`user_id`)
    REFERENCES `cmw_users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `cmw_forums_response_reported_responses_ibfk_1` FOREIGN KEY (`forum_reported_response_id`)
    REFERENCES `cmw_forums_response` (`forum_response_id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE = InnoDB
    CHARACTER SET = utf8mb4
    COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cmw_forums_followed`
(
    forums_followed_id      INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    forum_topic_id INT(11) NOT NULL,
    PRIMARY KEY (`forums_followed_id`),
    KEY `user_id` (`user_id`),
    CONSTRAINT `cmw_forums_followed_user_ibfk_1` FOREIGN KEY (`user_id`)
    REFERENCES `cmw_users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `cmw_forums_followed_topic_ibfk_1` FOREIGN KEY (`forum_topic_id`)
    REFERENCES `cmw_forums_topics` (`forum_topic_id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE = InnoDB
    CHARACTER SET = utf8mb4
    COLLATE = utf8mb4_unicode_ci;

INSERT INTO `cmw_forums_roles` (`forums_role_name`, `forums_role_description`, `forums_role_weight`, `forums_role_is_default`)
VALUES ('Membre', 'Rôle pour les membres', 0, 1),
       ('Modérateur', 'Rôle pour les modérateurs', 10, 0),
       ('Administrateur', 'Rôle pour les administrateurs', 100, 0);

-- Never change the id associated with the perm_code
INSERT INTO `cmw_forums_permissions` (`forum_permission_id`, `forum_permission_parent_id`, `forum_permission_code`)
VALUES (1, NULL, 'operator'),
       (2, NULL, 'user_view_forum'),
       (3, NULL, 'user_view_topic'),
       (4, NULL, 'user_create_topic'),
       (5, NULL, 'user_create_topic_tag'),
       (6, NULL, 'user_create_pool'),
       (7, NULL, 'user_edit_topic'),
       (8, NULL, 'user_edit_tag'),
       (9, NULL, 'user_edit_pool'),
       (10, NULL, 'user_remove_topic'),
       (11, NULL, 'user_react_topic'),
       (12, NULL, 'user_change_react_topic'),
       (13, NULL, 'user_remove_react_topic'),
       (14, NULL, 'user_response_topic'),
       (15, NULL, 'user_response_react'),
       (16, NULL, 'user_response_change_react'),
       (17, NULL, 'user_response_remove_react'),
       (18, NULL, 'admin_change_topic_name'),
       (19, NULL, 'admin_change_topic_tag'),
       (20, NULL, 'admin_change_topic_prefix'),
       (21, NULL, 'admin_set_important'),
       (22, NULL, 'admin_set_pin'),
       (23, NULL, 'admin_set_closed'),
       (24, NULL, 'admin_move_topic'),
       (25, NULL, 'admin_bypass_forum_disallow_topics'),
       (26, NULL, 'user_remove_response'),
       (27, NULL, 'user_edit_response'),
       (28, NULL, 'user_add_file'),
       (29, NULL, 'user_download_file');


INSERT INTO `cmw_forums_roles_permissions` (`forum_permission_id`, `forum_role_id`) VALUES ('1', '3'),('2', '2'), ('3', '2'), ('4', '2'), ('5', '2'), ('6', '2'), ('7', '2'), ('8', '2'), ('9', '2'), ('10', '2'), ('11', '2'), ('12', '2'), ('13', '2'), ('14', '2'), ('15', '2'), ('16', '2'), ('17', '2'), ('21', '2'), ('22', '2'), ('23', '2'),('2', '1'), ('3', '1'), ('4', '1'), ('5', '1'), ('11', '1'), ('12', '1'), ('13', '1'), ('14', '1'), ('15', '1'), ('16', '1'), ('17', '1');


CREATE TABLE IF NOT EXISTS cmw_forums_discord
(
    `forum_discord_id`   INT AUTO_INCREMENT PRIMARY KEY,
    `forum_discord_webhook` VARCHAR(255) NOT NULL,
    `forum_discord_description` VARCHAR(50) NOT NULL,
    `forum_discord_embed_color` VARCHAR(50) NOT NULL,
    `forum_category_id`  INT(11) NULL,
    `forum_prefix_id`  INT(11) NULL,
    `forum_feedback_id`  INT(11) NULL,
    `forum_topics_feedback_id`  INT(11) NULL,
    `forum_response_feedback_id`  INT(11) NULL,
    `forum_topics_views_id`  INT(11) NULL,
    `forum_settings_id`  INT(11) NULL,
    `forum_id`  INT(11) NULL,
    `forum_topic_id`  INT(11) NULL,
    `forums_topics_tags_id`  INT(11) NULL,
    `forum_response_id`  INT(11) NULL,
    CONSTRAINT `discord_biding_forum_category_id` FOREIGN KEY (`forum_category_id`)
        REFERENCES `cmw_forums_categories` (`forum_category_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `discord_biding_forum_prefix_id` FOREIGN KEY (`forum_prefix_id`)
        REFERENCES `cmw_forums_prefixes` (`forum_prefix_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `discord_biding_forum_feedback_id` FOREIGN KEY (`forum_feedback_id`)
        REFERENCES `cmw_forums_feedback` (`forum_feedback_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `discord_biding_forum_topics_feedback_id` FOREIGN KEY (`forum_topics_feedback_id`)
        REFERENCES `cmw_forums_topics_feedback` (`forum_topics_feedback_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `discord_biding_forum_response_feedback_id` FOREIGN KEY (`forum_response_feedback_id`)
        REFERENCES `cmw_forums_response_feedback` (`forum_response_feedback_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `discord_biding_forum_topics_views_id` FOREIGN KEY (`forum_topics_views_id`)
        REFERENCES `cmw_forums_topics_views` (`forum_topics_views_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `discord_biding_forum_settings_id` FOREIGN KEY (`forum_settings_id`)
        REFERENCES `cmw_forums_settings` (`forum_settings_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `discord_biding_forum_id` FOREIGN KEY (`forum_id`)
        REFERENCES `cmw_forums` (`forum_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `discord_biding_forum_topic_id` FOREIGN KEY (`forum_topic_id`)
        REFERENCES `cmw_forums_topics` (`forum_topic_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `discord_biding_forums_topics_tags_id` FOREIGN KEY (`forums_topics_tags_id`)
        REFERENCES `cmw_forums_topics_tags` (`forums_topics_tags_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `discord_biding_forum_response_id` FOREIGN KEY (`forum_response_id`)
        REFERENCES `cmw_forums_response` (`forum_response_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

INSERT INTO cmw_forums_users_roles (`user_id`,`forums_role_id`)
SELECT user_id, 1 FROM cmw_users;

INSERT INTO cmw_forums_users_blocked (`user_id`)
SELECT user_id FROM cmw_users;