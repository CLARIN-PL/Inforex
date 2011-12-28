<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
	"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>Inforex &mdash; webowy system anotacji korpusów</title>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Language" content="en" />
	<meta name="GENERATOR" content="PHPEclipse 1.2.0" />
	
	<link rel="StyleSheet" href="css/styles.css" TYPE="text/css"/>
	<link rel="StyleSheet" href="css/annotations.css" TYPE="text/css"/>
	
	<style>
	{if $new_style}
		{$new_style}
	{/if}	
	</style>
	
	<link rel="StyleSheet" href="css/menu_hor_1.css" TYPE="text/css"/>
	<link rel="StyleSheet" href="css/custom-theme/jquery-ui-1.7.2.custom.css" TYPE="text/css"/>
	<link rel="StyleSheet" href="css/table-themes/blue/style.css" TYPE="text/css"/>
	<link rel="stylesheet" type="text/css" href="js/jquery/markitup/skins/markitup/style.css" />
	<link rel="stylesheet" type="text/css" href="js/jquery/markitup/sets/default/style.css" />

	<script type="text/javascript" src="js/logs.js"></script>
	
	<script src="js/jquery/jquery-1.4.4.min.js" type="text/javascript"></script>
	<script src="js/jquery/jquery-ui-1.7.2.custom.min.js" type="text/javascript"></script>
	<script src="js/jquery/chili/jquery.chili-2.2.js" type="text/javaScript"></script>	
	<script src="js/jquery/jquery.a-tools-1.0.min.js" type="text/javascript"></script>
	<script src="js/jquery/jquery.autogrow.js" type="text/javascript"></script>
	<script src="js/jquery/jquery.tablesorter.min.js" type="text/javascript"></script>
	<script src="js/jquery/jquery.meerkat.1.0.js" type="text/javascript"></script>
	<script src="js/jquery/jquery.fixonscroll.1.0.js" type="text/javascript"></script>
	<script src="js/jquery/jquery.cookie.js" type="text/javascript"></script>
	<script type="text/javascript">
	    ChiliBook.recipeFolder = "js/jquery/chili/";
	</script>
	<script type="text/javascript" src="js/jquery/markitup/jquery.markitup.js"></script>
	<script type="text/javascript" src="js/jquery/markitup/sets/default/set.js"></script>

	<script type="text/javascript" src="js/CodeMirror/js/codemirror.js"></script>
	<link rel="stylesheet" type="text/css" href="js/CodeMirror/css/codemirror.css" />

	<script type="text/javascript" src="js/jquery/flexigrid/flexigrid.pack.js"></script>
	<link rel="stylesheet" type="text/css" href="js/jquery/flexigrid/css/flexigrid/flexigrid.css" />

	<script type="text/javascript" src="js/DataTables/js/jquery.dataTables.js"></script>
	<link rel="stylesheet" type="text/css" href="js/DataTables/css/demo_table.css" />

	{* 
		Automatyczne ukrywanie/pokazywanie elementów strony przy pomocy linków oznaczonych klasą .toggle.
		Atrybut `label` określa id elementu do pokazania/ukrycia.
	*}
	<script type="text/javascript" src="js/auto.toggle.js"></script>
	<script type="text/javascript" src="js/core_regex.js"></script>
	<script type="text/javascript" src="js/core_status.js"></script>
	<script type="text/javascript" src="js/core_console.js"></script>
	<script type="text/javascript" src="js/core_dialogs.js"></script>
	<script type="text/javascript" src="js/core_login.js"></script>
	<script type="text/javascript" src="js/normalize_text.js"></script>
	<script type="text/javascript" src="js/lib_selected_text.js"></script>

	{if $page == 'report'}
		{if $subpage == 'annotator'}
			<script type="text/javascript" src="js/page_report_annotator.js"></script>
			<script type="text/javascript" src="js/page_report_annotator_resize.js"></script>
		{elseif $subpage == 'annotator_anaphora'}
			<script type="text/javascript" src="js/page_report_annotator_anaphora.js"></script>
			<script type="text/javascript" src="js/page_report_annotator_anaphora_resize.js"></script>
		{elseif $subpage == 'autoextension'}
			<script type="text/javascript" src="js/page_report_autoextension.js"></script>
			<script type="text/javascript" src="js/page_report_autoextension_resize.js"></script>
		{elseif $subpage == 'annotatorwsd' }
			<script type="text/javascript" src="js/page_report_annotator_wsd.js"></script>
			<script type="text/javascript" src="js/page_report_annotator_wsd_resize.js"></script>
		{elseif $subpage == 'takipi' }
			<script type="text/javascript" src="js/page_report_takipi.js"></script>
		{elseif $subpage == 'edit' }
			<script type="text/javascript" src="js/page_report_edit.js"></script>
            <script type="text/javascript" src="js/page_report_edit_resize.js"></script>
		{elseif $subpage == 'transcription' }
			<script type="text/javascript" src="js/page_report_transcription_resize.js"></script>
			<script type="text/javascript" src="js/page_report_transcription.js"></script>
		{elseif $subpage == 'topic' }
			<script type="text/javascript" src="js/page_report_topic.js"></script>
			<script type="text/javascript" src="js/page_report_topic_resize.js"></script>
		{elseif $subpage == 'preview' }
			<script type="text/javascript" src="js/page_report_preview_resize.js"></script>
		{elseif $subpage == 'tokenization' }
			<script type="text/javascript" src="js/page_report_tokenization.js"></script>
		{elseif $subpage == 'relation_statistic' }
			<script type="text/javascript" src="js/page_relations.js"></script>
		{/if}
	{/if}
    {if $page == 'browse' }
        <script type="text/javascript" src="js/page_browse_resize.js"></script>
    {elseif $page == 'ner' }
	   <script type="text/javascript" src="js/page_report_takipi.js"></script>
	   <script type="text/javascript" src="js/page_report_annotation_highlight.js"></script>
	   <script type="text/javascript" src="js/page_ner_resize.js"></script>
	{elseif $page == 'report' }
	   <script type="text/javascript" src="js/c_selection.js"></script>
	   <script type="text/javascript" src="js/c_annotation.js"></script>
	   <script type="text/javascript" src="js/c_widget_annotation.js"></script>
	   <script type="text/javascript" src="js/page_report_annotation_highlight.js"></script>
	{elseif $page == 'relations' }	   
		<script type="text/javascript" src="js/page_relations.js"></script>
	{/if}
	
	{if $page_js_file}
	<script type="text/javascript" src="{$page_js_file}"></script>
	{/if}
	
	<!--<link rel="StyleSheet" href="css/annotations_gpw.css" TYPE="text/css"/>-->
	<!--<link rel="StyleSheet" href="css/annotations_link.css" TYPE="text/css"/>-->
	
	{if $corpus.id == 3}
	<link rel="stylesheet" type="text/css" href="css/styles_lps.css" />
	<script type="text/javascript" src="js/jquery/jquery.iviewer-0.4.2/jquery.iviewer.js"></script>
	<link rel="stylesheet" type="text/css" href="js/jquery/jquery.iviewer-0.4.2/jquery.iviewer.css" />
	<script type="text/javascript" src="js/c_editor_transcription.js"></script>
	<script type="text/javascript" src="js/jquery/splitter/splitter.js"></script>
	{/if}
			
