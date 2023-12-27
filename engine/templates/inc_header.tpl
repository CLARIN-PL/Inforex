{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Inforex &mdash; web-based text corpus management system</title>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Language" content="en" />

	<link rel="StyleSheet" href="css/styles.css?20130903" TYPE="text/css"/>
	<link rel="StyleSheet" href="css/annotations.css?20130903" TYPE="text/css"/>

	<style>
	{if $new_style}
		{$new_style}
	{/if}
	</style>

{* Na stronie ner użyta jest bibliotek wymagająca nowszej wersji jquery *}
{if $page == "ner"}
	<script type="text/javascript" src="libs/jquery.1.11.min.js"></script>
	<link rel="StyleSheet" href="libs/jquery-ui-1.12.1.custom/jquery-ui.min.css" TYPE="text/css"/>
	<script src="libs/jquery-ui-1.12.1.custom/jquery-ui.min.js" type="text/javascript"></script>
{elseif $page == "about"}
	<script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
{else}
	<script src="js/jquery/jquery-1.4.4.min.js" type="text/javascript"></script>
	<link rel="StyleSheet" href="css/custom-theme/jquery-ui-1.7.2.custom.css?20130903" TYPE="text/css"/>
	<script src="js/jquery/jquery-ui-1.7.2.custom.min.js" type="text/javascript"></script>
	<script src="js/jquery/jquery.ba-resize.min.js" type="text/javascript"></script>
	<script src="js/jquery/chili/jquery.chili-2.2.js" type="text/javaScript"></script>
	<script src="js/jquery/jquery.a-tools-1.0.min.js" type="text/javascript"></script>
	<script src="js/jquery/jquery.autogrow.js" type="text/javascript"></script>
	<script src="js/jquery/jquery.ba-bbq.min.js" type="text/javascript"></script>
	<script src="js/jquery/jquery.tablesorter.min.js" type="text/javascript"></script>
	<script src="js/jquery/jquery.tablesorter.pager.min.js" type="text/javascript"></script>
	<script src="js/jquery/jquery.meerkat.1.0.js" type="text/javascript"></script>
	<script src="js/jquery/jquery.fixonscroll.1.0.js" type="text/javascript"></script>
	<script src="js/jquery/jquery.cookie.js" type="text/javascript"></script>
	<script src="js/jquery/jquery.tooltip.min.js" type="text/javascript"></script>
	<script src="js/external/checkboxlist.js" type="text/javascript"></script>

	<script type="text/javascript">
	    ChiliBook.recipeFolder = "js/jquery/chili/";
	</script>

	{* Automatyczne ukrywanie/pokazywanie elementów strony przy pomocy linków oznaczonych klasą .toggle.
		Atrybut `label` określa id elementu do pokazania/ukrycia. *}
	<script type="text/javascript" src="js/auto.toggle.js?20130903"></script>
{/if}

	<link rel="stylesheet" href="css/font-awesome-4.7.0/css/font-awesome.min.css">
	<link rel="StyleSheet" href="css/menu_hor_1.css?20130903" TYPE="text/css"/>
	<link rel="StyleSheet" href="css/table-themes/blue/style.css?20130903" TYPE="text/css"/>
	<link rel="stylesheet" type="text/css" href="js/jquery/markitup/skins/markitup/style.css?20130903" />
	<link rel="stylesheet" type="text/css" href="js/jquery/markitup/sets/default/style.css?20130903" />

    <link href='https://fonts.googleapis.com/css?family=Chango' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Shanti' rel='stylesheet' type='text/css'>
    <link href="https://fonts.googleapis.com/css?family=Slabo+27px" rel="stylesheet" type='text/css'>
    <!--<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">-->

	<script type="text/javascript" src="js/logs.js"></script>

	<script type="text/javascript" src="https://www.google.com/jsapi"></script>
	<script type="text/javascript" src="js/jquery/markitup/jquery.markitup.js"></script>
	<script type="text/javascript" src="js/jquery/markitup/sets/default/set.js"></script>
	<script type="text/javascript" src="js/jquery/purl/purl.js"></script>

{if $page==wccl_match_tester || $page==wccl_match}
	<script type="text/javascript" src="js/codemirror/codemirror.js"></script>
	<link rel="StyleSheet" href="js/codemirror/codemirror.css" TYPE="text/css"/>
	<script type="text/javascript" src="js/codemirror/mode/wccl.js"></script>
{else}
	<script type="text/javascript" src="js/CodeMirror/js/codemirror.js"></script>
	<link rel="stylesheet" type="text/css" href="js/CodeMirror/css/codemirror.css?20130903" />
{/if}

	<script type="text/javascript" src="js/jquery/flexigrid/flexigrid.js"></script>
	<link rel="stylesheet" type="text/css" href="js/jquery/flexigrid/css/flexigrid/flexigrid.css?20130903" />

	<script type="text/javascript" src="js/DataTables/js/jquery.dataTables.js"></script>
	<link rel="stylesheet" type="text/css" href="js/DataTables/css/demo_table.css?20130903" />

	<script type="text/javascript" src="js/core_ajax.js?20130903"></script>
	<script type="text/javascript" src="js/core_regex.js?20130903"></script>
	<script type="text/javascript" src="js/core_status.js?20130903"></script>
	<script type="text/javascript" src="js/core_console.js?20130903"></script>
	<script type="text/javascript" src="js/core_dialogs.js?20130903"></script>
	<script type="text/javascript" src="js/core_login.js?20130903"></script>
	<script type="text/javascript" src="js/normalize_text.js?20130903"></script>
	<script type="text/javascript" src="js/lib_selected_text.js?20130903"></script>

{if $page == 'report'}
	<script type="text/javascript" src="js/c_selection.js?20130903"></script>
	<script type="text/javascript" src="js/c_annotation.js?20130903"></script>
	<script type="text/javascript" src="js/c_widget_annotation.js?20130903"></script>
	<script type="text/javascript" src="js/page_report_annotation_highlight.js?20130903"></script>
	{if $subpage == 'annotator'}
	<script type="text/javascript" src="js/page_report_preview.js?20130903"></script>
	{elseif $subpage == 'agreement'}
	<script type="text/javascript" src="js/c_widget_annotation_type_tree.js"></script>
	<script type="text/javascript" src="js/c_widget_user_selection_a_b.js"></script>
	{elseif $subpage == 'annotation_lemma'}
	<script type="text/javascript" src="js/c_widget_annotation_type_tree.js"></script>
	{elseif $subpage == 'preview' }
	<script type="text/javascript" src="js/page_report_annotator_resize.js?20130903"></script>
	{elseif $subpage == 'relation_statistic' }
	<script type="text/javascript" src="js/page_relations.js?20130903"></script>
    {elseif $subpage == 'unassigned' }
	<script type="text/javascript" src="js/page_report_unassigned.js?20130903"></script>
    {elseif $subpage=="transcription"}
	<link rel="stylesheet" type="text/css" href="css/styles_lps.css?20130903?20130903" />
	<script type="text/javascript" src="js/jquery/jquery.iviewer-0.4.2/jquery.iviewer.js"></script>
	<link rel="stylesheet" type="text/css" href="js/jquery/jquery.iviewer-0.4.2/jquery.iviewer.css?20130903" />
	<script type="text/javascript" src="js/c_editor_transcription.js?20130903"></script>
	<script type="text/javascript" src="js/jquery/splitter/splitter.js"></script>
	{/if}
{elseif $page == 'browse' }
	<script type="text/javascript" src="js/page_browse_resize.js?20130903"></script>
{elseif $page == 'ner' }
	<link rel="stylesheet" href="libs/bootstrap/dist/css/bootstrap.min.css"/>
	<script type="text/javascript" src="libs/bootstrap/dist/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="libs/lobipanel/css/lobipanel.css"/>
	<script type="text/javascript" src="libs/lobipanel/js/lobipanel.js"></script>
{elseif $page == 'export' }
	<script type="text/javascript" src="js/c_widget_annotation_layers_and_subsets.js"></script>
	<link rel="stylesheet" type="text/css" href="css/c_widget_annotation_layers_and_subsets.css" />
{elseif $page == 'ccl_viewer'}
	<script type="text/javascript" src="js/page_report_preview.js?20130903"></script>
	<script type="text/javascript" src="js/page_report_annotator_resize.js?20130903"></script>
{elseif $page == 'agreement_check'}
	<script type="text/javascript" src="js/c_widget_annotation_type_tree.js"></script>
{elseif $page == 'relation_agreement_check'}
    <script type="text/javascript" src="js/c_widget_annotation_type_tree.js"></script>
{/if}

	{foreach from=$include_files item=f}
		{if $f.type == "js"}<script type="text/javascript" src="{$f.file}"></script>{*
		*}{else if $f.type == "css"}<link rel="stylesheet" type="text/css" href="{$f.file}" />{/if}

	{/foreach}
</head>
<body>
<div id="page" class="{$page}">

	<div id="system_status" style="display: none">
		<img src="gfx/ajax-status.gif" style="vertical-align: baseline"/>
		<b>Status:</b> <span id="status_icon"></span> <span id="status_text">Tutaj będzie wyświetlany status.</span>
	</div>

	{if $exception}
		<div id="fatal_error" style="text-align: left"><h2>Exception:</h2><pre>{$exception}</pre></div>
	{/if}

	<div id="logo">
		<a href="{Config::Config()->get_url()}"><img src="gfx/inforex_logo_small.jpg" style="margin: 4px" title="Inforex home page"/></a>
	</div>

	<div style="float: right; margin-right: 10px; line-height: 30px;">
		{if $user}
			User: <a href="index.php?page=user_roles"><b>{$user.login} {if $user.screename}[{$user.screename}]{/if}</b></a> (<a href="#" id="logout_link" style="color: red">logout</a>)
		{else}
			User: <a href="#" id="login_link" style="color: green">login</a>
		{/if}
	</div>

	{include file="inc_menu.tpl"}

    <div id="page_content">

	{if $error}
		<div style="padding: 0pt 0.7em; margin: 10px;" class="ui-state-highlight ui-corner-all">
			<p style="padding: 10px"><span style="float: left; margin-right: 0.3em;" class="ui-icon ui-icon-info"></span>
			<strong>Error</strong> {$error}</p>
		</div>
	{/if}

	{if $info}
		<div style="padding: 0pt 0.7em; margin: 10px;" class="ui-state-highlight ui-corner-all">
			<p style="padding: 10px"><span style="float: left; margin-right: 0.3em;" class="ui-icon ui-icon-info"></span>
			<strong>Info</strong> {$info}</p>
		</div>
	{/if}

	{if $exceptions}
		<div id="exceptions" style="padding: 0pt 0.7em; margin: 5px; max-height: 150px; overflow: auto" class="ui-state-error ui-corner-all">
			<p style="padding: 10px"><span style="float: left; margin-right: 0.3em;" class="ui-icon ui-icon-info"></span>
			<strong>Exception</strong>
			<ul style="margin: 0px; padding: 0px; padding-left: 30px;">
			{foreach from=$exceptions item=ex}
				<li>{$ex}</li>
			{/foreach}
			</ul>
			</p>
		</div>
	{/if}
