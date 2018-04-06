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
    <div class="panel-body">
        <div class="row">
            <div class="col-md-1" style="padding: 40px"><i class="fa fa-lock fa-5x" aria-hidden="true"></i></div>
            <div class="col-md-11">
                <div class="row"><h1>{$access->getMessage()}</h1></div>
                <div class="row">This page requires one of the following roles:</div>
                <div class="row">{foreach from=$access->getRolesRequired() item=r}<button type="button" class="btn btn-danger btn-xs" style="margin: 3px">{$r}</button>{/foreach}</div>
                <div class="row">You are granted the following roles:</div>
                <div class="row">{foreach from=$access->getRolesGranted() item=r}<button type="button" class="btn btn-success btn-xs" style="margin: 3px">{$r}</button>{/foreach}</div>
            </div>
        </div>
    </div>
</div>
{/if}

{include file="inc_footer.tpl"}