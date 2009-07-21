<?php /* Smarty version 2.6.22, created on 2009-03-09 15:39:42
         compiled from /home/czuk/nlp/workspace/GPWKorpusWeb/templates/list_total.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'string_format', '/home/czuk/nlp/workspace/GPWKorpusWeb/templates/list_total.tpl', 28, false),)), $this); ?>
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
<h1>Raporty</h1>
<hr/>
<table>
	<tr>
		<th>Rok</th>
		<th>Miesiąc</th>
		<th>Liczba raportów</th>
		<th>T</th>
		<th></th>
	</tr>
<?php $_from = $this->_tpl_vars['rows']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['r']):
?>
	<tr>
		<td><?php echo $this->_tpl_vars['r']['year']; ?>
</td>
		<td style="text-align: right;"><?php echo $this->_tpl_vars['r']['month']; ?>
</td>
		<td style="text-align: right;"><?php echo $this->_tpl_vars['r']['count']; ?>
</td>
		<td style="text-align: right;"><?php echo $this->_tpl_vars['r']['t']; ?>
</td>
		<td style="text-align: right;"><?php echo ((is_array($_tmp=$this->_tpl_vars['r']['t']/$this->_tpl_vars['r']['count']*100)) ? $this->_run_mod_handler('string_format', true, $_tmp, "%01.2f") : smarty_modifier_string_format($_tmp, "%01.2f")); ?>
%</td>
		<td><a href="index.php?page=list&amp;year=<?php echo $this->_tpl_vars['r']['year']; ?>
&amp;month=<?php echo $this->_tpl_vars['r']['month']; ?>
">raporty >></a></td>
	</tr>
<?php endforeach; endif; unset($_from); ?>
</table>

</body>
</html>