</head>
<body>
<div id="page">

	<div id="system_status" style="display: none">
		<img src="gfx/ajax-status.gif" style="vertical-align: baseline"/>
		<b>Status:</b> <span id="status_icon"></span> <span id="status_text">Tutaj będzie wyświetlany status.</span>	
	</div>

	{if $exception}
		<div id="fatal_error" style="text-align: left"><h2>Exception:</h2><pre>{$exception}</pre></div>
	{/if}
	
	<div id="logo">
		<a href=""><img src="gfx/inforex_small.png" style="margin: 4px"/></a>
	</div>

	<div style="float: right; margin-right: 10px">
		{if $user}
			Użytkownik: <a href="index.php?page=user_roles"><b>{$user.screename}</b></a><br/>
			Opcje: <a href="#" id="logout_link" style="color: red">wyloguj</a>
		{else}
			Opcje: <a href="#" id="login_link" style="color: green">zaloguj</a>		
		{/if}
	</div>
	
	{include file="inc_menu.tpl"}
	
	<div id="page_content">
	
	{if $error}
		<div style="padding: 0pt 0.7em; margin: 10px;" class="ui-state-highlight ui-corner-all"> 
			<p style="padding: 10px"><span style="float: left; margin-right: 0.3em;" class="ui-icon ui-icon-info"></span>
			<strong>Błąd</strong> {$error}</p>
		</div>
	{/if}
	
	{if $info}
		<div style="padding: 0pt 0.7em; margin: 10px;" class="ui-state-highlight ui-corner-all"> 
			<p style="padding: 10px"><span style="float: left; margin-right: 0.3em;" class="ui-icon ui-icon-info"></span>
			<strong>Info</strong> {$info}</p>
		</div>
	{/if}
	
	{if $exceptions}
		<div style="padding: 0pt 0.7em; margin: 10px;" class="ui-state-error ui-corner-all"> 
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
				
