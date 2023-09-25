const getElementsByIdList = (...elements) => {
    const toReturn = [];
    elements.forEach(element => {
        const e = document.getElementById(element);
        if(e === undefined) return;

        toReturn.push(e);
    });

    return toReturn;
}

const setChecked = (...elements) => {
    elements.forEach(element => {
        element.checked = true;
    });
}

const setUnchecked = (...elements) => {
    elements.forEach(element => {
        element.checked = false;
    });
}

const setDisabled = (...elements) => {
    elements.forEach(element => {
        element.disabled = true;
    });
}

const removeDisabled = (...elements) => {
    elements.forEach(element => {
        element.disabled = false;
    })
}

const setCheckedAndDisabled = (...elements) => {
    setChecked(...elements);
    setDisabled(...elements);
}

const setUncheckedAndRemoveDisabled = (...elements) => {
    setUnchecked(...elements);
    removeDisabled(...elements);
}

const checkForumAndTopic = (element) => {
    if (element.checked) {
        user_view_forumCheckbox.checked = true;
        user_view_topicCheckbox.checked = true;
    }
}

const checkAllInputs = (...checkboxes) => {
    checkboxes.forEach(checkbox => {
        checkbox.onchange = () => {
            allPermsIsChecked();
        }
    });
}

const checkAllUserInputs = (...checkboxes) => {
    checkboxes.forEach(checkbox => {
        checkbox.onchange = () => {
            setUser();
        }
    });
}

const checkAllModeratorInputs = (...checkboxes) => {
    checkboxes.forEach(checkbox => {
        checkbox.onchange = () => {
            setModerator();
        }
    });
}

const setEventOnCheckBoxList = (...elements) => {
    elements.forEach(element => {
        element.addEventListener("change", function () {
            checkForumAndTopic(this);
        });
    });

}

const [moderator_all_checkCheckbox,
    user_all_checkCheckbox,
    operatorCheckbox,
    admin_change_topic_nameCheckbox,
    admin_change_topic_tagCheckbox,
    admin_change_topic_prefixCheckbox,
    admin_move_topicCheckbox,
    admin_set_importantCheckbox,
    admin_set_pinCheckbox,
    admin_set_closedCheckbox,
    user_view_forumCheckbox,
    user_react_topicCheckbox,
    user_change_react_topicCheckbox,
    user_remove_react_topicCheckbox,
    user_view_topicCheckbox,
    user_create_topicCheckbox,
    user_create_poolCheckbox,
    user_edit_topicCheckbox,
    user_create_topic_tagCheckbox,
    user_edit_tagCheckbox,
    user_edit_poolCheckbox,
    user_remove_topicCheckbox,
    user_response_topicCheckbox,
    user_response_reactCheckbox,
    user_response_change_reactCheckbox,
    user_response_remove_reactCheckbox]
    = getElementsByIdList(
    'moderator_all_check',
    'user_all_check',
    'operator',
    'admin_change_topic_name',
    'admin_change_topic_tag',
    'admin_change_topic_prefix',
    'admin_move_topic',
    'admin_set_important',
    'admin_set_pin',
    'admin_set_closed',
    'user_view_forum',
    'user_react_topic',
    'user_change_react_topic',
    'user_remove_react_topic',
    'user_view_topic',
    'user_create_topic',
    'user_create_pool',
    'user_edit_topic',
    'user_create_topic_tag',
    'user_edit_tag',
    'user_edit_pool',
    'user_remove_topic',
    'user_response_topic',
    'user_response_react',
    'user_response_change_react',
    'user_response_remove_react'
);


// ----

checkAllInputs(moderator_all_checkCheckbox,
    user_all_checkCheckbox,
    operatorCheckbox,
    admin_change_topic_nameCheckbox,
    admin_change_topic_tagCheckbox,
    admin_change_topic_prefixCheckbox,
    admin_move_topicCheckbox,
    admin_set_importantCheckbox,
    admin_set_pinCheckbox,
    admin_set_closedCheckbox,
    user_view_forumCheckbox,
    user_react_topicCheckbox,
    user_change_react_topicCheckbox,
    user_remove_react_topicCheckbox,
    user_view_topicCheckbox,
    user_create_topicCheckbox,
    user_create_poolCheckbox,
    user_edit_topicCheckbox,
    user_create_topic_tagCheckbox,
    user_edit_tagCheckbox,
    user_edit_poolCheckbox,
    user_remove_topicCheckbox,
    user_response_topicCheckbox,
    user_response_reactCheckbox,
    user_response_change_reactCheckbox,
    user_response_remove_reactCheckbox)

