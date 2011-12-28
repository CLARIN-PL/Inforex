<div id="main_menu">
	<ul>
		<li{if $page=="home" || $corpus.id} class="active"{/if}><a href="index.php?page=home">Corpora</a></li>
		<li{if $page=="download"} class="active"{/if}><a href="index.php?page=download">Download</a></li>
		<li{if $page=="ner"} class="active"{/if}><a href="index.php?page=ner">NER</a>-<small style="color: red">tests</small></li>
		<li{if $page=="events"} class="active"{/if}><a href="index.php?page=events">Events</a>-<small style="color: red">tests</small></li>
	{if "admin"|has_role}
		<li{if $page=="user_activities"} class="active"{/if}><a href="index.php?page=user_activities">Activities</a></li>
        <li{if $page=="annotation_edit"} class="active"{/if}><a href="index.php?page=annotation_edit">Annotations</a></li>
		<li{if $page=="event_edit"} class="active"{/if}><a href="index.php?page=event_edit">Events</a></li>
		<li{if $page=="relation_edit"} class="active"{/if}><a href="index.php?page=relation_edit">Relations</a></li>
	{/if}
	{if !$RELEASE && $user}
		<li{if $page=="backup"} class="active"{/if}><a href="index.php?page=backup">SQL backup</a></li>
		<li{if $page=="notes"} class="active"{/if}><a href="index.php?page=notes">Notatki</a></li>
	{/if}
	</ul>
</div>
{$corpus.is_public}
{if $corpus.id && ( "read"|has_corpus_role_or_owner || "admin"|has_role || $corpus.public ) }
	<div id="sub_menu">
		<div style="background: #333; color: white; padding: 2px">
			Corpora</a> &raquo; <b>{$corpus.name}</b> {if $row.subcorpus_name} &raquo; <b>{$row.subcorpus_name}</b> {/if} {if $row.title} &raquo; <b>{$row.title}</b>{/if}
		</div>
		<div style="float:left">
			<ul>
		{if "admin"|has_role}
				<li{if $page=="corpus"} class="active"{/if}><a href="index.php?page=corpus&amp;corpus={$corpus.id}">⇰Settings</a></li>
		{/if}
				<li{if $page=="browse" || $page=="report"} class="active"{/if}><a href="index.php?page=browse&amp;corpus={$corpus.id}{if $report_id && $report_id>0}&amp;r={$report_id}{/if}">⇰Documents</a></li>
				<li{if $page=="annmap"} class="active"{/if}><a href="index.php?page=annmap&amp;corpus={$corpus.id}">⇰Annotation map</a></li>
				<li{if $page=="stats"} class="active"{/if}><a href="index.php?page=stats&amp;corpus={$corpus.id}">⇰Statistics</a></li>
		{if $corpus.id == 3}
				<li{if $page=="lps_stats"} class="active"{/if}><a href="index.php?page=lps_stats&amp;corpus={$corpus.id}">⇰PCSN statistics</a></li>	
		{/if}
                <li{if $page=="morphology"} class="active"{/if}><a href="index.php?page=morphology&amp;corpus={$corpus.id}">⇰Morfologia</a></li> 
		{if $corpus.id == 1}
		{if !$RELEASE && $user && false}
				<li{if $page=="list_total"} class="active"{/if}><a href="index.php?page=list_total">⇰Postęp</a></li>
				<li{if $page=="titles"} class="active"{/if}><a href="index.php?page=titles">⇰Nagłówki</a></li>
		{/if}
				<li{if $page=="ontology"} class="active"{/if}><a href="index.php?page=ontology&amp;corpus={$corpus.id}">⇰Ontology</a></li>
		{/if}
		{if "add_documents"|has_corpus_role_or_owner || "admin"|has_role}
				<li{if $page=="document_edit"} class="active"{/if}><a href="index.php?page=document_edit&amp;corpus={$corpus.id}">⇰Add document</a></li>
		{/if}
			</ul>
		</div>
		{if $page=="report"}
		<div id="flagsContainer" style="float:right; padding-right: 5px">
			<div id="flagStates" style="display:none; width: 200px">
				<div>
					<b>New state:</b>
					<ul id="list_of_flags">
					{foreach from="$flags" item=flag}
					   <li>
						  <span class="flagState" flag_id="{$flag.id}" title="{$flag.name}" style="cursor:pointer">
							<img src="gfx/flag_{$flag.id}.png"/> {$flag.name}								
						  </span>
					   </li>
					{/foreach}
					</ul>
				</div>
			</div>			
			<b>Flags </b>:
			{foreach from=$corporaflags item=corporaflag}
				<span 
					class="corporaFlag" 
					cflag_id="{$corporaflag.id}" 
					report_id="{$row.id}"  
					style="padding: 0px 2px 0px 2px; cursor:pointer"
					title="{$corporaflag.name}: {if $corporaflag.flag_id}{$corporaflag.fname}{else}NIE GOTOWY{/if}">
                    <span style="font-size: 1.1em">[</span>
					   <span style="font-size: 12px; padding: 2px 0;">{$corporaflag.short}</span>
                       <img src="gfx/flag_{if $corporaflag.flag_id}{$corporaflag.flag_id}{else}-1{/if}.png" style-"padding-top: 1px"/>
                    <span style="font-size: 16px">]</span>
				</span>
			{/foreach}
			
		</div>
		{/if}
		<div style="clear:both">
		</div>
	</div>
{/if}