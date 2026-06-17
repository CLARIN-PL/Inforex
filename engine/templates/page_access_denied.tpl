{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{assign var="norole" value="1"}
{include file="inc_header2.tpl"}

{if $user == null}
<div class="access-notice access-notice-login">
    <div class="access-notice-icon"><i class="fa fa-lock" aria-hidden="true"></i></div>
    <div class="access-notice-copy">
        <div class="access-notice-title">You need to log in.</div>
        <div class="access-notice-text">Sign in to access this page.</div>
    </div>
</div>
<script type="text/javascript">
    if (window.inforexAuthMode === "oidc") {
        window.location.href = "index.php?page=login_oidc";
    } else {
        $("#loginForm").modal("show");
    }
</script>
{else}
<div class="access-denied-page">
    <div class="panel administration-content-panel access-denied-panel">
        <div class="panel-heading administration-content-heading access-denied-heading">
            <span class="administration-content-heading-icon access-denied-heading-icon"><i class="fa fa-shield" aria-hidden="true"></i></span>
            <span>Access denied</span>
        </div>
        <div class="panel-body access-denied-body">
            <div class="access-denied-layout">
                <div class="access-denied-hero">
                    <div class="access-denied-hero-mark">
                        <i class="fa fa-lock" aria-hidden="true"></i>
                    </div>
                    <div class="access-denied-hero-copy">
                        <div class="access-denied-eyebrow">Permission required</div>
                        <h1 class="access-denied-title">You do not have permission to open this page.</h1>
                        <p class="access-denied-text">Your current account does not have the permissions required for this view. If you believe you should have access, please contact a corpus administrator.</p>
                    </div>
                </div>

                <div class="access-denied-grid">
                    <div class="access-denied-card access-denied-card-required">
                        <div class="access-denied-card-label">Required roles</div>
                        <div class="access-denied-tags">
                            {foreach from=$access->getRolesRequired() item=r}
                                <span class="access-denied-tag access-denied-tag-required">{$r}</span>
                            {/foreach}
                        </div>
                    </div>
                    <div class="access-denied-card access-denied-card-granted">
                        <div class="access-denied-card-label">Your current roles</div>
                        <div class="access-denied-tags">
                            {foreach from=$access->getRolesGranted() item=r}
                                <span class="access-denied-tag access-denied-tag-granted">{$r}</span>
                            {/foreach}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{/if}

{include file="inc_footer.tpl"}
