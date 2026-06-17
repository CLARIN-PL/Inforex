(function($){
    var currentExportId = null;
    var currentTaskId = null;
    var korpuskopStarting = false;
    var exportRequestInFlight = false;
    var historyRefreshTimer = null;
    var historyRefreshEnabled = true;
    var historyRefreshGraceCycles = 0;
    var currentHistoryPage = 1;
    var historyPageSize = 10;
    var currentModalStep = 1;
    var exportDoneWithoutTaskPolls = 0;
    var reportStartMode = 'not started';
    var kindToFormat = {
        document: {value: 'clarin_parquet_zst', label: 'CLARIN Parquet ZST'},
        dialog: {value: 'dialog_parquet_zst', label: 'Dialog parquet'}
    };

    function getPageQuery(){
        return $.url(window.location.href).attr('query') || '';
    }

    function setLaunchButtonsBusy(isBusy){
        $('#korpuskopOpenExportModal').prop('disabled', isBusy);
        $('#korpuskopStartExport').prop('disabled', isBusy);
        $('#korpuskopCheckExport').prop('disabled', isBusy);
        $('#korpuskopCorpusTypeModal').prop('disabled', isBusy);
        $('#korpuskopFocusWords').prop('disabled', isBusy);
        $('#korpuskopModalPrev').prop('disabled', isBusy);
        $('#korpuskopModalNext').prop('disabled', isBusy);
    }

    function renderPayload(payload){
        if (!payload) {
            return '';
        }
        try {
            return JSON.stringify(payload, null, 2);
        } catch (e) {
            return '';
        }
    }

    function formatStageLabel(stage){
        stage = stage === null || typeof stage === 'undefined' ? '' : String(stage);
        if (!stage) {
            return '-';
        }
        return stage.replace(/_/g, ' ');
    }

    function getSelectedKind(){
        return $('#korpuskopCorpusTypeModal').val() || 'document';
    }

    function getFocusWords(){
        var raw = $('#korpuskopFocusWords').val() || '';
        var unique = {};
        var result = [];
        $.each(String(raw).split(/[\r\n,;]+/), function(_, item){
            var word = $.trim(item);
            var lower = word.toLowerCase();
            if (!word || unique[lower]) {
                return;
            }
            unique[lower] = true;
            result.push(word);
        });
        return result;
    }

    function updateStepSummary(){
        var kind = getSelectedKind();
        var focusWords = getFocusWords();
        $('.corpus-korpuskop-step-summary-kind').text('Type: ' + (kind === 'dialog' ? 'dialogs' : 'documents'));
        $('.corpus-korpuskop-step-summary-focus').text('Focus: ' + (focusWords.length ? focusWords.join(', ') : 'none'));
        $('.corpus-korpuskop-step-summary-format').text('Format: ' + ($('select[name="select-export-format"] option:selected').text() || '-'));
        $('.corpus-korpuskop-step-summary-tagging').text('Tagging: ' + ($('select[name="select-tagging"] option:selected').text() || '-'));
    }

    function updateStepIndicators(step){
        $('.corpus-korpuskop-modal-step-indicator').each(function(){
            var indicator = $(this);
            var indicatorStep = parseInt(indicator.data('step-indicator'), 10);
            indicator.toggleClass('active', indicatorStep === step);
            indicator.toggleClass('completed', indicatorStep < step);
        });
    }

    function showModalStep(step){
        currentModalStep = step;
        $('.corpus-korpuskop-modal-step').hide();
        $('.corpus-korpuskop-modal-step[data-step="' + step + '"]').show();
        if (step !== 2) {
            $('#korpuskopStepValidationWarning').hide();
        }
        $('#korpuskopModalPrev').toggle(step > 1);
        $('#korpuskopModalNext').toggle(step < 3);
        $('#korpuskopCheckExport').toggle(step === 2);
        $('#korpuskopStartExport').toggle(step === 3);
        updateStepIndicators(step);
        if (step === 3) {
            updateStepSummary();
        }
    }

    function syncExportFormat(){
        var kind = getSelectedKind();
        var format = kindToFormat[kind] || kindToFormat.document;
        var select = $('select[name="select-export-format"]');
        select.empty().append(
            $('<option></option>').attr('value', format.value).text(format.label)
        ).val(format.value);
        $('#korpuskopCorpusKind').val(kind);
        $('textarea[name="description"]').val('Corpus report export (' + (kind === 'dialog' ? 'dialogs' : 'documents') + ')');
    }

    function showProgressPanel(){
        $('#korpuskopTaskProgress').show();
    }

    function scrollModalBodyToTop(){
        var modalBody = $('#korpuskopExportForm .corpus-export-modal-body');
        if (modalBody.length) {
            modalBody.stop(true).animate({scrollTop: 0}, 150);
        }
    }

    function normalizeHistoryValue(value){
        return $.trim(String(value === null || typeof value === 'undefined' ? '' : value)).toLowerCase();
    }

    function getHistoryFilterState(){
        return {
            runId: normalizeHistoryValue($('#korpuskopHistoryFilterRunId').val()),
            taskId: normalizeHistoryValue($('#korpuskopHistoryFilterTaskId').val()),
            status: $('#korpuskopHistoryStatusFilter').val() || 'all',
            variant: $('#korpuskopHistoryVariantFilter').val() || 'all',
            size: normalizeHistoryValue($('#korpuskopHistoryFilterSize').val()),
            user: normalizeHistoryValue($('#korpuskopHistoryFilterUser').val()),
            finished: normalizeHistoryValue($('#korpuskopHistoryFilterFinished').val())
        };
    }

    function matchesStatusFilter(status, filter){
        if (filter === 'all') {
            return true;
        }
        if (filter === 'active') {
            return status === 'new' || status === 'process';
        }
        return status === filter;
    }

    function updateHistoryPagination(visibleRowsCount){
        var totalPages = Math.max(1, Math.ceil(visibleRowsCount / historyPageSize));
        if (currentHistoryPage > totalPages) {
            currentHistoryPage = totalPages;
        }
        if (currentHistoryPage < 1) {
            currentHistoryPage = 1;
        }

        var startIndex = visibleRowsCount === 0 ? 0 : ((currentHistoryPage - 1) * historyPageSize) + 1;
        var endIndex = Math.min(visibleRowsCount, currentHistoryPage * historyPageSize);

        $('#korpuskopHistoryPaginationSummary').text(startIndex + '–' + endIndex + ' / ' + visibleRowsCount);
        $('#korpuskopHistoryPageIndicator').text('Page ' + currentHistoryPage + ' / ' + totalPages);
        $('#korpuskopHistoryPrevPage').prop('disabled', currentHistoryPage <= 1);
        $('#korpuskopHistoryNextPage').prop('disabled', currentHistoryPage >= totalPages);
    }

    function applyHistoryFilters(filterState){
        var rows = $('.corpus-korpuskop-history-table tbody tr[data-run-status]');
        var matchedRows = [];

        rows.each(function(){
            var row = $(this);
            var status = normalizeHistoryValue(row.data('run-status'));
            var matches = matchesStatusFilter(status, filterState.status) &&
                normalizeHistoryValue(row.data('run-id')).indexOf(filterState.runId) !== -1 &&
                normalizeHistoryValue(row.data('run-task-id')).indexOf(filterState.taskId) !== -1 &&
                (filterState.variant === 'all' || normalizeHistoryValue(row.data('run-variant')) === normalizeHistoryValue(filterState.variant)) &&
                normalizeHistoryValue(row.data('run-size')).indexOf(filterState.size) !== -1 &&
                normalizeHistoryValue(row.data('run-user')).indexOf(filterState.user) !== -1 &&
                normalizeHistoryValue(row.data('run-finished')).indexOf(filterState.finished) !== -1;

            if (matches) {
                matchedRows.push(row);
            }
        });

        rows.hide();

        var start = (currentHistoryPage - 1) * historyPageSize;
        var end = start + historyPageSize;
        $.each(matchedRows, function(index, row){
            if (index >= start && index < end) {
                row.show();
            }
        });

        $('.corpus-korpuskop-history-empty-row').toggle(rows.length > 0 && matchedRows.length === 0);
        updateHistoryPagination(matchedRows.length);
    }

    function updateHistoryVariantOptions(){
        var select = $('#korpuskopHistoryVariantFilter');
        if (!select.length) {
            return;
        }

        var currentValue = select.val() || 'all';
        var variants = {};
        $('.corpus-korpuskop-history-table tbody tr[data-run-status]').each(function(){
            var variant = $.trim(String($(this).data('run-variant') || ''));
            if (variant) {
                variants[variant] = true;
            }
        });

        var keys = Object.keys(variants).sort();
        var html = '<option value="all">All variants</option>';
        $.each(keys, function(_, variant){
            html += '<option value="' + escapeHtml(variant) + '">' + escapeHtml(variant) + '</option>';
        });
        select.html(html);
        if (currentValue !== 'all' && variants[currentValue]) {
            select.val(currentValue);
        } else {
            select.val('all');
        }
    }

    function updateHistoryFilterCounts(){
        var rows = $('.corpus-korpuskop-history-table tbody tr[data-run-status]');
        var counts = {
            all: rows.length,
            active: 0,
            new: 0,
            process: 0,
            done: 0,
            error: 0
        };

        rows.each(function(){
            var status = $(this).data('run-status');
            if (status === 'new' || status === 'process') {
                counts.active += 1;
            }
            if (status === 'new') {
                counts.new += 1;
            }
            if (status === 'process') {
                counts.process += 1;
            }
            if (status === 'done') {
                counts.done += 1;
            }
            if (status === 'error') {
                counts.error += 1;
            }
        });

        $('#korpuskopHistoryStatusFilter option[value="all"]').text('All (' + counts.all + ')');
        $('#korpuskopHistoryStatusFilter option[value="active"]').text('Active (' + counts.active + ')');
        $('#korpuskopHistoryStatusFilter option[value="new"]').text('New (' + counts.new + ')');
        $('#korpuskopHistoryStatusFilter option[value="process"]').text('Processing (' + counts.process + ')');
        $('#korpuskopHistoryStatusFilter option[value="done"]').text('Done (' + counts.done + ')');
        $('#korpuskopHistoryStatusFilter option[value="error"]').text('Error (' + counts.error + ')');
    }

    function escapeHtml(value){
        return $('<div/>').text(value === null || typeof value === 'undefined' ? '' : String(value)).html();
    }

    function truncateMiddle(value, limit){
        value = value === null || typeof value === 'undefined' ? '' : String(value);
        if (value.length <= limit) {
            return value;
        }
        return value.substring(0, limit - 3) + '...';
    }

    function formatFileSizeMb(bytes){
        var numeric = parseFloat(bytes);
        if (!numeric) {
            return '';
        }
        return (numeric / 1048576).toFixed(2) + ' MB';
    }

    function renderRunStatusBadge(status){
        var iconClass = 'fa-info-circle';
        if (status === 'new') {
            iconClass = 'fa-clock-o';
        } else if (status === 'process') {
            iconClass = 'fa-refresh';
        } else if (status === 'done') {
            iconClass = 'fa-check';
        } else if (status === 'error') {
            iconClass = 'fa-exclamation-triangle';
        }

        return '<span class="corpus-korpuskop-status-badge corpus-korpuskop-status-badge-' + escapeHtml(status) + '">' +
            '<i class="fa ' + iconClass + '" aria-hidden="true"></i>' +
            escapeHtml(status) +
            '</span>';
    }

    function renderRunRow(run){
        var taskCell = '<span class="text-muted">-</span>';
        var fileSizeMb = formatFileSizeMb(run.file_size);
        if (run.task_id) {
            taskCell = '<a href="' + escapeHtml(run.view_url) + '">' + escapeHtml(run.task_id) + '</a>';
        }

        var actions = '';
        if (run.task_id) {
            actions += '<a class="btn btn-xs btn-default" href="' + escapeHtml(run.view_url) + '" title="Show task status"><i class="fa fa-eye" aria-hidden="true"></i></a>';
        }
        if (run.status === 'done' && run.download_url) {
            actions += '<a class="btn btn-xs btn-default" href="' + escapeHtml(run.download_url) + '" title="Download ZIP"><i class="fa fa-download" aria-hidden="true"></i></a>';
        }
        if (run.real_run_id) {
            actions += '<form method="post" class="corpus-korpuskop-delete-form" onsubmit="return confirm(\'Remove the history entry and related report files?\');">' +
                '<input type="hidden" name="korpuskop_action" value="delete_run">' +
                '<input type="hidden" name="run_id" value="' + escapeHtml(run.real_run_id) + '">' +
                '<button type="submit" class="btn btn-xs btn-danger" title="Remove history entry and files"><i class="fa fa-trash" aria-hidden="true"></i></button>' +
                '</form>';
        }

        return '<tr data-run-status="' + escapeHtml(run.status) + '"' +
            ' data-run-id="' + escapeHtml(run.run_id) + '"' +
            ' data-run-task-id="' + escapeHtml(run.task_id || '') + '"' +
            ' data-run-variant="' + escapeHtml(run.input_kind || '') + '"' +
            ' data-run-size="' + escapeHtml(fileSizeMb ? fileSizeMb.replace(' MB', '') : '') + '"' +
            ' data-run-user="' + escapeHtml(run.screename || '-') + '"' +
            ' data-run-finished="' + escapeHtml(run.finished_at || '') + '">' +
            '<td>' + escapeHtml(run.run_id) + '</td>' +
            '<td>' + taskCell + '</td>' +
            '<td>' + renderRunStatusBadge(run.status) + '</td>' +
            '<td>' + escapeHtml(run.input_kind) + '</td>' +
            '<td>' + escapeHtml(fileSizeMb) + '</td>' +
            '<td>' + escapeHtml(run.screename || '-') + '</td>' +
            '<td>' + escapeHtml(run.finished_at || '') + '</td>' +
            '<td class="corpus-korpuskop-history-actions">' + actions + '</td>' +
            '</tr>';
    }

    function renderRunsTable(runs){
        var body = $('.corpus-korpuskop-history-body');
        if (!body.length) {
            return;
        }

        var existingRows = body.find('tr[data-run-status]').length;

        if (!runs || !runs.length) {
            if (existingRows > 0) {
                return;
            }
            body.html(
                '<tr><td colspan="8" class="text-muted">No saved report history.</td></tr>' +
                '<tr class="corpus-korpuskop-history-empty-row" style="display:none;"><td colspan="8" class="text-muted">No runs match the selected filter.</td></tr>'
            );
            currentHistoryPage = 1;
            updateHistoryPagination(0);
            return;
        }

        var html = '';
        $.each(runs, function(_, run){
            html += renderRunRow(run);
        });
        html += '<tr class="corpus-korpuskop-history-empty-row" style="display:none;"><td colspan="8" class="text-muted">No runs match the selected filter.</td></tr>';
        body.html(html);
    }

    function refreshHistory(autoContinue){
        doAjax('korpuskop_runs_status', {url: getPageQuery()}, function(data){
            renderRunsTable(data.runs || []);
            updateHistoryVariantOptions();
            updateHistoryFilterCounts();
            applyHistoryFilters(getHistoryFilterState());
            updateRefreshTimestamp();

            if (historyRefreshEnabled && autoContinue && (data.has_active || historyRefreshGraceCycles > 0)) {
                if (!data.has_active && historyRefreshGraceCycles > 0) {
                    historyRefreshGraceCycles -= 1;
                }
                scheduleHistoryRefresh();
            } else if (!historyRefreshEnabled) {
                clearHistoryRefresh();
            } else if (!data.has_active) {
                clearHistoryRefresh();
            }
        }, function(){
            if (historyRefreshEnabled && autoContinue) {
                scheduleHistoryRefresh();
            }
        });
    }

    function renderHistoryRefreshToggle(){
        var toggle = $('#korpuskopHistoryRefreshState');
        if (!toggle.length) {
            return;
        }
        toggle
            .toggleClass('corpus-korpuskop-refresh-toggle-on', historyRefreshEnabled)
            .toggleClass('corpus-korpuskop-refresh-toggle-off', !historyRefreshEnabled)
            .attr('aria-pressed', historyRefreshEnabled ? 'true' : 'false')
            .find('.corpus-korpuskop-refresh-toggle-text')
            .text(historyRefreshEnabled ? 'On' : 'Off');
    }

    function clearHistoryRefresh(){
        if (historyRefreshTimer) {
            window.clearTimeout(historyRefreshTimer);
            historyRefreshTimer = null;
        }
        renderHistoryRefreshToggle();
    }

    function scheduleHistoryRefresh(){
        if (!historyRefreshEnabled) {
            clearHistoryRefresh();
            return;
        }
        clearHistoryRefresh();
        historyRefreshTimer = window.setTimeout(function(){
            refreshHistory(true);
        }, 5000);
    }

    function setHistoryRefreshEnabled(enabled, shouldRefreshNow){
        historyRefreshEnabled = !!enabled;
        if (!historyRefreshEnabled) {
            historyRefreshGraceCycles = 0;
            clearHistoryRefresh();
            return;
        }
        renderHistoryRefreshToggle();
        if (shouldRefreshNow) {
            refreshHistory(true);
        } else {
            scheduleHistoryRefresh();
        }
    }

    function updateRefreshTimestamp(){
        var now = new Date();
        var pad = function(value){
            return value < 10 ? ('0' + value) : String(value);
        };
        $('#korpuskopHistoryRefreshTime').text(
            'Last updated: ' + pad(now.getHours()) + ':' + pad(now.getMinutes()) + ':' + pad(now.getSeconds())
        );
    }

    function updateUnifiedProgress(status, percent, stage, message, payload){
        var panel = $('#korpuskopTaskProgress');
        var statusValue = panel.find('.korpuskop-task-status');
        var progressValue = panel.find('.korpuskop-task-percent');
        showProgressPanel();
        statusValue
            .text(status || '-')
            .removeClass('status-new status-process status-done status-error');
        if (status) {
            statusValue.addClass('status-' + String(status).toLowerCase());
        }
        progressValue
            .text(percent + '%')
            .removeClass('progress-new progress-process progress-done progress-error');
        if (status) {
            progressValue.addClass('progress-' + String(status).toLowerCase());
        }
        panel.find('.korpuskop-task-stage').text(formatStageLabel(stage));
        panel.find('.korpuskop-task-message').text(message || '-');
        panel.find('.progress-bar')
            .removeClass('progress-new progress-process progress-done progress-error')
            .addClass(status ? ('progress-' + String(status).toLowerCase()) : '')
            .css('width', percent + '%')
            .attr('aria-valuenow', percent)
            .text(percent + '%');
    }

    function updateTaskPanel(data){
        var panel = $('#korpuskopTaskProgress');
        var task = data.task || {};
        var payload = data.message_payload || {};
        if (!currentExportId && data.export_id) {
            currentExportId = data.export_id;
        }
        var percent = parseInt(data.percent || 0, 10);
        if (isNaN(percent)) {
            percent = 0;
        }

        panel.find('.korpuskop-task-id-label').text(task.task_id ? ('#' + task.task_id) : '');
        panel.find('.korpuskop-task-queue').text(typeof data.queue !== 'undefined' ? data.queue : '-');
        panel.find('.korpuskop-export-status').text(currentExportId ? ('done #' + currentExportId) : '-');
        if (payload.report_start_mode) {
            reportStartMode = payload.report_start_mode;
        }
        panel.find('.korpuskop-start-mode').text(reportStartMode);
        updateUnifiedProgress(task.status || '-', percent, payload.stage || '-', payload.message || task.description || '-', payload);

        var download = panel.find('.korpuskop-task-download');
        if (data.download_url) {
            download.attr('href', data.download_url).show();
        } else {
            download.attr('href', '#').hide();
        }
    }

    function resetTaskPanelDownload(){
        $('#korpuskopTaskProgress').find('.korpuskop-task-download').attr('href', '#').hide();
    }

    function resetTaskPanelState(){
        var panel = $('#korpuskopTaskProgress');
        showProgressPanel();
        reportStartMode = 'not started';
        panel.find('.korpuskop-task-id-label').text('');
        panel.find('.korpuskop-task-status')
            .text('new')
            .removeClass('status-new status-process status-done status-error')
            .addClass('status-new');
        panel.find('.korpuskop-task-percent')
            .text('0%')
            .removeClass('progress-new progress-process progress-done progress-error')
            .addClass('progress-new');
        panel.find('.korpuskop-task-queue').text('-');
        panel.find('.korpuskop-task-stage').text(formatStageLabel('queued'));
        panel.find('.korpuskop-task-message').text('The task is waiting for export preparation to begin.');
        panel.find('.korpuskop-export-status').text('not started');
        panel.find('.korpuskop-start-mode').text(reportStartMode);
        panel.find('.progress-bar')
            .removeClass('progress-new progress-process progress-done progress-error')
            .addClass('progress-new')
            .css('width', '0%')
            .attr('aria-valuenow', 0)
            .text('0%');
        resetTaskPanelDownload();
    }

    function pollTask(){
        var panel = $('#korpuskopTaskProgress');
        var taskId = currentTaskId || panel.data('task-id');
        if (!taskId) {
            return;
        }

        doAjax('korpuskop_task_status', {url: getPageQuery(), task_id: taskId}, function(data){
            currentTaskId = taskId;
            panel.data('task-id', taskId);
            updateTaskPanel(data);
            refreshHistory(false);
            if (data.task && (data.task.status === 'new' || data.task.status === 'process')) {
                if (historyRefreshEnabled) {
                    scheduleHistoryRefresh();
                }
                window.setTimeout(pollTask, 1000);
            } else if (historyRefreshEnabled) {
                historyRefreshGraceCycles = 3;
                scheduleHistoryRefresh();
            }
        });
    }

    function startKorpuskopFromExport(){
        if (!currentExportId || korpuskopStarting) {
            return;
        }
        korpuskopStarting = true;
        resetTaskPanelState();
        reportStartMode = 'fallback';
        $('#korpuskopTaskProgress').find('.korpuskop-start-mode').text(reportStartMode);
        updateUnifiedProgress('process', 100, 'export_done', 'Export finished. Starting the report…', {
            export_id: currentExportId,
            stage: 'export_done',
            report_start_mode: reportStartMode
        });

        doAjax('korpuskop_start_from_export', {
            url: getPageQuery(),
            export_id: currentExportId,
            input_kind: getSelectedKind(),
            focus_words: getFocusWords()
        }, function(data){
            korpuskopStarting = false;
            exportDoneWithoutTaskPolls = 0;
            currentTaskId = data.task_id;
            reportStartMode = 'fallback';
            $('#korpuskopTaskProgress').data('task-id', currentTaskId);
            pollTask();
        }, function(){
            korpuskopStarting = false;
            updateUnifiedProgress('error', 100, 'report_start_error', 'The report could not be started automatically. Please try again in a moment.', {
                export_id: currentExportId,
                stage: 'report_start_error'
            });
        });
    }

    function pollExport(){
        if (!currentExportId) {
            return;
        }

        doAjax('export_get_single_status', {url: getPageQuery(), export_id: currentExportId}, function(data){
            var exportData = data.export || {};
            var percent = parseInt(exportData.progress || 0, 10);
            if (isNaN(percent)) {
                percent = 0;
            }

            showProgressPanel();
            $('#korpuskopTaskProgress').find('.korpuskop-export-status').text((exportData.status || '-') + ' #' + currentExportId);
            updateUnifiedProgress(
                exportData.status || '-',
                percent,
                'export',
                exportData.message || 'Export for the report is being prepared.',
                exportData
            );

            if (exportData.status === 'new' || exportData.status === 'process') {
                window.setTimeout(pollExport, 1000);
                return;
            }

            if (exportData.status === 'done') {
                if (data.linked_korpuskop_task_id) {
                    exportDoneWithoutTaskPolls = 0;
                    currentTaskId = data.linked_korpuskop_task_id;
                    reportStartMode = 'automatic';
                    $('#korpuskopTaskProgress').find('.korpuskop-start-mode').text(reportStartMode);
                    $('#korpuskopTaskProgress')
                        .data('task-id', currentTaskId)
                        .data('export-id', currentExportId);
                    pollTask();
                    return;
                }
                exportDoneWithoutTaskPolls += 1;
                updateUnifiedProgress(
                    exportData.status || 'done',
                    100,
                    'export_done',
                    'Export finished. Waiting for the report task to be created.',
                    exportData
                );
                historyRefreshGraceCycles = 3;
                refreshHistory(true);
                if (!korpuskopStarting && exportDoneWithoutTaskPolls >= 2) {
                    startKorpuskopFromExport();
                    return;
                }
                window.setTimeout(pollExport, 1000);
                return;
            }

            if (exportData.status === 'error') {
                exportRequestInFlight = false;
                setLaunchButtonsBusy(false);
            }
            updateUnifiedProgress(exportData.status || 'error', percent, 'export_error', exportData.message || 'The export finished with an error.', exportData);
        });
    }

    function startExportForKorpuskop(definition){
        if (exportRequestInFlight) {
            return;
        }
        exportRequestInFlight = true;
        setLaunchButtonsBusy(true);
        resetTaskPanelState();
        var onError = function(){
            exportRequestInFlight = false;
            setLaunchButtonsBusy(false);
        };
        var params = {
            url: $.url(window.location.href).attr('query'),
            description: definition.description,
            selectors: definition.selectors,
            extractors: definition.extractors,
            indices: definition.indices,
            tagging: definition.taggingMethod,
            export_format: definition.exportFormat,
            post_export_action: 'korpuskop',
            post_export_payload: JSON.stringify({
                input_kind: getSelectedKind(),
                focus_words: getFocusWords()
            })
        };

        doAjax('export_new', params, function(data){
            currentExportId = data.export_id;
            currentTaskId = null;
            exportDoneWithoutTaskPolls = 0;
            $('#korpuskopTaskProgress').data('task-id', '').data('export-id', currentExportId);
            $('#korpuskopExportForm').modal('hide');
            updateUnifiedProgress(
                data.status || 'new',
                0,
                data.duplicate ? 'export_reused' : 'export_queued',
                data.duplicate ? 'An identical active export was found. Reusing the existing request.' : 'The export was added to the Inforex queue.',
                {
                export_id: currentExportId
                }
            );
            pollExport();
        }, onError, null, null, function(){
            startExportForKorpuskop(definition);
        });
    }

    $(function(){
        syncExportFormat();
        showModalStep(1);
        $('#korpuskopCorpusTypeModal').change(syncExportFormat);
        $('#korpuskopCorpusTypeModal, #korpuskopFocusWords').on('change keyup', updateStepSummary);

        $('#korpuskopOpenExportModal').click(function(){
            syncExportFormat();
            updateStepSummary();
            showModalStep(1);
            $('#korpuskopExportForm').modal('show');
        });

        $('#korpuskopModalNext').click(function(){
            syncExportFormat();
            if (currentModalStep === 1) {
                showModalStep(2);
                return;
            }
            if (currentModalStep === 2) {
                var validationResult = validateExportForm();
                if (validationResult === false) {
                    $('#korpuskopStepValidationWarning').show();
                    scrollModalBodyToTop();
                    return;
                }
                $('#korpuskopStepValidationWarning').hide();
                updateStepSummary();
                showModalStep(3);
            }
        });

        $('#korpuskopModalPrev').click(function(){
            if (currentModalStep === 3) {
                showModalStep(2);
                return;
            }
            showModalStep(1);
        });

        $('#korpuskopCheckExport').click(function(){
            if (validateExportForm() === false) {
                $('#korpuskopStepValidationWarning').show();
                scrollModalBodyToTop();
                return;
            }
            $('#korpuskopStepValidationWarning').hide();
        });

        $('#korpuskopStartExport').click(function(){
            if (exportRequestInFlight) {
                return;
            }
            syncExportFormat();
            var result = validateExportForm();
            if (result !== false) {
                startExportForKorpuskop(result);
            }
        });

        if ($('#korpuskopTaskProgress').data('task-id')) {
            currentTaskId = $('#korpuskopTaskProgress').data('task-id');
            pollTask();
        }
        var initialExportId = $('#korpuskopTaskProgress').data('export-id');
        if (!currentTaskId && initialExportId) {
            currentExportId = initialExportId;
            pollExport();
        }

        $('#korpuskopHistoryRefreshState').click(function(){
            setHistoryRefreshEnabled(!historyRefreshEnabled, historyRefreshEnabled === false);
        });

        $('#korpuskopHistoryStatusFilter, #korpuskopHistoryVariantFilter').change(function(){
            currentHistoryPage = 1;
            applyHistoryFilters(getHistoryFilterState());
        });
        $('#korpuskopHistoryFilterRunId, #korpuskopHistoryFilterTaskId, #korpuskopHistoryFilterSize, #korpuskopHistoryFilterUser, #korpuskopHistoryFilterFinished').on('input', function(){
            currentHistoryPage = 1;
            applyHistoryFilters(getHistoryFilterState());
        });
        $('#korpuskopHistoryPrevPage').click(function(){
            if (currentHistoryPage > 1) {
                currentHistoryPage -= 1;
                applyHistoryFilters(getHistoryFilterState());
            }
        });
        $('#korpuskopHistoryNextPage').click(function(){
            currentHistoryPage += 1;
            applyHistoryFilters(getHistoryFilterState());
        });
        $('#korpuskopHistoryPageSize').change(function(){
            var newSize = parseInt($(this).val(), 10);
            historyPageSize = isNaN(newSize) || newSize <= 0 ? 10 : newSize;
            currentHistoryPage = 1;
            applyHistoryFilters(getHistoryFilterState());
        });

        updateHistoryVariantOptions();
        updateHistoryFilterCounts();
        $('#korpuskopHistoryPageSize').val(String(historyPageSize));
        applyHistoryFilters(getHistoryFilterState());
        renderHistoryRefreshToggle();
        scheduleHistoryRefresh();
    });
})(jQuery);
