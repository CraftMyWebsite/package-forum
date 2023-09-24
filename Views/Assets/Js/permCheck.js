let operatorCheckbox = document.getElementById('operator');
let admin_change_topic_nameCheckbox = document.getElementById('admin_change_topic_name');
let admin_change_topic_tagCheckbox = document.getElementById('admin_change_topic_tag');
let admin_change_topic_prefixCheckbox = document.getElementById('admin_change_topic_prefix');
let admin_move_topicCheckbox = document.getElementById('admin_move_topic');
let admin_set_importantCheckbox = document.getElementById('admin_set_important');
let admin_set_pinCheckbox = document.getElementById('admin_set_pin');
let admin_set_closedCheckbox = document.getElementById('admin_set_closed');
let user_view_forumCheckbox = document.getElementById('user_view_forum');
let user_react_topicCheckbox = document.getElementById('user_react_topic');
let user_change_react_topicCheckbox = document.getElementById('user_change_react_topic');
let user_remove_react_topicCheckbox = document.getElementById('user_remove_react_topic');
let user_view_topicCheckbox = document.getElementById('user_view_topic');
let user_create_topicCheckbox = document.getElementById('user_create_topic');
let user_create_poolCheckbox = document.getElementById('user_create_pool');
let user_edit_topicCheckbox = document.getElementById('user_edit_topic');
let user_create_topic_tagCheckbox = document.getElementById('user_create_topic_tag');
let user_edit_tagCheckbox = document.getElementById('user_edit_tag');
let user_edit_poolCheckbox = document.getElementById('user_edit_pool');
let user_remove_topicCheckbox = document.getElementById('user_remove_topic');
let user_response_topicCheckbox = document.getElementById('user_response_topic');
let user_response_reactCheckbox = document.getElementById('user_response_react');
let user_response_change_reactCheckbox = document.getElementById('user_response_change_react');
let user_response_remove_reactCheckbox = document.getElementById('user_response_remove_react');

admin_change_topic_nameCheckbox.addEventListener("change", function () {
    if (this.checked) {
        user_view_forumCheckbox.checked = true;
        user_view_topicCheckbox.checked = true;
    }
});

admin_change_topic_tagCheckbox.addEventListener("change", function () {
    if (this.checked) {
        user_view_forumCheckbox.checked = true;
        user_view_topicCheckbox.checked = true;
    }
});

admin_change_topic_prefixCheckbox.addEventListener("change", function () {
    if (this.checked) {
        user_view_forumCheckbox.checked = true;
        user_view_topicCheckbox.checked = true;
    }
});

admin_move_topicCheckbox.addEventListener("change", function () {
    if (this.checked) {
        user_view_forumCheckbox.checked = true;
        user_view_topicCheckbox.checked = true;
    }
});

admin_set_importantCheckbox.addEventListener("change", function () {
    if (this.checked) {
        user_view_forumCheckbox.checked = true;
        user_view_topicCheckbox.checked = true;
    }
});

admin_set_pinCheckbox.addEventListener("change", function () {
    if (this.checked) {
        user_view_forumCheckbox.checked = true;
        user_view_topicCheckbox.checked = true;
    }
});

admin_set_closedCheckbox.addEventListener("change", function () {
    if (this.checked) {
        user_view_forumCheckbox.checked = true;
        user_view_topicCheckbox.checked = true;
    }
});
user_view_forumCheckbox.addEventListener("change", function () {
    if (!this.checked) {
        admin_change_topic_nameCheckbox.checked = false;
        admin_change_topic_tagCheckbox.checked = false;
        admin_change_topic_prefixCheckbox.checked = false;
        admin_move_topicCheckbox.checked = false;
        admin_set_importantCheckbox.checked = false;
        admin_set_pinCheckbox.checked = false;
        admin_set_closedCheckbox.checked = false;
        user_react_topicCheckbox.checked = false;
        user_change_react_topicCheckbox.checked = false;
        user_remove_react_topicCheckbox.checked = false;
        user_view_topicCheckbox.checked = false;
        user_create_topicCheckbox.checked = false;
        user_create_poolCheckbox.checked = false;
        user_edit_topicCheckbox.checked = false;
        user_create_topic_tagCheckbox.checked = false;
        user_edit_tagCheckbox.checked = false;
        user_edit_poolCheckbox.checked = false;
        user_remove_topicCheckbox.checked = false;
        user_response_topicCheckbox.checked = false;
        user_response_reactCheckbox.checked = false;
        user_response_change_reactCheckbox.checked = false;
        user_response_remove_reactCheckbox.checked = false;
    }
});

