{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{assign var="norole" value="1"}
{include file="inc_header.tpl"}

{if $user == null}
<div style="background: #E03D19; padding: 1px; margin: 10px; ">
    <div style="background: #FFF194; padding: 5px; color: #733B0E; font-size: 16px; font-weight: bold;"> <img src="gfx/lock.png" title="No access" style="vertical-align: middle"/> You need to log in.</div>
</div>
<script type="text/javascript">
    loginForm(true, null);
</script>
{else}
<div style="background: #E03D19; padding: 1px; margin: 10px; ">
    <div style="background: #FFF194; padding: 5px; color: #733B0E; font-size: 16px; font-weight: bold;"> <img src="gfx/lock.png" title="No access" style="vertical-align: middle"/> You do not have required permission to see this page.</div>
</div>
{/if}

{include file="inc_footer.tpl"}