checkAllUserInputs(
    user_view_forumCheckbox,
    user_react_topicCheckbox,
    user_change_react_topicCheckbox,
    user_remove_react_topicCheckbox,
    user_view_topicCheckbox,
    user_create_topicCheckbox,
    user_create_poolCheckbox,
    user_edit_topicCheckbox,
    user_create_topic_tagCheckbox,
    user_edit_tagCheckbox,
    user_edit_poolCheckbox,
    user_remove_topicCheckbox,
    user_response_topicCheckbox,
    user_response_reactCheckbox,
    user_response_change_reactCheckbox,
    user_response_remove_reactCheckbox)

checkAllModeratorInputs(
    admin_change_topic_nameCheckbox,
    admin_change_topic_tagCheckbox,
    admin_change_topic_prefixCheckbox,
    admin_move_topicCheckbox,
    admin_set_importantCheckbox,
    admin_set_pinCheckbox,
    admin_set_closedCheckbox)

setEventOnCheckBoxList(
    admin_change_topic_nameCheckbox,
    admin_change_topic_tagCheckbox,
    admin_change_topic_prefixCheckbox,
    admin_move_topicCheckbox,
    admin_set_importantCheckbox,
    admin_set_pinCheckbox,
    admin_set_closedCheckbox,
    user_remove_topicCheckbox,
    user_response_topicCheckbox
)

user_view_forumCheckbox.addEventListener("change", function () {
    if (!this.checked) {
        setUnchecked(
            moderator_all_checkCheckbox,
            admin_change_topic_nameCheckbox,
            admin_change_topic_tagCheckbox,
            admin_change_topic_prefixCheckbox,
            admin_move_topicCheckbox,
            admin_set_importantCheckbox,
            admin_set_pinCheckbox,
            admin_set_closedCheckbox,
            user_react_topicCheckbox,
            user_change_react_topicCheckbox,
            user_remove_react_topicCheckbox,
            user_view_topicCheckbox,
            user_create_topicCheckbox,
            user_create_poolCheckbox,
            user_edit_topicCheckbox,
            user_create_topic_tagCheckbox,
            user_edit_tagCheckbox,
            user_edit_poolCheckbox,
            user_remove_topicCheckbox,
            user_response_topicCheckbox,
            user_response_reactCheckbox,
            user_response_change_reactCheckbox,
            user_response_remove_reactCheckbox
        )
    }
});

user_react_topicCheckbox.addEventListener("change", function () {
    this.checked
        ? setChecked(user_view_forumCheckbox, user_view_topicCheckbox)
        : setUnchecked(user_change_react_topicCheckbox, user_remove_react_topicCheckbox)
});

user_change_react_topicCheckbox.addEventListener("change", function () {
    if (this.checked) {
        setChecked(user_react_topicCheckbox, user_view_forumCheckbox, user_view_topicCheckbox)
    }
});

user_remove_react_topicCheckbox.addEventListener("change", function () {
    if (this.checked) {
        setChecked(user_react_topicCheckbox, user_view_forumCheckbox, user_view_topicCheckbox)
    }
});

user_view_topicCheckbox.addEventListener("change", function () {
    this.checked
        ? setChecked(user_view_forumCheckbox)
        : setUnchecked(
            moderator_all_checkCheckbox,
            admin_change_topic_nameCheckbox,
            admin_change_topic_tagCheckbox,
            admin_change_topic_prefixCheckbox,
            admin_move_topicCheckbox,
            admin_set_importantCheckbox,
            admin_set_pinCheckbox,
            admin_set_closedCheckbox,
            user_react_topicCheckbox,
            user_react_topicCheckbox,
            user_change_react_topicCheckbox,
            user_remove_react_topicCheckbox,
            user_view_topicCheckbox,
            user_create_topicCheckbox,
            user_create_poolCheckbox,
            user_edit_topicCheckbox,
            user_create_topic_tagCheckbox,
            user_edit_tagCheckbox,
            user_edit_poolCheckbox,
            user_remove_topicCheckbox,
            user_response_topicCheckbox,
            user_response_reactCheckbox,
            user_response_change_reactCheckbox,
            user_response_remove_reactCheckbox
        );
});

user_create_topicCheckbox.addEventListener("change", function () {
    this.checked
        ? setChecked(user_view_forumCheckbox, user_view_topicCheckbox)
        : setUnchecked(user_create_topic_tagCheckbox, user_create_poolCheckbox)
});

user_create_poolCheckbox.addEventListener("change", function () {
    if (this.checked) {
        setChecked(user_view_forumCheckbox, user_view_topicCheckbox, user_create_topicCheckbox)
    }
});

