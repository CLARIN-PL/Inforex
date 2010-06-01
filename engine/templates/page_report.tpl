{include file="inc_header.tpl"}

{if $view!='full' && false}
	<td style="vertical-align: top; width: 200px; border: 1px solid #444; background: linen">
		<div style="border: 1px solid #444; background: white; margin: 5px; padding: 2px; text-align: center;">poprzednie 10 raportów</div>
		<dl>
		{foreach from=$reports item=report}
			<dt style="float: left; width: 35px; text-align: right;">#{$report.id}</dt> <dd style="margin-left: 40px"><a href="">{$report.title}</a></dd>
		{/foreach}
		</dl>
		<div style="border: 1px solid #444; background: white; margin: 5px; padding: 2px; text-align: center;">następne 10 raportów</div>
	</td>	
{/if}


<td class="table_cell_content">
<!--
<div style="float: right">
	{if $view=="full"}
	<a href="index.php?page=report&amp;id={$row.id}&amp;view=noraml">normalny widok</a>	
	{else}
	<a href="index.php?page=report&amp;id={$row.id}&amp;view=full">pełny ekran</a>
	{/if}
</div>
-->
<div style="text-align: center" class="pagging">
	<span title="Liczba raportów znajdujących się przed aktualnym raportem"> ({$row_prev_c}) </span>	 
	{if $row_first}<a href="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row_first}">|< pierwszy</a>{else}<span class="inactive">|< pierwszy</span>{/if} ,
	{if $row_prev_100}<a href="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row_prev_100}">-100</a>{else}<span class="inactive">-100</span>{/if} ,
	{if $row_prev_10}<a href="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row_prev_10}">-10</a> {else}<span class="inactive">-10</span>{/if} ,
	{if $row_prev}<a id="article_prev" href="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row_prev}">< poprzedni</a>{else}<span class="inactive">< poprzedni</span>{/if}
	| nr <b>{$row_number}</b> (#<b>{$row.id}</b>) |
	{if $row_next}<a id="article_next" href="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row_next}">następny ></a>{else}<span class="inactive">następny ></span>{/if} ,
	{if $row_next_10}<a href="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row_next_10}">+10</a> {else}<span class="inactive">+10</span>{/if} ,
	{if $row_next_100}<a href="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row_next_100}">+100</a>{else}<span class="inactive">+100</span>{/if} ,
	{if $row_last}<a href="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row_last}">ostatni >|</a>{else}<span class="inactive">ostatni >|</span>{/if}
	<span title"Liczba raportów znajdujących się po aktualnym raporcie">({$row_next_c})</span>
</div>

<div id="tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all" style="background: #f3f3f3; margin-bottom: 5px; ">
	<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
		<li class="ui-state-default ui-corner-top {if $subpage=='preview'}ui-state-active ui-tabs-selected{/if}">
			<a href="index.php?page=report&amp;corpus={$corpus.id}&amp;subpage=preview&amp;id={$row.id}">Tekst</a></li>
		<li class="ui-state-default ui-corner-top {if $subpage=='html'}ui-state-active ui-tabs-selected{/if}">
			<a href="index.php?page=report&amp;corpus={$corpus.id}&amp;subpage=html&amp;id={$row.id}">HTML</a></li>
		{if $row.corpora==1}
		<li class="ui-state-default ui-corner-top {if $subpage=='raw'}ui-state-active ui-tabs-selected{/if}">
			<a href="index.php?page=report&amp;corpus={$corpus.id}&amp;subpage=raw&amp;id={$row.id}">Źródłowy dokument</a></li>
		<li class="ui-state-default ui-corner-top {if $subpage=='takipi'}ui-state-active ui-tabs-selected{/if}">
			<a href="index.php?page=report&amp;corpus={$corpus.id}&amp;subpage=takipi&amp;id={$row.id}">TaKIPI</a></li>
		{/if}
	{if !$RELEASE && $user}	
		{if "edit_documents"|has_corpus_role}
		<li class="ui-state-default ui-corner-top {if $subpage=='edit'}ui-state-active ui-tabs-selected{/if}">
			<a href="index.php?page=report&amp;corpus={$corpus.id}&amp;subpage=edit&amp;id={$row.id}">Edycja</a></li>
		{if $row.corpora==1}
		<li class="ui-state-default ui-corner-top {if $subpage=='edit_raw'}ui-state-active ui-tabs-selected{/if}">
			<a href="index.php?page=report&amp;corpus={$corpus.id}&amp;subpage=edit_raw&amp;id={$row.id}">Edycja / źródło</a></li>
		{/if}
		{/if}
		{if "annotate"|has_corpus_role}
		<li class="ui-state-default ui-corner-top {if $subpage=='annotator'}ui-state-active ui-tabs-selected{/if}">
			<a href="index.php?page=report&amp;corpus={$corpus.id}&amp;subpage=annotator&amp;id={$row.id}">Anotacja</a></li>
		{/if}
	{/if}
		{if $row.corpora==1}
		<li class="ui-state-default ui-corner-top {if $subpage=='tei'}ui-state-active ui-tabs-selected{/if}">
			<a href="index.php?page=report&amp;corpus={$corpus.id}&amp;subpage=tei&amp;id={$row.id}">TEI</a></li>
		{/if}
	</ul>

	<div>
		{if $subpage=='edit' || $subpage=='edit_raw' || $subpage=='annotator' || $subpage=='tei'}
			{include file="$subpage_file"}	
		{else}
		<br/>
		<div class="ui-widget ui-widget-content ui-corner-all" style="margin: 5px">			
		<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Meta dane raportu</div>		
			<table id="report">
				<tr>
					<th>Status:</th>
					<td><i>{$row.status_name}</i></td>
				</tr>
				<tr>
					<th>Typ:</th>
					<td><i>{$row.type_name}</i></td>
				</tr>
				<tr>
					<th>Tytuł:</th>
					<td><b>{$row.title}</b></td>
				</tr>
				<tr>
					<th>Firma:</th>
					<td>{$row.company}</td>
				</tr>
				<tr>
					<th>Link:</th>
					<td>{$row.link}</td>
				</tr>				
			</table>
		</div>
		{include file="$subpage_file"}
		{/if}
	</div>

	{include file="inc_system_messages.tpl"}

</div>
</td>

{include file="inc_footer.tpl"}