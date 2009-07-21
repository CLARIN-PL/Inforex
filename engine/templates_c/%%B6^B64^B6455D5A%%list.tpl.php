<?php /* Smarty version 2.6.22, created on 2009-04-19 18:35:06
         compiled from /home/czuk/nlp/workspace/GPWKorpusWeb/engine/templates/list.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', '/home/czuk/nlp/workspace/GPWKorpusWeb/engine/templates/list.tpl', 29, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc_header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc_filter.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<h1><a href="index.php?page=list_total">Raporty</a> &raquo; <?php echo $this->_tpl_vars['year']; ?>
-<?php echo $this->_tpl_vars['month']; ?>
</h1>

<div id="pagging">
<?php unset($this->_sections['foo']);
$this->_sections['foo']['name'] = 'foo';
$this->_sections['foo']['loop'] = is_array($_loop=$this->_tpl_vars['pages']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['foo']['show'] = true;
$this->_sections['foo']['max'] = $this->_sections['foo']['loop'];
$this->_sections['foo']['step'] = 1;
$this->_sections['foo']['start'] = $this->_sections['foo']['step'] > 0 ? 0 : $this->_sections['foo']['loop']-1;
if ($this->_sections['foo']['show']) {
    $this->_sections['foo']['total'] = $this->_sections['foo']['loop'];
    if ($this->_sections['foo']['total'] == 0)
        $this->_sections['foo']['show'] = false;
} else
    $this->_sections['foo']['total'] = 0;
if ($this->_sections['foo']['show']):

            for ($this->_sections['foo']['index'] = $this->_sections['foo']['start'], $this->_sections['foo']['iteration'] = 1;
                 $this->_sections['foo']['iteration'] <= $this->_sections['foo']['total'];
                 $this->_sections['foo']['index'] += $this->_sections['foo']['step'], $this->_sections['foo']['iteration']++):
$this->_sections['foo']['rownum'] = $this->_sections['foo']['iteration'];
$this->_sections['foo']['index_prev'] = $this->_sections['foo']['index'] - $this->_sections['foo']['step'];
$this->_sections['foo']['index_next'] = $this->_sections['foo']['index'] + $this->_sections['foo']['step'];
$this->_sections['foo']['first']      = ($this->_sections['foo']['iteration'] == 1);
$this->_sections['foo']['last']       = ($this->_sections['foo']['iteration'] == $this->_sections['foo']['total']);
?>
    <a <?php if ($this->_tpl_vars['p'] == $this->_sections['foo']['iteration']-1): ?> class="active"<?php endif; ?>href="index.php?page=list&amp;year=<?php echo $this->_tpl_vars['year']; ?>
&amp;month=<?php echo $this->_tpl_vars['month']; ?>
&amp;p=<?php echo $this->_sections['foo']['iteration']-1; ?>
"><?php echo $this->_sections['foo']['iteration']; ?>
</a>
<?php endfor; endif; ?>
</div>

<table style="width: 100%">
	<tr style="border: 1px solid #999;">
		<th>Lp.</th>
		<th>#</th>
		<th>Firma</th>
		<th>Nazwa&nbsp;raportu</th>
		<th>Typ&nbsp;raportu</th>
		<th>Status</th>
		<th colspan="2"> </th>
	</tr>
<?php $_from = $this->_tpl_vars['rows']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['list'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['list']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['r']):
        $this->_foreach['list']['iteration']++;
?>
	<tr class="row_<?php if (( ($this->_foreach['list']['iteration']-1)%2 == 0 )): ?>even<?php else: ?>odd<?php endif; ?><?php if ($this->_tpl_vars['r']['formated'] == 1): ?>_formated<?php elseif ($this->_tpl_vars['r']['status'] == 2): ?>_ok<?php endif; ?>">
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
		<td><?php if ($this->_tpl_vars['r']['status'] == 2): ?><div style="width: 10px; height: 10px; background: #3366FF"> </div>
			<?php else: ?><div style="width: 10px; height: 10px; background: #ddd"> </div><?php endif; ?></td>
		<td><?php if ($this->_tpl_vars['r']['formated'] == 1): ?><div style="width: 10px; height: 10px; background: orange"> </div>
			<?php else: ?><div style="width: 10px; height: 10px; background: #ddd"> </div><?php endif; ?></td>
	</tr>
<?php endforeach; endif; unset($_from); ?>
</table>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc_footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>