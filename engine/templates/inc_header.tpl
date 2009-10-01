<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
	"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Language" content="en" />
	<meta name="GENERATOR" content="PHPEclipse 1.2.0" />
	<link rel="StyleSheet" href="css/styles.css" TYPE="text/css"/>
	<link rel="StyleSheet" href="css/menu_hor_1.css" TYPE="text/css"/>
	<title>Korpus GPW</title>
	
	<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
	<script src="js/jquery-1.3.2.min.js" type="text/javascript"></script>
	<script src="js/chili/jquery.chili-2.2.js" type="text/javaScript"></script>	
	<script src="js/jquery.a-tools-1.0.min.js" type="text/javascript"></script>
	<script src="js/jquery.autogrow.js" type="text/javascript"></script>
	<script type="text/javascript">
	    ChiliBook.recipeFolder = "js/chili/";
	</script>
	
	{if $page == 'report' }
	<script type="text/javascript" src="js/page_report.js"></script>
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
<table style="width: 100%; background: tan; ">
	<tr>