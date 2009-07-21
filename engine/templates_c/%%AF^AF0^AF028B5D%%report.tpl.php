<?php /* Smarty version 2.6.22, created on 2009-03-09 15:51:59
         compiled from /home/czuk/nlp/workspace/GPWKorpusWeb/templates/report.tpl */ ?>
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
<div style="float: right; margin: 5px;">
<form method="post" action="index.php?page=report&amp;id=<?php echo $this->_tpl_vars['row']['id']; ?>
" style="display: inline">
Typ zdarzenia: <?php echo $this->_tpl_vars['select_type']; ?>
; Status: <?php echo $this->_tpl_vars['select_status']; ?>
 <input type="submit" value="zapisz" name="zapisz"/>
</form>
<?php if (( $this->_tpl_vars['row']['status'] == 1 )): ?>
 | <form method="post" action="index.php?page=report&amp;id=<?php echo $this->_tpl_vars['row']['id']; ?>
" style="display: inline">
	<input type="submit" value="OK" name="zapisz"/>
	<input type="hidden" value="<?php echo $this->_tpl_vars['row']['type']; ?>
" name="type"/> 
	<input type="hidden" value="2" name="status"/> 
</form>
<?php endif; ?>
</div>
<h1><a href="index.php?page=list_total">Raporty</a>
 &raquo; <a href="index.php?page=list&amp;year=<?php echo $this->_tpl_vars['year']; ?>
&amp;month=<?php echo $this->_tpl_vars['month']; ?>
"><?php echo $this->_tpl_vars['year']; ?>
-<?php echo $this->_tpl_vars['month']; ?>
</a>
 &raquo; <?php echo $this->_tpl_vars['row']['id']; ?>
</h1>
<hr/>
	<?php echo $this->_tpl_vars['links']; ?>

<hr/>
<?php if (( $this->_tpl_vars['row']['status'] == 1 )): ?>
<div style="float: right; width: 700px;">
	<iframe src ="index.php?page=raw&amp;id=<?php echo $this->_tpl_vars['row']['id']; ?>
" width="100%" height="450">
	  <p>Your browser does not support iframes.</p>
	</iframe>
</div>
<?php endif; ?>
<div>
	<h2><?php echo $this->_tpl_vars['row']['title']; ?>
</h2>
	<h3><?php echo $this->_tpl_vars['row']['company']; ?>
</h3>
	<div style="color: #4e5b22">
	<?php echo $this->_tpl_vars['row']['content']; ?>

	</div>
</div>
<hr/>
<small><a href="<?php echo $this->_tpl_vars['row']['link']; ?>
" target="_blank"><?php echo $this->_tpl_vars['row']['link']; ?>
</a></small>

</body>
</html>