create table if not exists cmw_forums_categories
(
    forum_category_id          int auto_increment
        primary key,
    forum_category_name        varchar(50) not null,
    forum_category_description text        null
);

create table if not exists cmw_forums
(
    forum_id          int auto_increment
        primary key,
    forum_name        varchar(50) not null,
    forum_description text        null,
    forum_subforum_id int         null,
    forum_category_id int         null,
    constraint FK_FORUM_CATEGORY_ID
        foreign key (forum_category_id) references cmw_forums_categories (forum_category_id),
    constraint FK_FORUM_FORUM_ID
        foreign key (forum_subforum_id) references cmw_forums (forum_id)
);

create table if not exists cmw_forums_topics
(
    forum_topic_id      int auto_increment
        primary key,
    forum_topic_name    text        not null,
    forum_topic_content varchar(50) null,
    user_id             int         null,
    forum_id            int         not null,
    constraint FK_FORUM_ID
        foreign key (forum_id) references cmw_forums (forum_id),
    constraint FK_TOPICS_USER_ID
        foreign key (user_id) references cmw_users (user_id)
);

create table if not exists cmw_forums_response
(
    forum_response_id      int auto_increment
        primary key,
    forum_response_content text not null,
    forum_topic_id         int  not null,
    user_id                int  not null,
    constraint FK_RESPONSE_FORUM_TOPIC_ID
        foreign key (forum_topic_id) references cmw_forums_topics (forum_topic_id),
    constraint FK_RESPONSE_USER_ID
        foreign key (user_id) references cmw_users (user_id)
);

