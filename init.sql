CREATE TABLE IF NOT EXISTS cmw_forums_categories
(
    forum_category_id          INT AUTO_INCREMENT PRIMARY KEY,
    forum_category_name        VARCHAR(50) NOT NULL,
    forum_category_description TEXT        NULL
);

CREATE TABLE IF NOT EXISTS cmw_forums
(
    forum_id          INT AUTO_INCREMENT PRIMARY KEY,
    forum_name        VARCHAR(50)  NOT NULL,
    forum_slug        VARCHAR(255) NOT NULL,
    forum_description TEXT         NULL,
    forum_subforum_id INT          NULL,
    forum_category_id INT          NULL,
    CONSTRAINT fk_forum_category_id
        FOREIGN KEY (forum_category_id) REFERENCES cmw_forums_categories (forum_category_id),
    CONSTRAINT fk_forum_forum_id
        FOREIGN KEY (forum_subforum_id) REFERENCES cmw_forums (forum_id)
);

CREATE TABLE IF NOT EXISTS cmw_forums_topics
(
    forum_topic_id      INT AUTO_INCREMENT PRIMARY KEY,
    forum_topic_name    TEXT         NOT NULL,
    forum_topic_slug    VARCHAR(255) NOT NULL,
    forum_topic_content MEDIUMTEXT   NULL,
    forum_topic_pinned  TINYINT(1)   NOT NULL DEFAULT 0,
    user_id             INT          NULL,
    forum_id            INT          NOT NULL,
    CONSTRAINT fk_forum_id
        FOREIGN KEY (forum_id) REFERENCES cmw_forums (forum_id),
    CONSTRAINT fk_topics_user_id
        FOREIGN KEY (user_id) REFERENCES cmw_users (user_id)
);

CREATE TABLE IF NOT EXISTS cmw_forums_response
(
    forum_response_id      INT AUTO_INCREMENT PRIMARY KEY,
    forum_response_content TEXT NOT NULL,
    forum_topic_id         INT  NOT NULL,
    user_id                INT  NOT NULL,
    CONSTRAINT fk_response_forum_topic_id
        FOREIGN KEY (forum_topic_id) REFERENCES cmw_forums_topics (forum_topic_id),
    CONSTRAINT fk_response_user_id
        FOREIGN KEY (user_id) REFERENCES cmw_users (user_id)
);

