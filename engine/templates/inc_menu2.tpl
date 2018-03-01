{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div class="tnav">
	<nav class="navbar navbar-default">
		<div class="container-fluid">
			<div class="navbar-header">
				<a href="index.php"><img class="logo" src="gfx/inforex_logo.png" alt="Inforex"></a>
			</div>
			<ul class="nav navbar-nav">
				<li class="{if $page=="home"} active{/if}">
					<a href="index.php?page=home">Corpora</a>
				</li>
                {if $corpus.id}
					<li class="active dropdown navbar-sub corpus_select_nav">
						<a class="dropdown-toggle" data-toggle="dropdown" href="index.php?page=start&amp;corpus={$corpus.id}"><b>{$corpus.name}</b>
							<span class="caret"></span></a>
						<ul class="dropdown-menu">

                            {if !empty($corpus.user_owned_corpora)}
                                <li class="dropdown-submenu corpora_collapse">
                                    <a tabindex="-1" href="#">My corpora</a>
                                    <ul class="dropdown-menu corpus_dropdown_menu">
                                        {if empty($corpus.user_owned_corpora)}
                                            <li>Empty</li>
                                        {/if}
                                        {foreach from=$corpus.user_owned_corpora item=element}
                                            <li><a href="index.php?page={if $row.title}browse{else}{$page}{/if}&amp;corpus={$element.corpus_id}">{$element.name}</a></li>
                                        {/foreach}
                                    </ul>
                                </li>
                            {/if}
                            {if !empty($corpus.public_corpora)}
                                <li class="dropdown-submenu corpora_collapse">
                                    <a tabindex="-1" href="#">Public corpora</a>
                                    <ul class="dropdown-menu corpus_dropdown_menu">
                                        {foreach from=$corpus.public_corpora item=element}
                                            <li><a href="index.php?page={if $row.title}browse{else}{$page}{/if}&amp;corpus={$element.corpus_id}">{$element.name} <strong>({$element.screename})</strong></a></li>
                                        {/foreach}
                                    </ul>
                                </li>
                            {/if}

                            {if !empty($corpus.private_corpora)}
                                <li class="dropdown-submenu corpora_collapse">
                                    <a tabindex="-1" href="#">Private corpora</a>
                                    <ul class="dropdown-menu corpus_dropdown_menu">
                                        {foreach from=$corpus.private_corpora item=element}
                                            <li><a href="index.php?page={if $row.title}browse{else}{$page}{/if}&amp;corpus={$element.corpus_id}">{$element.name} <strong>({$element.screename})</strong></a></li>
                                        {/foreach}
                                    </ul>
                                </li>
                            {/if}

                            <hr>
                            <li class = "dropdown-submenu dropdown-submenu-search">
                                <input title = "Type at least 2 characters to search..." tabindex="-1" class="form-control corpora_search_bar" name="public_corpora_table" placeholder="Search" autocomplete="off" type="text">
                                <ul class="dropdown-menu dropdown-menu-search corpus_dropdown_menu">
                                </ul>
                            </li>
                            <br>



						</ul>
					</li>
                {/if}
                {if $corpus.id && ( "read"|has_corpus_role_or_owner || "admin"|has_role || $corpus.public ) }

                    <li class="navbar-sub dropdown nav_corpus_pages" style="background: #eee">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">Corpus page<span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li{if $page=="start"} class="active"{/if}><a href="index.php?page=start&amp;corpus={$corpus.id}">Start</a></li>
                            {if "admin"|has_role || "manager"|has_corpus_role_or_owner}
                                <li{if $page=="corpus"} class="active"{/if}><a href="index.php?page=corpus&amp;corpus={$corpus.id}">Settings</a></li>
                            {/if}
                            <li{if $page=="browse" || $page=="report"} class="active"{/if}><a href="index.php?page=browse&amp;corpus={$corpus.id}{if $report_id && $report_id>0}&amp;r={$report_id}{/if}">Documents</a></li>
                            {if "browse_annotations"|has_corpus_role_or_owner}
                                <li{if $page=="annmap"} class="active"{/if}><a href="index.php?page=annmap&amp;corpus={$corpus.id}">Annotations</a></li>
                                <li{if $page=="annotation_browser"} class="active"{/if}><a href="index.php?page=annotation_browser&amp;corpus={$corpus.id}">Annotation browser</a></li>
                                <li{if $page=="annotation_frequency"} class="active"{/if}><a href="index.php?page=annotation_frequency&amp;corpus={$corpus.id}">Annotation frequency</a></li>
                            {/if}
                            {if "browse_relations"|has_corpus_role_or_owner}
                                <li{if $page=="relations"} class="active"{/if}><a href="index.php?page=relations&amp;corpus={$corpus.id}">Relations</a></li>
                            {/if}
                            {if "run_tests"|has_corpus_role_or_owner}
                                <li{if $page=="tests"} class="active"{/if}><a href="index.php?page=tests&amp;corpus={$corpus.id}">Tests</a></li>
                            {/if}
                            <li{if $page=="stats"} class="active"{/if}><a href="index.php?page=stats&amp;corpus={$corpus.id}">Statistics</a></li>
                            {if "agreement_check"|has_corpus_role_or_owner}
                                <li{if $page=="agreement_check"} class="active"{/if}><a href="index.php?page=agreement_check&amp;corpus={$corpus.id}">Annotation agreement</a></li>
                            {/if}
                            {if "relation_agreement_check"|has_corpus_role_or_owner}
                                <li{if $page=="relation_agreement_check"} class="active"{/if}><a href="index.php?page=relation_agreement_check&amp;corpus={$corpus.id}">Relation agreement</a></li>
                            {/if}
                            {if "agreement_morpho"|has_corpus_role_or_owner}
								<li{if $page=="morpho_agreement_check"} class="active"{/if}><a href="index.php?page=morpho_agreement_check&amp;corpus={$corpus.id}">Morphological Agreement</a></li>
                            {/if}
                            {if $corpus.id == 3}
                                <li{if $page=="lps_authors"} class="active"{/if}><a href="index.php?page=lps_authors&amp;corpus={$corpus.id}">Authors of letters</a></li>
                                <li{if $page=="lps_stats"} class="active"{/if}><a href="index.php?page=lps_stats&amp;corpus={$corpus.id}">PCSN statistics</a></li>
                                <li{if $page=="lps_metric"} class="active"{/if}><a href="index.php?page=lps_metric&amp;corpus={$corpus.id}">PCSN metrics</a></li>
                            {/if}
                            <li{if $page=="word_frequency"} class="active"{/if}><a href="index.php?page=word_frequency&amp;corpus={$corpus.id}">Words frequency</a></li>
                            <li{if $page=="wccl_match"} class="active"{/if}><a href="index.php?page=wccl_match&amp;corpus={$corpus.id}">Wccl Match</a></li>
                            {if $corpus.id == 1}
                                {if !$RELEASE && $user && false}
                                    <li{if $page=="list_total"} class="active"{/if}><a href="index.php?page=list_total">Postęp</a></li>
                                    <li{if $page=="titles"} class="active"{/if}><a href="index.php?page=titles">Nagłówki</a></li>
                                {/if}
                                <li{if $page=="ontology"} class="active"{/if}><a href="index.php?page=ontology&amp;corpus={$corpus.id}">Ontology</a></li>
                            {/if}
                            {if "tasks"|has_corpus_role_or_owner}
                                <li{if $page=="tasks" or $page=="task"} class="active"{/if}><a href="index.php?page=tasks&amp;corpus={$corpus.id}">Tasks</a></li>
                            {/if}
                            {if "export"|has_corpus_role_or_owner}
                                <li{if $page=="export"} class="active"{/if}><a href="index.php?page=export&amp;corpus={$corpus.id}">Export</a></li>
                            {/if}
                            {if "add_documents"|has_corpus_role_or_owner || "admin"|has_role}
                                <li{if $page=="document_edit"} class="active"{/if}><a href="index.php?page=document_edit&amp;corpus={$corpus.id}">Add document</a></li>
                                <li{if $page=="upload"} class="active"{/if}><a href="index.php?page=upload&amp;corpus={$corpus.id}">Upload documents</a></li>
                            {/if}
                        </ul>
				    </li>
                {/if}
                <li{if $page=="public_annotations"} class="active"{/if}><a href="index.php?page=public_annotations">Annotations</a></li>
                <li{if $page=="ner"} class="active"{/if}><a href="index.php?page=ner">Liner2</a></li>
                <li{if $page=="ccl_viewer"} class="active"{/if}><a href="index.php?page=ccl_viewer">CCL Viewer</a></li>
                {if $config->wccl_match_enable}
					<li{if $page=="wccl_match_tester"} class="active"{/if}><a href="index.php?page=wccl_match_tester">Wccl Match Tester</a></li>
                {/if}
                {if "admin"|has_role}
					<li{if in_array($page, array("annotation_edit","relation_edit","event_edit","sense_edit","user_admin")) } class="active"{/if}>
						<a href="index.php?page=annotation_edit">Administration</a></li>
                {/if}
				<li{if $page=="about"} class="active"{/if}><a href="index.php?page=about">About & citing</a></li>
			</ul>
			<ul class="nav navbar-nav navbar-right" style="margin-right:50px;">
				{*<li><a href="#">About</a></li>*}
				{if $user}
				<li><a href="index.php?page=user_roles"><b>{$user.login} {if $user.screename}[{$user.screename}]{/if}</b></a></li>
				{/if}
				<li>
					{*if not using federation login show usual login buttons*}
					{if !($config->federationLoginUrl)}
						{if $user}
							 {*<a href="#" id="logout_link" style="color: red">Logout</a>*}
							<button href="#" id="logout_link" type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#loginForm">Logout</button>
						{else}
							{*<a href="#" id="login_link" style="color: green">login</a>*}
							<button href="#" type="button" class="btn btn-success btn-xs" data-toggle="modal" data-target="#loginForm">Login</button>
						{/if}
					{/if}
				</li>
				<li>
					<a id="compact-mode" href="#" title="Turn on/off a compact mode"><i class="fa fa-laptop" aria-hidden="true"></i></a>
				</li>
			</ul>
		</div>
	</nav>
</div>

<!-- Modal -->
<div class="modal fade" id="loginForm" role="dialog">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Login to Inforex</h4>
			</div>
			<div class="modal-body">
				<form>
					<div class="form-group">
						<label for="exampleInputLogin">Login</label>
						<input type="login" name="username" class="form-control" id="username" placeholder="Login">
					</div>
					<div class="form-group">
						<label for="exampleInputPassword1">Password</label>
						<input type="password" name="password" class="form-control" id="password" placeholder="Password">
					</div>
					<button type="submit" class="btn btn-primary">Login</button>
					<span style="color: red; margin-left: 70px" id="dialog-form-login-error"></span>
				</form>
			</div>
			<div class="modal-footer">
			</div>
		</div>

	</div>
</div>
	
    {if $page=="report"}
		<ul class="pager" style="padding: 0 20px">
			<li class="previous" style="" title="Number of reports before the current one."><span> ({$row_prev_c}) </span></li>
			<li class="previous">{if $row_first}<a href="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row_first}"><i class="fa fa-step-backward" aria-hidden="true"></i> First</a>{else}<span class="inactive"><i class="fa fa-step-backward" aria-hidden="true"></i> First</span>{/if}</li>
			<li class="previous">{if $row_prev_100}<a href="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row_prev_100}">-100</a>{else}<span class="inactive">-100</span>{/if}</li>
			<li class="previous">{if $row_prev_10}<a href="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row_prev_10}">-10</a> {else}<span class="inactive">-10</span>{/if}</li>
			<li class="previous">{if $row_prev}<a id="article_prev" href="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row_prev}"><i class="fa fa-arrow-left" aria-hidden="true"></i> Previous</a>{else}<span class="inactive"><i class="fa fa-arrow-left" aria-hidden="true"></i> Previous</span>{/if}</li>
			<li> <span style="color: black"><b>{$row_number}</b> z <b>{$row_prev_c+$row_next_c+1}</b>: <a href="#">{if $row.subcorpus_name}<b>{$row.subcorpus_name}</b> {/if} {if $row.title} &raquo; <b>{$row.title}</b>{/if}</a></span> </li>
			<li class="next"><span title"Liczba raportów znajdujących się po aktualnym raporcie">({$row_next_c})</span></li>
			<li class="next">{if $row_last}<a href="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row_last}"> Last <i class="fa fa-step-forward" aria-hidden="true"></i></a>{else}<span class="inactive">Last <i class="fa fa-step-forward" aria-hidden="true"></i></span>{/if}</li>
			<li class="next">{if $row_next_100}<a href="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row_next_100}">+100</a>{else}<span class="inactive">+100</span>{/if}</li>
			<li class="next">{if $row_next_10}<a href="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row_next_10}">+10</a> {else}<span class="inactive">+10</span>{/if}</li>
			<li class="next">{if $row_next}<a id="article_next" href="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row_next}">Next <i class="fa fa-arrow-right" aria-hidden="true"></i></a>{else}<span class="inactive">Next <i class="fa fa-arrow-right" aria-hidden="true"></i></span>{/if}</li>
		</ul>
    {/if}           
	
