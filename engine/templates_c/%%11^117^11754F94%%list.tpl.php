<?php /* Smarty version 2.6.22, created on 2009-03-09 15:04:58
         compiled from /home/czuk/nlp/workspace/GPWKorpusWeb/templates/list.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', '/home/czuk/nlp/workspace/GPWKorpusWeb/templates/list.tpl', 43, false),)), $this); ?>
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
<h1><a href="index.php?page=list_total">Raporty</a> &raquo; <?php echo $this->_tpl_vars['year']; ?>
-<?php echo $this->_tpl_vars['month']; ?>
</h1>
<hr/>
<?php if (( $this->_tpl_vars['page_prev'] == -1 )): ?>
	<< poprzednia
<?php else: ?>
	<a href="index.php?page=list&amp;year=<?php echo $this->_tpl_vars['year']; ?>
&amp;month=<?php echo $this->_tpl_vars['month']; ?>
&amp;p=<?php echo $this->_tpl_vars['page_prev']; ?>
"><< poprzenia</a>
<?php endif; ?>
 | 
<?php if (( $this->_tpl_vars['page_next'] == -1 )): ?>
	następna >>
<?php else: ?>
	<a href="index.php?page=list&amp;year=<?php echo $this->_tpl_vars['year']; ?>
&amp;month=<?php echo $this->_tpl_vars['month']; ?>
&amp;p=<?php echo $this->_tpl_vars['page_next']; ?>
">następna >></a>
<?php endif; ?>
 
<hr/>
<table>
	<tr style="border: 1px solid #999;">
		<th>Lp.</th>
		<th>#</th>
		<th>Firma</th>
		<th>Nazwa raportu</th>
		<th>Typ raportu</th>
		<th>Status</th>
	</tr>
<?php $_from = $this->_tpl_vars['rows']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['list'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['list']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['r']):
        $this->_foreach['list']['iteration']++;
?>
	<tr style="background: <?php if (( ($this->_foreach['list']['iteration']-1)%2 == 0 )): ?>#eee<?php endif; ?>; color: <?php if (( $this->_tpl_vars['r']['status'] == 1 )): ?>#999<?php endif; ?>">
		<td style="text-align: right"><?php echo ($this->_foreach['list']['iteration']-1)+$this->_tpl_vars['from']; ?>
.</td>
		<td>#<?php echo $this->_tpl_vars['r']['id']; ?>
</td>
				<td><?php echo $this->_tpl_vars['r']['company']; ?>
</td>
		<td><a href="index.php?page=report&amp;id=<?php echo $this->_tpl_vars['r']['id']; ?>
"><?php echo $this->_tpl_vars['r']['title']; ?>
</a></td>
		<td style="<?php if ($this->_tpl_vars['r']['type'] == 1): ?>color: #777;<?php endif; ?>; text-align: center;"><?php echo ((is_array($_tmp=@$this->_tpl_vars['r']['type_name'])) ? $this->_run_mod_handler('default', true, $_tmp, "---") : smarty_modifier_default($_tmp, "---")); ?>
</td>
		<td style="<?php if ($this->_tpl_vars['r']['status'] == 1): ?>color: #777;<?php endif; ?>; text-align: center;"><?php echo ((is_array($_tmp=@$this->_tpl_vars['r']['status_name'])) ? $this->_run_mod_handler('default', true, $_tmp, "---") : smarty_modifier_default($_tmp, "---")); ?>
</td>
	</tr>
<?php endforeach; endif; unset($_from); ?>
</table>

</body>
</html>