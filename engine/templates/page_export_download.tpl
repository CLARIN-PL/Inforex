{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header2.tpl"}

<style>
    .export-download-error-wrap {
        display: flex;
        justify-content: center;
        padding: 28px 16px;
    }

    .export-download-error {
        background: linear-gradient(180deg, #fff8f7 0%, #fff2f0 100%);
        border: 1px solid #e7c2bc;
        border-radius: 12px;
        box-shadow: 0 10px 24px rgba(95, 45, 40, 0.08);
        color: #8f342c;
        max-width: 920px;
        text-align: center;
        width: 100%;
    }

    .export-download-error .panel-body {
        padding: 26px 28px;
    }

    .export-download-error-icon {
        color: #b2483e;
        display: block;
        font-size: 42px;
        margin-bottom: 12px;
    }

    .export-download-error-title {
        color: #7d2b24;
        display: block;
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .export-download-error-message {
        font-size: 14px;
        line-height: 1.6;
        margin: 0 auto;
        max-width: 760px;
        word-break: break-word;
    }

    .export-download-error-path {
        color: #5c6872;
        display: inline-block;
        font-family: "Consolas", "Monaco", monospace;
        font-size: 13px;
        margin: 6px 0;
    }
</style>

<div class="export-download-error-wrap">
    <div class="panel export-download-error">
        <div class="panel-body">
            <i class="fa fa-exclamation-circle export-download-error-icon" aria-hidden="true"></i>
            <span class="export-download-error-title">Export file is unavailable</span>
            <p class="export-download-error-message">
                Export file
                <span class="export-download-error-path">{$file|escape}</span>
                does not exist. You cannot download it.
            </p>
        </div>
    </div>
</div>

{include file="inc_footer.tpl"}
