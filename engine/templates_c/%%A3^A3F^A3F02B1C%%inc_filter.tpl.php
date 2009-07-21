<?php /* Smarty version 2.6.22, created on 2009-07-16 11:01:27
         compiled from inc_filter.tpl */ ?>
<div id="filter_box">
Status raportu:
<?php $this->assign('count', 0); ?>
<ul>
<?php $_from = $this->_tpl_vars['statuses']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['s']):
?>
	<?php $this->assign('count', $this->_tpl_vars['count']+$this->_tpl_vars['s']['count']); ?>
	<li<?php if ($this->_tpl_vars['status'] == $this->_tpl_vars['s']['status_id']): ?> class="active"<?php endif; ?>><a href="index.php?action=status_set&amp;page=<?php echo $this->_tpl_vars['page']; ?>
&amp;status=<?php echo $this->_tpl_vars['s']['status_id']; ?>
"><?php echo $this->_tpl_vars['s']['status_name']; ?>
 (<?php echo $this->_tpl_vars['s']['count']; ?>
)</a></li>
<?php endforeach; endif; unset($_from); ?>
	<li<?php if (! $this->_tpl_vars['status']): ?> class="active"<?php endif; ?>><a href="index.php?action=status_set&amp;page=<?php echo $this->_tpl_vars['page']; ?>
">wszystkie (<?php echo $this->_tpl_vars['count']; ?>
)</a></li>
</ul>
</div>