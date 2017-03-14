{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header2.tpl"}

<div style="margin: 5px">
{*<div class="alert alert-info" role="alert">Upload a set of text files with metadata.</div>*}

{if $action_error}
<div class="alert alert-danger">
	<strong>Error!</strong> {$action_error}
</div>
{/if}

{if $action_performed}
	<div class="alert alert-success">
		<strong>Success!</strong> {$action_performed}
	</div>
{/if}

{*{include file="inc_system_messages.tpl"}*}
{*
<form >
	Plik zip z plikami tekstowymi: 
	<input type="file" name="files"/>
	<input class="button" type="submit" name="upload" value="Upload"/>
</form>
</div>
*}

	<div class="panel panel-default">
		<div class="panel-heading">Upload a set of <em>txt</em> files.</div>
		<div class="panel-body">

<form class="form-horizontal" method="post" action="index.php?corpus={$corpus.id}&page=upload"  enctype="multipart/form-data">
	<input type="hidden" name="action" value="upload"/>
	<div class="form-group">
		<label for="inputEmail" class="control-label col-xs-1">Zip file</label>
		<div class="col-xs-4">
			<input type="file" name="files" class="form-control" id="inputEmail" placeholder="Email">
			The Zip file must contain a set of <em>txt</em> files. File's metadata should be stored in a <em>ini</em> file with the same name as the <em>txt</em> file. The <em>ini</em> file must have the following format:
			<br/>
			<pre style="white-space: pre-wrap">[metadata]
url = "<i>Path to a web with the document source</i>"
publish_date = "<i>Publish date in the format of YYYY-MM-DD</i>"
author = "<i>Author name</i>"
title = "<i>Document title</i>"</pre>
		</div>
	</div>
	<div class="form-group">
		<label for="inputPassword" class="control-label col-xs-1">Subcorpus</label>
		<div class="col-xs-4">
			<select name="subcorpus_id" id="listSubcorpora" style="width: 400px" class="form-control">
				<option value="">none</option>
                {foreach from=$subcorpora item=s}
					<option value="{$s.subcorpus_id}">{$s.name}</option>
                {/foreach}
			</select>
			<div class="checkbox">
			<label style="line-height: 20px">
				<input type="checkbox" name="autosplit" id="checkboxSubcorpora" value="option1"> Split into subcorpora based on the file prefix: <code>
					<span style="text-decoration: underline" title="Subcorpus name">SUBCORPUS</span><em title="Separator">-</em><span style="text-decoration: underline" title="Document name">DOCUMENT_NAME</span>.txt</code>.
			</label>
			</div>
		</div>
	</div>
	<div class="form-group">
		<div class="col-xs-offset-1 col-xs-10">
			<button type="submit" class="btn btn-primary">Upload</button>
		</div>
	</div>
</form>
</div>
	</div>

</div>

{include file="inc_footer.tpl"}