user_edit_topicCheckbox.addEventListener("change", function () {
    this.checked
        ? setChecked(user_view_forumCheckbox, user_view_topicCheckbox)
        : setUnchecked(user_edit_tagCheckbox, user_edit_poolCheckbox)
});

user_create_topic_tagCheckbox.addEventListener("change", function () {
    if (this.checked) {
        setChecked(user_view_forumCheckbox, user_view_topicCheckbox, user_create_topicCheckbox)
    }
});

user_edit_tagCheckbox.addEventListener("change", function () {
    if (this.checked) {
        setChecked(user_view_forumCheckbox, user_view_topicCheckbox, user_edit_topicCheckbox)
    }
});

user_edit_poolCheckbox.addEventListener("change", function () {
    if (this.checked) {
        setChecked(user_view_forumCheckbox, user_view_topicCheckbox, user_edit_topicCheckbox)
    }
});

user_response_reactCheckbox.addEventListener("change", function () {
    checkForumAndTopic(this);

    if(!this.checked) {
        setUnchecked(user_response_change_reactCheckbox, user_response_remove_reactCheckbox)
    }
});

user_response_change_reactCheckbox.addEventListener("change", function () {
    if (this.checked) {
        setChecked(user_view_forumCheckbox, user_view_topicCheckbox, user_response_reactCheckbox)
    }
});

user_response_remove_reactCheckbox.addEventListener("change", function () {
    if (this.checked) {
        setChecked(user_view_forumCheckbox, user_view_topicCheckbox, user_response_reactCheckbox)
    }
});

moderator_all_checkCheckbox.addEventListener("change", function () {
    if (this.checked) {
        setChecked(admin_change_topic_nameCheckbox, admin_change_topic_tagCheckbox, admin_change_topic_prefixCheckbox, admin_move_topicCheckbox, admin_set_importantCheckbox, admin_set_pinCheckbox, admin_set_closedCheckbox, user_view_forumCheckbox, user_view_topicCheckbox)
    } else {
        setUnchecked(admin_change_topic_nameCheckbox, admin_change_topic_tagCheckbox, admin_change_topic_prefixCheckbox, admin_move_topicCheckbox, admin_set_importantCheckbox, admin_set_pinCheckbox, admin_set_closedCheckbox)
    }
});

user_all_checkCheckbox.addEventListener("change", function () {
    if (this.checked) {
        setChecked(user_view_forumCheckbox, user_react_topicCheckbox, user_change_react_topicCheckbox, user_remove_react_topicCheckbox, user_view_topicCheckbox, user_create_topicCheckbox, user_create_poolCheckbox, user_edit_topicCheckbox, user_create_topic_tagCheckbox, user_edit_tagCheckbox, user_edit_poolCheckbox, user_remove_topicCheckbox, user_response_topicCheckbox, user_response_reactCheckbox, user_response_change_reactCheckbox, user_response_remove_reactCheckbox)
    } else
        setUnchecked(user_view_forumCheckbox, user_react_topicCheckbox, user_change_react_topicCheckbox, user_remove_react_topicCheckbox, user_view_topicCheckbox, user_create_topicCheckbox, user_create_poolCheckbox, user_edit_topicCheckbox, user_create_topic_tagCheckbox, user_edit_tagCheckbox, user_edit_poolCheckbox, user_remove_topicCheckbox, user_response_topicCheckbox, user_response_reactCheckbox, user_response_change_reactCheckbox, user_response_remove_reactCheckbox)
});

operatorCheckbox.addEventListener('change', function() {
        operatorCheckbox.checked
            ? setCheckedAndDisabled(
                moderator_all_checkCheckbox,
                user_all_checkCheckbox,
                admin_change_topic_nameCheckbox,
                admin_change_topic_tagCheckbox,
                admin_change_topic_prefixCheckbox,
                admin_move_topicCheckbox,
                admin_set_importantCheckbox,
                admin_set_pinCheckbox,
                admin_set_closedCheckbox,
                user_view_forumCheckbox,
                user_react_topicCheckbox,
                user_change_react_topicCheckbox,
                user_remove_react_topicCheckbox,
                user_view_topicCheckbox,
                user_create_topicCheckbox,
                user_create_poolCheckbox,
                user_edit_topicCheckbox,
                user_create_topic_tagCheckbox,
                user_edit_tagCheckbox,
                user_edit_poolCheckbox,
                user_remove_topicCheckbox,
                user_response_topicCheckbox,
                user_response_reactCheckbox,
                user_response_change_reactCheckbox,
                user_response_remove_reactCheckbox
            )
            : setUncheckedAndRemoveDisabled(
                moderator_all_checkCheckbox,
                user_all_checkCheckbox,
                admin_change_topic_nameCheckbox,
                admin_change_topic_tagCheckbox,
                admin_change_topic_prefixCheckbox,
                admin_move_topicCheckbox,
                admin_set_importantCheckbox,
                admin_set_pinCheckbox,
                admin_set_closedCheckbox,
                user_view_forumCheckbox,
                user_react_topicCheckbox,
                user_change_react_topicCheckbox,
                user_remove_react_topicCheckbox,
                user_view_topicCheckbox,
                user_create_topicCheckbox,
                user_create_poolCheckbox,
                user_edit_topicCheckbox,
                user_create_topic_tagCheckbox,
                user_edit_tagCheckbox,
                user_edit_poolCheckbox,
                user_remove_topicCheckbox,
                user_response_topicCheckbox,
                user_response_reactCheckbox,
                user_response_change_reactCheckbox,
                user_response_remove_reactCheckbox
            )
    }
);

