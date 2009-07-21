<?php /* Smarty version 2.6.22, created on 2009-02-25 19:17:38
         compiled from /home/czuk/nlp/workspace/GPWKorpusWeb/templates/report.html.tpl */ ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
	"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Language" content="en" />
	<meta name="GENERATOR" content="PHPEclipse 1.2.0" />
	<link rel="StyleSheet" href="gfx/styles.css" TYPE="text/css"/>
	<title>Korpus GPW</title>
</head>
<body>
<?php echo $this->_tpl_vars['links']; ?>

<h2>#<?php echo $this->_tpl_vars['row']['id']; ?>
; <?php echo $this->_tpl_vars['row']['title']; ?>
</h2>
<h3><?php echo $this->_tpl_vars['row']['company']; ?>
</h3>
<?php echo $this->_tpl_vars['row']['content']; ?>

</body>
</html>