user_react_topicCheckbox.addEventListener("change", function () {
    if (this.checked) {
        user_view_forumCheckbox.checked = true;
        user_view_topicCheckbox.checked = true;
    } else {
        user_change_react_topicCheckbox.checked = false;
        user_remove_react_topicCheckbox.checked = false;
    }
});

user_change_react_topicCheckbox.addEventListener("change", function () {
    if (this.checked) {
        user_react_topicCheckbox.checked = true;
        user_view_forumCheckbox.checked = true;
        user_view_topicCheckbox.checked = true;
    }
});

user_remove_react_topicCheckbox.addEventListener("change", function () {
    if (this.checked) {
        user_react_topicCheckbox.checked = true;
        user_view_forumCheckbox.checked = true;
        user_view_topicCheckbox.checked = true;
    }
});

user_view_topicCheckbox.addEventListener("change", function () {
    if (this.checked) {
        user_view_forumCheckbox.checked = true;
    } else {
        admin_change_topic_nameCheckbox.checked = false;
        admin_change_topic_tagCheckbox.checked = false;
        admin_change_topic_prefixCheckbox.checked = false;
        admin_move_topicCheckbox.checked = false;
        admin_set_importantCheckbox.checked = false;
        admin_set_pinCheckbox.checked = false;
        admin_set_closedCheckbox.checked = false;
        user_react_topicCheckbox.checked = false;
        user_react_topicCheckbox.checked = false;
        user_change_react_topicCheckbox.checked = false;
        user_remove_react_topicCheckbox.checked = false;
        user_view_topicCheckbox.checked = false;
        user_create_topicCheckbox.checked = false;
        user_create_poolCheckbox.checked = false;
        user_edit_topicCheckbox.checked = false;
        user_create_topic_tagCheckbox.checked = false;
        user_edit_tagCheckbox.checked = false;
        user_edit_poolCheckbox.checked = false;
        user_remove_topicCheckbox.checked = false;
        user_response_topicCheckbox.checked = false;
        user_response_reactCheckbox.checked = false;
        user_response_change_reactCheckbox.checked = false;
        user_response_remove_reactCheckbox.checked = false;
    }
});

user_create_topicCheckbox.addEventListener("change", function () {
    if (this.checked) {
        user_view_forumCheckbox.checked = true;
        user_view_topicCheckbox.checked = true;
    } else {
        user_create_topic_tagCheckbox.checked = false;
        user_create_poolCheckbox.checked = false;
    }
});

user_create_poolCheckbox.addEventListener("change", function () {
    if (this.checked) {
        user_view_forumCheckbox.checked = true;
        user_view_topicCheckbox.checked = true;
        user_create_topicCheckbox.checked = true;
    }
});

user_edit_topicCheckbox.addEventListener("change", function () {
    if (this.checked) {
        user_view_forumCheckbox.checked = true;
        user_view_topicCheckbox.checked = true;
    } else {
        user_edit_tagCheckbox.checked = false;
        user_edit_poolCheckbox.checked = false;
    }
});

user_create_topic_tagCheckbox.addEventListener("change", function () {
    if (this.checked) {
        user_view_forumCheckbox.checked = true;
        user_view_topicCheckbox.checked = true;
        user_create_topicCheckbox.checked = true;
    }
});

user_edit_tagCheckbox.addEventListener("change", function () {
    if (this.checked) {
        user_view_forumCheckbox.checked = true;
        user_view_topicCheckbox.checked = true;
        user_edit_topicCheckbox.checked = true;
    }
});

user_edit_poolCheckbox.addEventListener("change", function () {
    if (this.checked) {
        user_view_forumCheckbox.checked = true;
        user_view_topicCheckbox.checked = true;
        user_edit_topicCheckbox.checked = true;
    }
});

user_remove_topicCheckbox.addEventListener("change", function () {
    if (this.checked) {
        user_view_forumCheckbox.checked = true;
        user_view_topicCheckbox.checked = true;
    }
});

