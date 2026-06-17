$(function () {
    $(".administration-activity-queue-status-apply").on("click", function () {
        var $button = $(this);
        var $action = $button.closest(".administration-activity-queue-action");
        var exportId = parseInt($action.data("export-id"), 10);
        var $select = $action.find(".administration-activity-queue-status-select");
        var status = $select.val();
        var $feedback = $action.find(".administration-activity-queue-status-feedback");

        if (!exportId || !status) {
            return;
        }

        $button.prop("disabled", true);
        $select.prop("disabled", true);
        $feedback.removeClass("is-success is-error").text("Saving...");

        doAjax("administration_queue_export_status_update", {
            export_id: exportId,
            status: status
        }, function (response) {
            var exportData = response && response.export ? response.export : null;

            $feedback
                .removeClass("is-error")
                .addClass("is-success")
                .text(response && response.message ? response.message : "Updated.");

            if (exportData && exportData.status) {
                var $row = $action.closest("tr");
                $row.find("td:nth-child(6)").text(exportData.status);

                if (typeof exportData.progress !== "undefined") {
                    $row.find("td:nth-child(7)").text(exportData.progress + "%");
                }

                if (typeof exportData.datetime_start !== "undefined") {
                    $row.find("td:nth-child(5)").text(exportData.datetime_start ? exportData.datetime_start : "—");
                }
            }
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
            $button.prop("disabled", false);
            $select.prop("disabled", false);
        });
    });
});
