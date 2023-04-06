CREATE TABLE IF NOT EXISTS cmw_forums_categories
(
    forum_category_id          INT AUTO_INCREMENT PRIMARY KEY,
    forum_category_name        VARCHAR(50) NOT NULL,
    forum_category_icon        VARCHAR(50) NULL,
    forum_category_description TEXT        NULL,
    forum_category_created TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    forum_category_updated TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS cmw_forums
(
    forum_id          INT AUTO_INCREMENT PRIMARY KEY,
    forum_name        VARCHAR(50)  NOT NULL,
    forum_icon        VARCHAR(50) NULL,
    forum_slug        VARCHAR(255) NOT NULL,
    forum_description TEXT         NULL,
    forum_subforum_id INT          NULL,
    forum_category_id INT          NULL,
    forum_created TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    forum_updated TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
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
    forum_topic_pinned           TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    forum_topic_disallow_replies TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    forum_topic_important        TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    user_id                      INT                 NULL,
    forum_id                     INT                 NOT NULL,
    forum_topic_created TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    forum_topic_updated TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_forum_id
        FOREIGN KEY (forum_id) REFERENCES cmw_forums (forum_id) ON UPDATE CASCADE ON DELETE CASCADE,
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
    forum_response_id      INT AUTO_INCREMENT PRIMARY KEY,
    forum_response_content TEXT NOT NULL,
    forum_topic_id         INT  NOT NULL,
    forum_response_created TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    forum_response_updated TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    user_id                INT  NOT NULL,
    CONSTRAINT fk_response_forum_topic_id
        FOREIGN KEY (forum_topic_id) REFERENCES cmw_forums_topics (forum_topic_id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_response_user_id
        FOREIGN KEY (user_id) REFERENCES cmw_users (user_id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;
