SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS
    cmw_forums_discord,
    cmw_forums_response_feedback,
    cmw_forums_topics_feedback,
    cmw_forums_topics_tags,
    cmw_forums_topics_views,
    cmw_forums_topic_reported,
    cmw_forums_response_reported,
    cmw_forums_followed,
    cmw_forums_roles_permissions,
    cmw_forums_users_roles,
    cmw_forums_categories_groups_allowed,
    cmw_forums_groups_allowed,
    cmw_forums_users_blocked,
    cmw_forums_response,
    cmw_forums_topics,
    cmw_forums_feedback,
    cmw_forums_permissions,
    cmw_forums_roles,
    cmw_forums_prefixes,
    cmw_forums,
    cmw_forums_categories,
    cmw_forums_settings;

SET FOREIGN_KEY_CHECKS = 1;