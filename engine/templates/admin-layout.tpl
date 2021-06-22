<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <!-- Required meta tags -->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="Content-Language" content="en"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <link rel="stylesheet" href="vendors/metro4/css/metro-all.min.css">
    <link rel="stylesheet" href="css/index.css">

    <title>Inforex &mdash; web-based text corpus management system</title>
    {block name=head}{/block}
</head>
<body class="m4-cloak h-vh-100">
<div data-role="navview" data-toggle="#paneToggle" data-expand="xl" data-compact="lg" data-active-state="true">
    {include file="admin-sidemenu.tpl"}
    <div class="navview-content h-100">
        {include file="admin-topmenu.tpl"}
        {if "admin"|has_role}
            <div id="content-wrapper" class="content-inner h-100" style="overflow-y: auto">
                {include file="inc_system_messages.tpl"}
                {block name=body}{/block}
            </div>
        {/if}
        {include file="inc_error_modal2.tpl"}
    </div>
</div>

<script src="vendors/jquery/jquery-3.4.1.min.js"></script>
<script src="vendors/jquery/jquery.validate.min.js"></script>
<script src="vendors/metro4/js/metro.min.js"></script>
<script src="js/core_ajax.js"></script>
<script src="js/error_modal2.js"></script>
{block name=scripts}{/block}
</body>
</html>