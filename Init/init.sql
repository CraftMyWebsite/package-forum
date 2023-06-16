CREATE TABLE IF NOT EXISTS cmw_forums_categories
(
    forum_category_id          INT AUTO_INCREMENT PRIMARY KEY,
    forum_category_name        VARCHAR(50) NOT NULL,
    forum_category_icon        VARCHAR(50) NULL,
    forum_category_description TEXT        NULL,
    forum_category_created     TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    forum_category_updated     TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

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
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS cmw_forums_feedback
(
    forum_feedback_id   INT AUTO_INCREMENT PRIMARY KEY,
    forum_feedback_image VARCHAR(50) NOT NULL,
    forum_feedback_name VARCHAR(50) NOT NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

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
  DEFAULT CHARSET = utf8mb4;

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
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS cmw_forums_topics_views
(
    forum_topics_views_id       INT AUTO_INCREMENT PRIMARY KEY,
    forum_topics_views_topic_id INT         NOT NULL,
    forum_topics_views_ip       VARCHAR(50) NOT NULL,
    CONSTRAINT fk_views_topic_id FOREIGN KEY (forum_topics_views_topic_id)
        REFERENCES cmw_forums_topics (forum_topic_id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS cmw_forums_settings
(
    forum_settings_id      INT AUTO_INCREMENT PRIMARY KEY,
    forum_settings_name    VARCHAR(50)  NOT NULL,
    forum_settings_value   VARCHAR(200) NULL,
    forum_settings_updated TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS cmw_forums
(
    forum_id          INT AUTO_INCREMENT PRIMARY KEY,
    forum_name        VARCHAR(50)  NOT NULL,
    forum_icon        VARCHAR(50)  NULL,
    forum_slug        VARCHAR(255) NOT NULL,
    forum_description TEXT         NULL,
    forum_subforum_id INT          NULL,
    forum_category_id INT          NULL,
    forum_created     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    forum_updated     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_forum_category_id
        FOREIGN KEY (forum_category_id) REFERENCES cmw_forums_categories (forum_category_id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_forum_forum_id
        FOREIGN KEY (forum_subforum_id) REFERENCES cmw_forums (forum_id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

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
    CONSTRAINT fk_forum_id
        FOREIGN KEY (forum_id) REFERENCES cmw_forums (forum_id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_prefix_id
        FOREIGN KEY (forum_topic_prefix) REFERENCES cmw_forums_prefixes (forum_prefix_id) ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_topics_user_id
        FOREIGN KEY (user_id) REFERENCES cmw_users (user_id) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS `cmw_forums_topics_tags`
(
    `forums_topics_tags_id`       INT         NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `forums_topics_tags_content`  VARCHAR(50) NOT NULL,
    `forums_topics_tags_topic_id` INT         NOT NULL,
    INDEX (`forums_topics_tags_topic_id`),
    CONSTRAINT fk_topic_id FOREIGN KEY (forums_topics_tags_topic_id)
        REFERENCES cmw_forums_topics (forum_topic_id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

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
  DEFAULT CHARSET = utf8mb4;

INSERT INTO `cmw_forums_settings` (`forum_settings_name`, `forum_settings_value`)
VALUES ('IconNotRead', 'fa-solid fa-eye'),
       ('IconImportant', 'fa-solid fa-triangle-exclamation'),
       ('IconPin', 'fa-solid fa-thumbtack'),
       ('IconClosed', 'fa-solid fa-lock');


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
  DEFAULT CHARSET = utf8mb4;
