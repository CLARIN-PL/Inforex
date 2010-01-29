<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
	"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Language" content="en" />
	<meta name="GENERATOR" content="PHPEclipse 1.2.0" />
	<link rel="StyleSheet" href="css/styles.css" TYPE="text/css"/>
	<link rel="StyleSheet" href="css/menu_hor_1.css" TYPE="text/css"/>
	<link rel="StyleSheet" href="css/custom-theme/jquery-ui-1.7.2.custom.css" TYPE="text/css"/>
	<link rel="StyleSheet" href="css/table-themes/blue/style.css" TYPE="text/css"/>
	<title>Korpus GPW</title>
	
	<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
	<script src="js/jquery-1.3.2.min.js" type="text/javascript"></script>
	<script src="js/jquery-ui-1.7.2.custom.min.js" type="text/javascript"></script>
	<script src="js/chili/jquery.chili-2.2.js" type="text/javaScript"></script>	
	<script src="js/jquery.a-tools-1.0.min.js" type="text/javascript"></script>
	<script src="js/jquery.autogrow.js" type="text/javascript"></script>
	<script src="js/jquery.tablesorter.min.js" type="text/javascript"></script>
	<script src="js/jquery.meerkat.1.0.js" type="text/javascript"></script>
	<script src="js/jquery.fixonscroll.1.0.js" type="text/javascript"></script>
	<script type="text/javascript">
	    ChiliBook.recipeFolder = "js/chili/";
	</script>

	{* 
		Automatyczne ukrywanie/pokazywanie elementów strony przy pomocy linków oznaczonych klasą .toggle.
		Atrybut `label` określa id elementu do pokazania/ukrycia.
	*}
	<script type="text/javascript" src="js/auto.toggle.js"></script>
	<script type="text/javascript" src="js/regex.js"></script>
	
	{if $page == 'report' && $subpage == 'annotator' }
	<script type="text/javascript" src="js/page_report_annotator.js"></script>
	{/if}
	{if $page == 'report' && $subpage == 'takipi' }
	<script type="text/javascript" src="js/page_report_takipi.js"></script>
	{/if}
	{if $page == 'report' }
	<script type="text/javascript" src="js/c_selection.js"></script>
	<script type="text/javascript" src="js/c_annotation.js"></script>
	<script type="text/javascript" src="js/c_widget_annotation.js"></script>
	<script type="text/javascript" src="js/page_report_annotation_highlight.js"></script>
	{/if}
	
	{if $marginalia_js}
		{foreach from=$marginalia_js item=js}
			<script type='text/javascript' src='marginalia/{$js}'></script>
		{/foreach}
		
		<!-- These are implementations of how to fetch annotations, set preferences, and
		of localized strings.  They will likely be different on every system. -->
		<script type="text/javascript" src="marginalia-custom/marginalia-strings.js"></script>
		<script type="text/javascript" src="marginalia-custom/static-annotate.js"></script>
		<script type="text/javascript" src="marginalia-custom/static-prefs.js"></script>

		<!-- Custom Javascript to set up Marginalia.  See here for essential code: -->
		<script type="text/javascript" src="marginalia-custom/index.js"></script>
		<script type="text/javascript" src="marginalia-custom/onload.js"></script>

		<link rel="StyleSheet" href="marginalia-custom/index.css" TYPE="text/css"/>
		<link rel="StyleSheet" href="marginalia/marginalia.css" TYPE="text/css"/>
	{/if}
		
</head>
<body>
{if !$cookie}<div id="fatal_error"><h2>Włącz ciasteczka</h2>Do poprawnego działania skryptu wymagane są aktywne ciasteczka (COOKIES).</div>{/if}
<div style="float: right; margin-top: 2px"><img src="gfx/inforex_small.png"/></div>
{include file="inc_menu.tpl"}
<table style="width: 100%; background: tan; ">
	<tr>