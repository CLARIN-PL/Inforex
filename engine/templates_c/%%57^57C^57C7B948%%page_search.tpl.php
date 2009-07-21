<?php /* Smarty version 2.6.22, created on 2009-07-16 11:23:44
         compiled from /home/czuk/nlp/workspace/GPWKorpusWeb/engine/templates/page_search.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'count', '/home/czuk/nlp/workspace/GPWKorpusWeb/engine/templates/page_search.tpl', 10, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc_header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc_menu.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<form method="post" action="index.php?page=search">
	Fraza: <input type="text" name="phrase"/>
	<input type="submit" value="Szukaj" />
</form>

<?php if ($this->_tpl_vars['phrase']): ?>
<div>Wynik wyszukiwania dla frazy <b><?php echo $this->_tpl_vars['phrase']; ?>
</b>; liczba dokumentów: <b><?php echo count($this->_tpl_vars['reports']); ?>
</b></div>
<?php $_from = $this->_tpl_vars['reports']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['report']):
?>
<h2><?php echo $this->_tpl_vars['report']['title']; ?>
</h2>
<div style="margin: 5px;"><?php echo $this->_tpl_vars['report']['content']; ?>
</div>
<hr/>
<?php endforeach; endif; unset($_from); ?>
<?php endif; ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc_footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>