user_response_topicCheckbox.addEventListener("change", function () {
    if (this.checked) {
        user_view_forumCheckbox.checked = true;
        user_view_topicCheckbox.checked = true;
    }
});

user_response_reactCheckbox.addEventListener("change", function () {
    if (this.checked) {
        user_view_forumCheckbox.checked = true;
        user_view_topicCheckbox.checked = true;
    } else {
        user_response_change_reactCheckbox.checked = false;
        user_response_remove_reactCheckbox.checked = false;
    }
});

user_response_change_reactCheckbox.addEventListener("change", function () {
    if (this.checked) {
        user_view_forumCheckbox.checked = true;
        user_view_topicCheckbox.checked = true;
        user_response_reactCheckbox.checked = true;
    }
});

user_response_remove_reactCheckbox.addEventListener("change", function () {
    if (this.checked) {
        user_view_forumCheckbox.checked = true;
        user_view_topicCheckbox.checked = true;
        user_response_reactCheckbox.checked = true;
    }
});

operatorCheckbox.addEventListener('change', function() {
    if (operatorCheckbox.checked) {
        admin_change_topic_nameCheckbox.disabled = true;
        admin_change_topic_nameCheckbox.checked = true;
        admin_change_topic_tagCheckbox.disabled = true;
        admin_change_topic_tagCheckbox.checked = true;
        admin_change_topic_prefixCheckbox.disabled = true;
        admin_change_topic_prefixCheckbox.checked = true;
        admin_move_topicCheckbox.disabled = true;
        admin_move_topicCheckbox.checked = true;
        admin_set_importantCheckbox.disabled = true;
        admin_set_importantCheckbox.checked = true;
        admin_set_pinCheckbox.disabled = true;
        admin_set_pinCheckbox.checked = true;
        admin_set_closedCheckbox.disabled = true;
        admin_set_closedCheckbox.checked = true;
        user_view_forumCheckbox.disabled = true;
        user_view_forumCheckbox.checked = true;
        user_react_topicCheckbox.disabled = true;
        user_react_topicCheckbox.checked = true;
        user_change_react_topicCheckbox.disabled = true;
        user_change_react_topicCheckbox.checked = true;
        user_remove_react_topicCheckbox.disabled = true;
        user_remove_react_topicCheckbox.checked = true;
        user_view_topicCheckbox.disabled = true;
        user_view_topicCheckbox.checked = true;
        user_create_topicCheckbox.disabled = true;
        user_create_topicCheckbox.checked = true;
        user_create_poolCheckbox.disabled = true;
        user_create_poolCheckbox.checked = true;
        user_edit_topicCheckbox.disabled = true;
        user_edit_topicCheckbox.checked = true;
        user_create_topic_tagCheckbox.disabled = true;
        user_create_topic_tagCheckbox.checked = true;
        user_edit_tagCheckbox.disabled = true;
        user_edit_tagCheckbox.checked = true;
        user_edit_poolCheckbox.disabled = true;
        user_edit_poolCheckbox.checked = true;
        user_remove_topicCheckbox.disabled = true;
        user_remove_topicCheckbox.checked = true;
        user_response_topicCheckbox.disabled = true;
        user_response_topicCheckbox.checked = true;
        user_response_reactCheckbox.disabled = true;
        user_response_reactCheckbox.checked = true;
        user_response_change_reactCheckbox.disabled = true;
        user_response_change_reactCheckbox.checked = true;
        user_response_remove_reactCheckbox.disabled = true;
        user_response_remove_reactCheckbox.checked = true;
    } else {
        admin_change_topic_nameCheckbox.disabled = false;
        admin_change_topic_nameCheckbox.checked = false;
        admin_change_topic_tagCheckbox.disabled = false;
        admin_change_topic_tagCheckbox.checked = false;
        admin_change_topic_prefixCheckbox.disabled = false;
        admin_change_topic_prefixCheckbox.checked = false;
        admin_move_topicCheckbox.disabled = false;
        admin_move_topicCheckbox.checked = false;
        admin_set_importantCheckbox.disabled = false;
        admin_set_importantCheckbox.checked = false;
        admin_set_pinCheckbox.disabled = false;
        admin_set_pinCheckbox.checked = false;
        admin_set_closedCheckbox.disabled = false;
        admin_set_closedCheckbox.checked = false;
        user_view_forumCheckbox.disabled = false;
        user_view_forumCheckbox.checked = false;
        user_react_topicCheckbox.disabled = false;
        user_react_topicCheckbox.checked = false;
        user_change_react_topicCheckbox.disabled = false;
        user_change_react_topicCheckbox.checked = false;
        user_remove_react_topicCheckbox.disabled = false;
        user_remove_react_topicCheckbox.checked = false;
        user_view_topicCheckbox.disabled = false;
        user_view_topicCheckbox.checked = false;
        user_create_topicCheckbox.disabled = false;
        user_create_topicCheckbox.checked = false;
        user_create_poolCheckbox.disabled = false;
        user_create_poolCheckbox.checked = false;
        user_edit_topicCheckbox.disabled = false;
        user_edit_topicCheckbox.checked = false;
        user_create_topic_tagCheckbox.disabled = false;
        user_create_topic_tagCheckbox.checked = false;
        user_edit_tagCheckbox.disabled = false;
        user_edit_tagCheckbox.checked = false;
        user_edit_poolCheckbox.disabled = false;
        user_edit_poolCheckbox.checked = false;
        user_remove_topicCheckbox.disabled = false;
        user_remove_topicCheckbox.checked = false;
        user_response_topicCheckbox.disabled = false;
        user_response_topicCheckbox.checked = false;
        user_response_reactCheckbox.disabled = false;
        user_response_reactCheckbox.checked = false;
        user_response_change_reactCheckbox.disabled = false;
        user_response_change_reactCheckbox.checked = false;
        user_response_remove_reactCheckbox.disabled = false;
        user_response_remove_reactCheckbox.checked = false;
    }
});

