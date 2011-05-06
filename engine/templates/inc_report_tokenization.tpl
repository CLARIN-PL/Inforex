<form method="POST" action="index.php?page=report&amp;corpus={$corpus.id}&amp;subpage=tokenization&amp;id={$report_id}" enctype="multipart/form-data">
	<label for="xcesFile">Choose file:</label>
	<input type="file" name="xcesFile" />
	<input type="hidden" name="action" value="report_set_tokens"/>
	<input type="submit" value="Submit"/>
</form>
<div>
{if $message}
	{$message}
{/if}
</div>