{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{* Komunikaty systemowe *}
{if $action_permission_denied}
<div style="background: #A6001C; padding: 2px; margin: 2px; ">
	<div style="float: left; color: white; padding: 5px; font-weight: bold;">Access denied:</div>
	<div style="background: #FFF194; margin-left: 150px; padding: 5px; color: #A6001C">{$action_permission_denied}</div>
</div>
{/if}

{if $action_error}
<div style="background: #E03D19; padding: 2px; margin: 2px; ">
	<div style="float: left; color: white; padding: 5px; font-weight: bold;">Error:</div>
	<div style="background: #FFF194; margin-left: 150px; padding: 5px; color: #733B0E">{$action_error}</div>
</div>
{/if}

{if $action_performed}
<div style="background: #6B9100; padding: 2px; margin: 2px; ">
	<div style="float: left; color: white; padding: 5px; font-weight: bold;">Success:</div>
	<div style="background: #FFF194; margin-left: 150px; padding: 5px; color: #3E5400">{$action_performed}</div>
</div>
{/if}

{if $page_permission_denied}
<div style="background: #A6001C; padding: 2px; margin: 2px; ">
	<div style="float: left; color: white; padding: 5px; font-weight: bold;">Access denied:</div>
	<div style="background: #FFF194; margin-left: 150px; padding: 5px; color: #A6001C">&raquo;{$page_permission_denied}</div>
</div>
{/if}
