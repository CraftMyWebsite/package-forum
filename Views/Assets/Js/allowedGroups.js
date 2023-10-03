const allowedGroupsToggleCheckbox = document.getElementById("allowedGroupsToggle");
const allowedGroups = document.getElementById("listAllowedGroups");

allowedGroupsToggleCheckbox.addEventListener("change", function () {
    if (allowedGroupsToggleCheckbox.checked) {
        allowedGroups.style.display = "block";
    } else {
        allowedGroups.style.display = "none";
    }
});
if (allowedGroupsToggleCheckbox.checked) {
    allowedGroups.style.display = "block";
} else {
    allowedGroups.style.display = "none";
}
