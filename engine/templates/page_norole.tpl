{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{assign var="norole" value="1"}
{include file="inc_header2.tpl"}

{if $user == null}
<div class="panel panel-default">
    <div class="panel-body"><i class="fa fa-lock fa-5x" aria-hidden="true" style="vertical-align: middle; margin-right: 15px;"></i>You need to log in.</div>
</div>
<script type="text/javascript">
    $("#loginForm").modal("show");
</script>
{else}
<div class="panel panel-default">
    <div class="panel-body"><i class="fa fa-lock fa-5x" aria-hidden="true" style="vertical-align: middle; margin-right: 15px;"></i>You do not have required permission to see this page.</div>
</div>
{/if}

{include file="inc_footer.tpl"}