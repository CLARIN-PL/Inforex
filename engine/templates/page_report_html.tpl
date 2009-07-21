{include file="inc_header.tpl"}
{include file="inc_menu.tpl"}

{*
<script type="text/javascript">
jQuery(document).ready(function(){ldelim}
	demoOnLoad( userid, 'gpw#r{$row.id}', serviceRoot, '' );
{rdelim});
</script>
*}

<div style="float: right; margin: 5px;">
<form method="post" action="index.php?page=report&amp;id={$row.id}" style="display: inline">
Typ zdarzenia: {$select_type}; Status: {$select_status} <input type="submit" value="zapisz" name="zapisz"/>
</form>
{if ($row.status==1)}
 | <form method="post" action="index.php?page=report&amp;id={$row.id}" style="display: inline">
	<input type="submit" value="OK" name="zapisz" id="accept"/>
	<input type="hidden" value="{$row.type}" name="type"/> 
	<input type="hidden" value="2" name="status"/> 
</form>
{/if}
 | <form method="get" action="index.php" style="display: inline">
 	<input type="hidden" name="page" value="report"/>
 	<input type="hidden" name="id" value="{$row.id}"/>
 	<input type="hidden" name="edit" value="1"/>
	<input type="submit" value="edytuj"/>
</form>

</div>
<h1><a href="index.php?page=list_total">Raporty</a>
 &raquo; <a href="index.php?page=list&amp;year={$year}&amp;month={$month}">{$year}-{$month}</a>
 &raquo; {$row.id}</h1>
<hr/>

<div style="text-align: left">
	{if $row_prev}<a id="article_prev" href="index.php?page=report_html&amp;id={$row_prev}"><< poprzedni</a>{else}poprzedni{/if}
	| <a href="html.php?id={$row.id}">html</a> | 
	{if $row_next}<a id="article_next" href="index.php?page=report_html&amp;id={$row_next}">następny >></a>{else}następny{/if}
</div>

{include file="inc_report_menu_view.tpl"}


{if ($row.status==1)}
<div style="float: right; width: 700px;">
	<iframe src ="index.php?page=raw&amp;id={$row.id}" width="100%" height="450">
	  <p>Your browser does not support iframes.</p>
	</iframe>
</div>
{/if}
<div>
	<h2>{$row.title}</h2>
	<h3>{$row.company}</h3>

	<code class="html" style="white-space: pre-wrap"><br/>{$content}</code>
		
</div>

{*
<hr style="clear: both" />
<small><a href="{$row.link}" target="_blank">{$row.link}</a></small>
<hr/>
Skróty:
<ul>
<li>k - poprzedni komunikat,</li>
<li>l - następny komunikat,</li>
<li>s - zapisz komunikat.</li>
</ul>
*}
{include file="inc_footer.tpl"}