function operatorCheckboxes() {
    if (operatorCheckbox.checked) {
        admin_change_topic_nameCheckbox.disabled = true;
        admin_change_topic_nameCheckbox.checked = true;
        admin_change_topic_tagCheckbox.disabled = true;
        admin_change_topic_tagCheckbox.checked = true;
        admin_change_topic_prefixCheckbox.disabled = true;
        admin_change_topic_prefixCheckbox.checked = true;
        admin_move_topicCheckbox.disabled = true;
        admin_move_topicCheckbox.checked = true;
        admin_set_importantCheckbox.disabled = true;
        admin_set_importantCheckbox.checked = true;
        admin_set_pinCheckbox.disabled = true;
        admin_set_pinCheckbox.checked = true;
        admin_set_closedCheckbox.disabled = true;
        admin_set_closedCheckbox.checked = true;
        user_view_forumCheckbox.disabled = true;
        user_view_forumCheckbox.checked = true;
        user_react_topicCheckbox.disabled = true;
        user_react_topicCheckbox.checked = true;
        user_change_react_topicCheckbox.disabled = true;
        user_change_react_topicCheckbox.checked = true;
        user_remove_react_topicCheckbox.disabled = true;
        user_remove_react_topicCheckbox.checked = true;
        user_view_topicCheckbox.disabled = true;
        user_view_topicCheckbox.checked = true;
        user_create_topicCheckbox.disabled = true;
        user_create_topicCheckbox.checked = true;
        user_create_poolCheckbox.disabled = true;
        user_create_poolCheckbox.checked = true;
        user_edit_topicCheckbox.disabled = true;
        user_edit_topicCheckbox.checked = true;
        user_create_topic_tagCheckbox.disabled = true;
        user_create_topic_tagCheckbox.checked = true;
        user_edit_tagCheckbox.disabled = true;
        user_edit_tagCheckbox.checked = true;
        user_edit_poolCheckbox.disabled = true;
        user_edit_poolCheckbox.checked = true;
        user_remove_topicCheckbox.disabled = true;
        user_remove_topicCheckbox.checked = true;
        user_response_topicCheckbox.disabled = true;
        user_response_topicCheckbox.checked = true;
        user_response_reactCheckbox.disabled = true;
        user_response_reactCheckbox.checked = true;
        user_response_change_reactCheckbox.disabled = true;
        user_response_change_reactCheckbox.checked = true;
        user_response_remove_reactCheckbox.disabled = true;
        user_response_remove_reactCheckbox.checked = true;
    }
}

operatorCheckboxes();