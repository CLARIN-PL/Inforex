<div id="main_menu">
	<ul>
		<li{if $page=="browse" || $page=="report"} class="active"{/if}><a href="index.php?page=browse">Raporty</a></li>
{if !$RELEASE}
		<li{if $page=="list_total"} class="active"{/if}><a href="index.php?page=list_total">Postęp</a></li>
		<li{if $page=="backup"} class="active"{/if}><a href="index.php?page=backup">SQL backup</a></li>
		<li{if $page=="titles"} class="active"{/if}><a href="index.php?page=titles">Nagłówki</a></li>
		<li{if $page=="notes"} class="active"{/if}><a href="index.php?page=notes">Notatki</a></li>
{/if}
		<li{if $page=="ontology"} class="active"{/if}><a href="index.php?page=ontology">Ontologia</a></li>
		<li{if $page=="stats"} class="active"{/if}><a href="index.php?page=stats">Statystyki</a></li>
		<li{if $page=="annmap"} class="active"{/if}><a href="index.php?page=annmap">Mapa anotacji</a></li>
		<li{if $page=="download"} class="active"{/if}><a href="index.php?page=download">Do pobrania</a></li>
		<!--<li><a href="">Statystyki</a></li>-->
	</ul>
</div>
