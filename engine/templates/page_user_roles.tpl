{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header2.tpl"}

<div class="panel panel-primary scrollingWrapper">
	<div class="panel-heading">Your profile</div>
	<div class="panel-body scrolling">

		{include file="inc_system_messages.tpl"}

		<div class="panel panel-default">
			<div class="panel-heading">Your system roles</div>
			<div class="panel-body">
				{foreach from=$user.role item=description key=role}
					<span class="btn btn-xs btn-primary" title="{$description}">{$role}</span>
				{/foreach}
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">Your corpora roles</div>
			{if count($corpus_roles)>10}
				<div class="panel-heading"><input class="form-control" id="corpora_filter" type="text" placeholder="Filter.."></div>
			{/if}
			<div class="panel-body" style = "max-height: 500px; overflow: auto;">
				<table id = "corpora_table" class = "table table-striped table-hover sortable">
					<thead>
					<th>Id</th>
					<th>Corpus name</th>
					<th>Roles</th>
					</thead>
					<tbody>
					{foreach from=$corpus_roles item=corpus}
						<tr>
							<td>{$corpus.corpus_id}</td>
							<td>{$corpus.corpus_name}</td>
							<td>
								{foreach from=$corpus.roles item = role}
									<span class="btn btn-xs btn-success" title="{$role.description}">{$role.role}</span>
								{/foreach}
							</td>
						</tr>
					{/foreach}
					</tbody>
				</table>
			</div>
		</div>

		{if $user.role.loggedin}
		<div class="panel panel-default">
			<div class="panel-heading">Options</div>
			<div class="panel-body">
				<ul>
					<li><a class="option" id="password_change_form" href="#">Password change</a></li>
				</ul>
				<form  class="password_change_form" style="padding: 5px; background: #eee;  margin: 10px; width: 400px; display: none" action="index.php?page=user_roles" method="post">
					<input type="hidden" name="action" value="user_password_change"/>
					<table border="0" width="450">
						<tr>
							<td class="tekst"> Old password: </td>
							<td><input class="password_change" type="password" name="old_pass" size="20" /></td>
						</tr>
						<tr>
							<td class="tekst"> New password: </td>
							<td><input class="password_change" type="password" name="new_pass1" size="20" maxlength="20"/></td>
						</tr>
						<tr>
							<td class="tekst"> Repeat new password: </td>
							<td><input class="password_change" type="password" name="new_pass2" size="20" maxlength="20"/></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td align="center"><input type="submit" class="password_change" value="Change password" disabled="disabled" /></td>
						</tr>
					</table>
				</form>
			</div>
		</div>
		{/if}
	</div>
</div>

{include file="inc_footer.tpl"}
