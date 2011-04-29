<div id="main_menu">
	<ul>
		<li{if $page=="home" || $corpus.id} class="active"{/if}><a href="index.php?page=home">Corpora</a></li>
		<li{if $page=="download"} class="active"{/if}><a href="index.php?page=download">Download</a></li>
		<li{if $page=="ner"} class="active"{/if}><a href="index.php?page=ner">NER</a>-<small style="color: red">tests</small></li>
		<li{if $page=="events"} class="active"{/if}><a href="index.php?page=events">Events</a>-<small style="color: red">tests</small></li>
	{if "admin"|has_role}
		<li{if $page=="user_activities"} class="active"{/if}><a href="index.php?page=user_activities">User activities</a></li>
		<li{if $page=="event_edit"} class="active"{/if}><a href="index.php?page=event_edit">Event editor</a></li>
		<li{if $page=="relation_edit"} class="active"{/if}><a href="index.php?page=relation_edit">Relation editor</a></li>
		<li{if $page=="annotation_edit"} class="active"{/if}><a href="index.php?page=annotation_edit">Annotation editor</a></li>
	{/if}
	{if !$RELEASE && $user}
		<li{if $page=="backup"} class="active"{/if}><a href="index.php?page=backup">SQL backup</a></li>
		<li{if $page=="notes"} class="active"{/if}><a href="index.php?page=notes">Notatki</a></li>
	{/if}
	</ul>
</div>
{if $corpus.id}
	<div id="sub_menu">
		<div style="background: #333; color: white; padding: 2px">
			Corpora</a> &raquo; <b>{$corpus.name}</b> {if $row.title} &raquo; <b>{$row.title}</b>{/if} 		
		</div>
		<ul>
			<li{if $page=="corpus"} class="active"{/if}><a href="index.php?page=corpus&amp;corpus={$corpus.id}">Settings</a></li>
			<li{if $page=="browse" || $page=="report"} class="active"{/if}><a href="index.php?page=browse&amp;corpus={$corpus.id}">Documents</a></li>
			<li{if $page=="annmap"} class="active"{/if}><a href="index.php?page=annmap&amp;corpus={$corpus.id}">Annotation map</a></li>
			<li{if $page=="stats"} class="active"{/if}><a href="index.php?page=stats&amp;corpus={$corpus.id}">Statistics</a></li>
	{if $corpus.id == 3}
			<li{if $page=="lps_stats"} class="active"{/if}><a href="index.php?page=lps_stats&amp;corpus={$corpus.id}">Author statistics</a></li>	
	{/if}
	{if $corpus.id == 1}
	{if !$RELEASE && $user && false}
			<li{if $page=="list_total"} class="active"{/if}><a href="index.php?page=list_total">Postęp</a></li>
			<li{if $page=="titles"} class="active"{/if}><a href="index.php?page=titles">Nagłówki</a></li>
	{/if}
			<li{if $page=="ontology"} class="active"{/if}><a href="index.php?page=ontology&amp;corpus={$corpus.id}">Ontology</a></li>
	{/if}
	{if "add_documents"|has_corpus_role_or_owner || "admin"|has_role}
			<li{if $page=="document_edit"} class="active"{/if}><a href="index.php?page=document_edit&amp;corpus={$corpus.id}">Add document</a></li>
	{/if}
		</ul>
	</div>
{/if}