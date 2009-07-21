<?php /* Smarty version 2.6.22, created on 2009-07-20 22:25:26
         compiled from /home/czuk/nlp/workspace/GPWKorpusWeb/engine/templates/page_list_total.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'string_format', '/home/czuk/nlp/workspace/GPWKorpusWeb/engine/templates/page_list_total.tpl', 18, false),)), $this); ?>
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

<td class="table_cell_content">

<h1>Raporty</h1>
<hr/>
<table>
	<tr>
		<th>Rok</th>
		<th>Miesiąc</th>
		<th>Liczba raportów</th>
		<th colspan="3">Sprawdzone</th>
		<th colspan="3">Zaakceptowane</th>
	</tr>
<?php $_from = $this->_tpl_vars['rows']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['list'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['list']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['r']):
        $this->_foreach['list']['iteration']++;
?>
	<tr class="<?php if (( ($this->_foreach['list']['iteration']-1)%2 == 0 )): ?>even<?php else: ?>odd<?php endif; ?> month_<?php echo $this->_tpl_vars['r']['month']; ?>
">
		<?php $this->assign('progress', ((is_array($_tmp=$this->_tpl_vars['r']['s']/$this->_tpl_vars['r']['count']*100)) ? $this->_run_mod_handler('string_format', true, $_tmp, "%01.0f") : smarty_modifier_string_format($_tmp, "%01.0f"))); ?>
		<?php if (( $this->_tpl_vars['r']['sz'] == 0 )): ?><?php $this->assign('progress_f', 0); ?><?php else: ?><?php $this->assign('progress_f', ((is_array($_tmp=$this->_tpl_vars['r']['szf']/$this->_tpl_vars['r']['sz']*100)) ? $this->_run_mod_handler('string_format', true, $_tmp, "%01.0f") : smarty_modifier_string_format($_tmp, "%01.0f"))); ?><?php endif; ?>
		<td><?php echo $this->_tpl_vars['r']['year']; ?>
</td>
		<td style="text-align: right;"><?php echo $this->_tpl_vars['r']['month']; ?>
</td>
		<td style="text-align: right;"><?php echo $this->_tpl_vars['r']['count']; ?>
</td>
		<td style="text-align: right;"><?php echo $this->_tpl_vars['r']['s']; ?>
</td>
		<td style="text-align: right; <?php if (( $this->_tpl_vars['progress'] < 100 )): ?>color: red<?php endif; ?>"><?php echo ((is_array($_tmp=$this->_tpl_vars['r']['s']/$this->_tpl_vars['r']['count']*100)) ? $this->_run_mod_handler('string_format', true, $_tmp, "%01.2f") : smarty_modifier_string_format($_tmp, "%01.2f")); ?>
%</td>
		<td><div style="width: 100px; height: 10px; background: #B3C7FF">
				<div style="width: <?php echo $this->_tpl_vars['progress']; ?>
%; background: #3366FF; height: 10px"> </div>
			</div>
		</td>
		<td style="text-align: right"><?php echo $this->_tpl_vars['r']['sz']; ?>
</td>
		<td style="text-align: right"><?php echo $this->_tpl_vars['r']['szf']; ?>
</td>
		<td><div style="width: 100px; height: 10px; background: #fc8">
				<div style="width: <?php echo $this->_tpl_vars['progress_f']; ?>
%; background: orange; height: 10px"> </div>
			</div>
		</td>
		<td><a href="index.php?page=list&amp;year=<?php echo $this->_tpl_vars['r']['year']; ?>
&amp;month=<?php echo $this->_tpl_vars['r']['month']; ?>
">raporty >></a></td>
	</tr>
<?php endforeach; endif; unset($_from); ?>
</table>
</td>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc_footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>