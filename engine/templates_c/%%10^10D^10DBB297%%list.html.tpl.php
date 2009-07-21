<?php /* Smarty version 2.6.22, created on 2009-03-02 13:21:55
         compiled from /home/czuk/nlp/workspace/GPWKorpusWeb/templates/list.html.tpl */ ?>
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
<a href="index.php?page=list&amp;page=<?php echo $this->_tpl_vars['page_prev']; ?>
"><< poprzenia</a> | <a href="index.php?page=list&amp;page=<?php echo $this->_tpl_vars['page_next']; ?>
">następna >></a>
<hr/>
<table>
	<tr>
		<th>#</th>
		<th>Firma</th>
		<th>Nazwa raportu</th>
		<th>Status</th>
	</tr>
<?php $_from = $this->_tpl_vars['rows']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['r']):
?>
	<tr>
		<td>#<?php echo $this->_tpl_vars['r']['id']; ?>
</td>
				<td><?php echo $this->_tpl_vars['r']['company']; ?>
</td>
		<td><a href="index.php?page=report&amp;id=<?php echo $this->_tpl_vars['r']['id']; ?>
"><?php echo $this->_tpl_vars['r']['title']; ?>
</a></td>
		<td></td>
	</tr>
<?php endforeach; endif; unset($_from); ?>
</table>

</body>
</html>