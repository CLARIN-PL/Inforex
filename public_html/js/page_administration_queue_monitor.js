$(function () {
    function setEditingState($action, editing) {
        var $row = $action.closest("tr");
        var $statusView = $row.find(".administration-activity-queue-status-view");
        var $statusSelect = $row.find(".administration-activity-queue-status-select");

        $action.find(".administration-activity-queue-status-edit").toggle(!editing);
        $action.find(".administration-activity-queue-status-save").toggle(editing);
        $action.find(".administration-activity-queue-status-cancel").toggle(editing);
        $statusView.toggle(!editing);
        $statusSelect.toggle(editing);
    }

    $(".administration-activity-queue-status-edit").on("click", function () {
        var $action = $(this).closest(".administration-activity-queue-action");
        var $row = $action.closest("tr");
        var currentStatus = $.trim($row.find(".administration-activity-queue-status-view").text());

        $action.data("original-status", currentStatus);
        $row.find(".administration-activity-queue-status-select").val(currentStatus);
        $action.find(".administration-activity-queue-status-feedback").removeClass("is-success is-error").text("");
        setEditingState($action, true);
    });

    $(".administration-activity-queue-status-cancel").on("click", function () {
        var $action = $(this).closest(".administration-activity-queue-action");
        var originalStatus = $action.data("original-status");
        var $row = $action.closest("tr");

        if (originalStatus) {
            $row.find(".administration-activity-queue-status-select").val(originalStatus);
        }

        $action.find(".administration-activity-queue-status-feedback").removeClass("is-success is-error").text("");
        setEditingState($action, false);
    });

    $(".administration-activity-queue-status-save").on("click", function () {
        var $button = $(this);
        var $action = $button.closest(".administration-activity-queue-action");
        var exportId = parseInt($action.data("export-id"), 10);
        var taskId = parseInt($action.data("task-id"), 10);
        var itemKind = $action.data("item-kind");
        var $row = $action.closest("tr");
        var $select = $row.find(".administration-activity-queue-status-select");
        var status = $select.val();
        var $feedback = $action.find(".administration-activity-queue-status-feedback");
        var ajaxName = itemKind === "task" ? "administration_queue_task_status_update" : "administration_queue_export_status_update";
        var payload = { status: status };

        if ((!exportId && !taskId) || !status) {
            return;
        }

        if (itemKind === "task") {
            payload.task_id = taskId;
        } else {
            payload.export_id = exportId;
        }

        $action.find("button").prop("disabled", true);
        $select.prop("disabled", true);
        $feedback.removeClass("is-success is-error").text("Saving...");

        doAjax(ajaxName, payload, function (response) {
            var exportData = response && response.export ? response.export : null;
            var taskData = response && response.task ? response.task : null;
            var itemData = exportData || taskData;

            $feedback
                .removeClass("is-error")
                .addClass("is-success")
                .text(response && response.message ? response.message : "Updated.");

            if (itemData && itemData.status) {
                $row.find(".administration-activity-queue-status-view").text(itemData.status);
                $action.data("original-status", itemData.status);

                if (itemKind === "export" && typeof itemData.progress !== "undefined") {
                    $row.find("td:nth-child(7)").text(itemData.progress + "%");
                } else if (itemKind === "task") {
                    if (typeof itemData.current_step !== "undefined" && typeof itemData.max_steps !== "undefined" && parseInt(itemData.max_steps, 10) > 0) {
                        $row.find("td:nth-child(7)").text(itemData.current_step + "/" + itemData.max_steps);
                    } else {
                        $row.find("td:nth-child(7)").text("—");
                    }
                }

                if (typeof itemData.datetime_start !== "undefined") {
                    $row.find("td:nth-child(5)").text(itemData.datetime_start ? itemData.datetime_start : "—");
                }
            }

            setEditingState($action, false);

            window.setTimeout(function () {
                window.location.reload();
            }, 350);
        }, function (error) {
            var message = "Update failed.";
            if (error && error.error_msg) {
                message = error.error_msg;
            }
            $feedback
                .removeClass("is-success")
                .addClass("is-error")
                .text(message);
        }, function () {
            $action.find("button").prop("disabled", false);
            $select.prop("disabled", false);
        });
    });
});
