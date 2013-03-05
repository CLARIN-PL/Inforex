<div id="main_menu">
	<ul>
		<li{if $page=="home" || $corpus.id} class="active"{/if}><a href="index.php?page=home">Corpora</a></li>
		<li{if $page=="download"} class="active"{/if}><a href="index.php?page=download">Download</a></li>
		<li{if $page=="ner"} class="active"{/if}><a href="index.php?page=ner">NER</a></li>
		<li{if $page=="ccl_viewer"} class="active"{/if}><a href="index.php?page=ccl_viewer">CCL Viewer</a></li>
	{if "admin"|has_role}
        <li{if in_array($page, array("annotation_edit","relation_edit","event_edit","sense_edit","user_admin")) } class="active"{/if}>
            <a href="index.php?page=annotation_edit">Administration</a></li>
	{/if}	
	</ul>
</div>
{$corpus.is_public}
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
		{if "admin"|has_role || "manager"|has_corpus_role_or_owner}
				<li{if $page=="corpus"} class="active"{/if}><a href="index.php?page=corpus&amp;corpus={$corpus.id}">⇰Settings</a></li>
		{/if}
				<li{if $page=="browse" || $page=="report"} class="active"{/if}><a href="index.php?page=browse&amp;corpus={$corpus.id}{if $report_id && $report_id>0}&amp;r={$report_id}{/if}">⇰Documents</a></li>
		{if "browse_annotations"|has_corpus_role_or_owner}
				<li{if $page=="annmap"} class="active"{/if}><a href="index.php?page=annmap&amp;corpus={$corpus.id}">⇰Annotations</a></li>
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
			{if "delete_documents"|has_corpus_role_or_owner}
			<div id="optionsContainer" style="float:right; padding-right: 5px">
				<b>Options: </b>
					<span class="optionsDocument" report_id="{$row.id}" style="padding: 0px 2px 0px 2px; cursor:pointer" title="Delete document" corpus={$corpus.id}>
                	    <span style="font-size: 1.1em">[</span>
						   <span style="font-size: 12px; padding: 2px 0;">delete</span>                       
	                    <span style="font-size: 16px">]</span>
					</span>
			</div>
			{/if}
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
