<div id="main_menu">
	<ul>
		<li{if $page=="home" || $corpus.id} class="active"{/if}><a href="index.php?page=home">Korpus</a></li>
		<li{if $page=="download"} class="active"{/if}><a href="index.php?page=download">Do pobrania</a></li>
		<li{if $page=="ner"} class="active"{/if}><a href="index.php?page=ner">NER</a>-<small style="color: red">testy</small></li>
	{if !$RELEASE && $user}
		<li{if $page=="backup"} class="active"{/if}><a href="index.php?page=backup">SQL backup</a></li>
		<li{if $page=="notes"} class="active"{/if}><a href="index.php?page=notes">Notatki</a></li>
	{/if}
	</ul>
</div>
{if $corpus.id}
	<div id="sub_menu">
		<ul>
			<li{if $page=="corpus"} class="active"{/if}><a href="index.php?page=corpus&amp;corpus={$corpus.id}"><b>{$corpus.name}</b></a></li>
			<li{if $page=="browse" || $page=="report"} class="active"{/if}><a href="index.php?page=browse&amp;corpus={$corpus.id}">Dokumenty</a></li>
			<li{if $page=="annmap"} class="active"{/if}><a href="index.php?page=annmap&amp;corpus={$corpus.id}">Mapa anotacji</a></li>
	{if $corpus.id == 1}
	{if !$RELEASE && $user && false}
			<li{if $page=="list_total"} class="active"{/if}><a href="index.php?page=list_total">Postęp</a></li>
			<li{if $page=="titles"} class="active"{/if}><a href="index.php?page=titles">Nagłówki</a></li>
	{/if}
			<li{if $page=="ontology"} class="active"{/if}><a href="index.php?page=ontology&amp;corpus={$corpus.id}">Ontologia</a></li>
			<li{if $page=="stats"} class="active"{/if}><a href="index.php?page=stats&amp;corpus={$corpus.id}">Statystyki</a></li>
	{elseif $corpus.id > 1}
			<li{if $page=="document_edit"} class="active"{/if}><a href="index.php?page=document_edit&amp;corpus={$corpus.id}">Dodaj dokument</a></li>
	{/if}
		</ul>
	</div>
{/if}