function operatorCheckboxes() {
    if (operatorCheckbox.checked) {
        setCheckedAndDisabled(
            moderator_all_checkCheckbox,
            user_all_checkCheckbox,
            admin_change_topic_nameCheckbox,
            admin_change_topic_tagCheckbox,
            admin_change_topic_prefixCheckbox,
            admin_move_topicCheckbox,
            admin_set_importantCheckbox,
            admin_set_pinCheckbox,
            admin_set_closedCheckbox,
            user_view_forumCheckbox,
            user_react_topicCheckbox,
            user_change_react_topicCheckbox,
            user_remove_react_topicCheckbox,
            user_view_topicCheckbox,
            user_create_topicCheckbox,
            user_create_poolCheckbox,
            user_edit_topicCheckbox,
            user_create_topic_tagCheckbox,
            user_edit_tagCheckbox,
            user_edit_poolCheckbox,
            user_remove_topicCheckbox,
            user_response_topicCheckbox,
            user_response_reactCheckbox,
            user_response_change_reactCheckbox,
            user_response_remove_reactCheckbox
        )
    }
}

function allPermsIsChecked() {
    //Besoin de Badiiix pour fix ça (checker tout manuellement ne check pas "Toutes les perms"):
    if (moderator_all_checkCheckbox.checked && user_all_checkCheckbox.checked && !user_all_checkCheckbox.disabled) {
        operatorCheckbox.checked = true;
        operatorCheckboxes()
    } else if (admin_change_topic_nameCheckbox.checked && !admin_change_topic_nameCheckbox.disabled && admin_change_topic_tagCheckbox.checked && admin_change_topic_prefixCheckbox.checked && admin_move_topicCheckbox.checked && admin_set_importantCheckbox.checked && admin_set_pinCheckbox.checked && admin_set_closedCheckbox.checked && user_view_forumCheckbox.checked && user_react_topicCheckbox.checked && user_change_react_topicCheckbox.checked && user_remove_react_topicCheckbox.checked && user_view_topicCheckbox.checked && user_create_topicCheckbox.checked && user_create_poolCheckbox.checked && user_edit_topicCheckbox.checked && user_create_topic_tagCheckbox.checked && user_edit_tagCheckbox.checked && user_edit_poolCheckbox.checked && user_remove_topicCheckbox.checked && user_response_topicCheckbox.checked && user_response_reactCheckbox.checked && user_response_change_reactCheckbox.checked && user_response_remove_reactCheckbox.checked) {
        operatorCheckbox.checked = true;
        operatorCheckboxes()
    }
}

function setUser() {
    if (user_view_forumCheckbox.checked && user_react_topicCheckbox.checked && user_change_react_topicCheckbox.checked && user_remove_react_topicCheckbox.checked && user_view_topicCheckbox.checked && user_create_topicCheckbox.checked && user_create_poolCheckbox.checked && user_edit_topicCheckbox.checked && user_create_topic_tagCheckbox.checked && user_edit_tagCheckbox.checked && user_edit_poolCheckbox.checked && user_remove_topicCheckbox.checked && user_response_topicCheckbox.checked && user_response_reactCheckbox.checked && user_response_change_reactCheckbox.checked && user_response_remove_reactCheckbox.checked) {
        setChecked(user_all_checkCheckbox)
    } else {
        setUnchecked(user_all_checkCheckbox)
    }
}

function setModerator() {
    if (admin_change_topic_nameCheckbox.checked && admin_change_topic_tagCheckbox.checked && admin_change_topic_prefixCheckbox.checked && admin_move_topicCheckbox.checked && admin_set_importantCheckbox.checked && admin_set_pinCheckbox.checked && admin_set_closedCheckbox.checked) {
        setChecked(moderator_all_checkCheckbox)
    } else {
        setUnchecked(moderator_all_checkCheckbox)
    }
}

operatorCheckboxes();