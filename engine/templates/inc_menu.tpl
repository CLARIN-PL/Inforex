{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
<div id="main_menu">
	<ul>
		<li{if $page=="home" || $corpus.id} class="active"{/if}><a href="index.php?page=home">Corpora</a></li>
		<li{if $page=="ner"} class="active"{/if}><a href="index.php?page=ner">Liner2</a></li>
		<li{if $page=="ccl_viewer"} class="active"{/if}><a href="index.php?page=ccl_viewer">CCL Viewer</a></li>
	{if $config->wccl_match_enable}
        <li{if $page=="wccl_match_tester"} class="active"{/if}><a href="index.php?page=wccl_match_tester">Wccl Match Tester</a></li>
    {/if}
	{if "admin"|has_role}
        <li{if in_array($page, array("annotation_edit","relation_edit","event_edit","sense_edit","user_admin")) } class="active"{/if}>
            <a href="index.php?page=annotation_edit">Administration</a></li>
	{/if}	
	</ul>
</div>

{if $corpus.id && ( "read"|has_corpus_role_or_owner || "admin"|has_role || $corpus.public ) }
	<div id="sub_menu">
		<div style="background: #333; color: white; padding: 2px">
			<span class="corpora_list" style="cursor: pointer" 
					onmouseover="$(this).css('text-decoration', 'underline');" 
					onmouseout="$(this).css('text-decoration', 'none');" 
					onclick="if($('.user_corpus_list').hasClass('show_corpus_list')) $('.user_corpus_list').removeClass('show_corpus_list').addClass('hide_corpus_list'); else $('.user_corpus_list').removeClass('hide_corpus_list').addClass('show_corpus_list');">
					↧ Corpora
					<div class="user_corpus_list hide_corpus_list"><ul>
					{foreach from=$corpus.user_corpus item=element}
						<li><a href="index.php?page={if $row.title}browse{else}{$page}{/if}&amp;corpus={$element.corpus_id}">{$element.name}</a></li>
					{/foreach}	
					</ul></div>				
			</span> &raquo; <b>{$corpus.name}</b> {if $row.subcorpus_name} &raquo; <b>{$row.subcorpus_name}</b> {/if} {if $row.title} &raquo; <b>{$row.title}</b>{/if}
		</div>
		<div style="float:left">
			<ul>
                <li{if $page=="start"} class="active"{/if}><a href="index.php?page=start&amp;corpus={$corpus.id}">Start</a></li>
		{if "admin"|has_role || "manager"|has_corpus_role_or_owner}
				<li{if $page=="corpus"} class="active"{/if}><a href="index.php?page=corpus&amp;corpus={$corpus.id}">⇰Settings</a></li>
		{/if}
				<li{if $page=="browse" || $page=="report"} class="active"{/if}><a href="index.php?page=browse&amp;corpus={$corpus.id}{if $report_id && $report_id>0}&amp;r={$report_id}{/if}">⇰Documents</a></li>
		{if "browse_annotations"|has_corpus_role_or_owner}
				<li{if $page=="annmap"} class="active"{/if}><a href="index.php?page=annmap&amp;corpus={$corpus.id}">⇰Annotations</a></li>
                <li{if $page=="annotation_browser"} class="active"{/if}><a href="index.php?page=annotation_browser&amp;corpus={$corpus.id}">⇰Annotation browser</a></li>
		{/if}
		{if "browse_relations"|has_corpus_role_or_owner}
				<li{if $page=="relations"} class="active"{/if}><a href="index.php?page=relations&amp;corpus={$corpus.id}">⇰Relations</a></li>
		{/if}
        {if "run_tests"|has_corpus_role_or_owner}
				<li{if $page=="tests"} class="active"{/if}><a href="index.php?page=tests&amp;corpus={$corpus.id}">⇰Tests</a></li>
		{/if}
				<li{if $page=="stats"} class="active"{/if}><a href="index.php?page=stats&amp;corpus={$corpus.id}">⇰Statistics</a></li>
		{if $corpus.id == 3}
                <li{if $page=="lps_authors"} class="active"{/if}><a href="index.php?page=lps_authors&amp;corpus={$corpus.id}">⇰Authors of letters</a></li> 
				<li{if $page=="lps_stats"} class="active"{/if}><a href="index.php?page=lps_stats&amp;corpus={$corpus.id}">⇰PCSN statistics</a></li>	
				<li{if $page=="lps_metric"} class="active"{/if}><a href="index.php?page=lps_metric&amp;corpus={$corpus.id}">⇰PCSN metrics</a></li>	
		{/if}
                <li{if $page=="word_frequency"} class="active"{/if}><a href="index.php?page=word_frequency&amp;corpus={$corpus.id}">⇰Words frequency</a></li>
                <li{if $page=="wccl_match"} class="active"{/if}><a href="index.php?page=wccl_match&amp;corpus={$corpus.id}">⇰Wccl Match</a></li>
		{if $corpus.id == 1}
			{if !$RELEASE && $user && false}
					<li{if $page=="list_total"} class="active"{/if}><a href="index.php?page=list_total">⇰Postęp</a></li>
					<li{if $page=="titles"} class="active"{/if}><a href="index.php?page=titles">⇰Nagłówki</a></li>
			{/if}
					<li{if $page=="ontology"} class="active"{/if}><a href="index.php?page=ontology&amp;corpus={$corpus.id}">⇰Ontology</a></li>
		{/if}
        {if "tasks"|has_corpus_role_or_owner}
                <li{if $page=="tasks" or $page=="task"} class="active"{/if}><a href="index.php?page=tasks&amp;corpus={$corpus.id}">⇰Tasks</a></li>
        {/if}
		{if "add_documents"|has_corpus_role_or_owner || "admin"|has_role}
				<li{if $page=="document_edit"} class="active"{/if}><a href="index.php?page=document_edit&amp;corpus={$corpus.id}">⇰Add document</a></li>
		{/if}
			</ul>
		</div>
		<div style="clear:both"></div>
	</div>
	
    {if $page=="report"}
        <div id="document_navigation">
            <span title="Liczba raportów znajdujących się przed aktualnym raportem"> ({$row_prev_c}) </span>     
            {if $row_first}<a href="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row_first}">|< pierwszy</a>{else}<span class="inactive">|< pierwszy</span>{/if} ,
            {if $row_prev_100}<a href="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row_prev_100}">-100</a>{else}<span class="inactive">-100</span>{/if} ,
            {if $row_prev_10}<a href="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row_prev_10}">-10</a> {else}<span class="inactive">-10</span>{/if} ,
            {if $row_prev}<a id="article_prev" href="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row_prev}">< poprzedni</a>{else}<span class="inactive">< poprzedni</span>{/if}
            | <span style="color: black"><b>{$row_number}</b> z <b>{$row_prev_c+$row_next_c+1}</b></span> |
            {if $row_next}<a id="article_next" href="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row_next}">następny ></a>{else}<span class="inactive">następny ></span>{/if} ,
            {if $row_next_10}<a href="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row_next_10}">+10</a> {else}<span class="inactive">+10</span>{/if} ,
            {if $row_next_100}<a href="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row_next_100}">+100</a>{else}<span class="inactive">+100</span>{/if} ,
            {if $row_last}<a href="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row_last}">ostatni >|</a>{else}<span class="inactive">ostatni >|</span>{/if}
            <span title"Liczba raportów znajdujących się po aktualnym raporcie">({$row_next_c})</span>
        </div>
    {/if}           
	
{/if}
