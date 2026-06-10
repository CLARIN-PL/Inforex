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
        <div class="access-notice-text">Sign in to continue.</div>
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
<div class="panel panel-default">
    <div class="panel-body"><i class="fa fa-lock fa-5x" aria-hidden="true" style="vertical-align: middle; margin-right: 15px;"></i>You do not have required permission to see this page.</div>
</div>
{/if}

{include file="inc_footer.tpl"}
