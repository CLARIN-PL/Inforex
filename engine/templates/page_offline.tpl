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
	<title>Inforex &mdash; web-based text corpus management system (off-line)</title>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Language" content="en" />

	<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
	<script type="text/javascript" src="libs/bootstrap/dist/js/bootstrap.min.js"></script>
	<link rel="StyleSheet" href="libs/bootstrap/dist/css/bootstrap.min.css" TYPE="text/css"/>
	<link rel="stylesheet" href="css/font-awesome-4.7.0/css/font-awesome.min.css">
</head>
<body>
<div id="page" class="{$page}{if $compact_mode} compact{/if}">

	<div class="container">
		<div class="row">
			<div class="col-sm-12" style="text-align: center;">
				<div style="margin: 40px;">
					<a href="{$Config.url}"><img src="gfx/inforex_logo_small.jpg" style="margin: 4px" title="Inforex home page"/></a>
				</div>
				<div class="alert alert-info" style="font-size: 24px; margin: 40px;">
					Our website is currently down for maintenance.
				</div>
				<div style="background: url(screens/inforex_offline_splash.png); height: 500px; background-position: center top; background-repeat: no-repeat; padding-top: 400px">
					<i class="fa fa-hourglass-half fa-6" aria-hidden="true" style="font-size: 10em; color: #333; opacity: 0.5"></i>
					<div style="margin: 30px">We will be back soon.</div>
				</div>
			</div>

		</div>
	</div>

</div>
</body>
</html>

