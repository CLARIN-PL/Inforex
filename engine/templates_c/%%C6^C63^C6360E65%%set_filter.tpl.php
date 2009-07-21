<?php /* Smarty version 2.6.22, created on 2009-04-18 16:17:40
         compiled from /home/czuk/nlp/workspace/GPWKorpusWeb/engine/templates/set_filter.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc_header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<h1>Ustawienia filtrowania</h1>
<hr/>
<form method="post" action=".">
<h2>Status raportu</h2>
<ul>
<?php $_from = $this->_tpl_vars['statuses']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['status']):
?>
<li><input type="checkbox" name="statuses[]" value="<?php echo $this->_tpl_vars['status']['id']; ?>
" style="vertical-align: middle"/> <?php echo $this->_tpl_vars['status']['status']; ?>
<br/><span><?php echo $this->_tpl_vars['status']['description']; ?>
</span></li>
<?php endforeach; endif; unset($_from); ?>
</ul>

<h2>Rodzaj raportu</h2>
<ul>
<?php $_from = $this->_tpl_vars['types']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['type']):
?>
<li><input type="checkbox" name="types[]" value="<?php echo $this->_tpl_vars['type']['id']; ?>
" style="vertical-align: middle"/> <?php echo $this->_tpl_vars['type']['name']; ?>
</li>
<?php endforeach; endif; unset($_from); ?>
</ul>
<input type="submit" value="Zapisz" />
</form>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc_footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>