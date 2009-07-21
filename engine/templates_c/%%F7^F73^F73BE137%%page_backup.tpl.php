<?php /* Smarty version 2.6.22, created on 2009-07-16 11:26:47
         compiled from /home/czuk/nlp/workspace/GPWKorpusWeb/engine/templates/page_backup.tpl */ ?>
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
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc_filter.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<h1>Backup</h1>

<?php if ($this->_tpl_vars['output']): ?>
<div class="info">Backup został wykonany</div>
<?php endif; ?>

<div>
	<ul>
	<?php $_from = $this->_tpl_vars['files']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['line']):
?>
	<li><a href="index.php?page=backup&amp;file=<?php echo $this->_tpl_vars['line']; ?>
"><?php echo $this->_tpl_vars['line']; ?>
</a></li>
	<?php endforeach; endif; unset($_from); ?>
	</ul>
	
	<form method="POST">
		<input type="submit" name="backup" value="wykonaj backup"/>
	</form>
</div>

<?php if ($this->_tpl_vars['display']): ?>
<hr/>
<h2><?php echo $this->_tpl_vars['file']; ?>
</h2>
<pre style="padding: 5px; border: 1px solid orange; margin: 5px; ">
	<?php echo $this->_tpl_vars['display_content']; ?>

</pre>
<?php endif; ?>

<br style="clear: both"/>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc_footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>