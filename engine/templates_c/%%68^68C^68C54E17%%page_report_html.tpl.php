<?php /* Smarty version 2.6.22, created on 2009-07-18 21:45:55
         compiled from /home/czuk/nlp/workspace/GPWKorpusWeb/engine/templates/page_report_html.tpl */ ?>
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
	<input type="submit" value="OK" name="zapisz" id="accept"/>
	<input type="hidden" value="<?php echo $this->_tpl_vars['row']['type']; ?>
" name="type"/> 
	<input type="hidden" value="2" name="status"/> 
</form>
<?php endif; ?>
 | <form method="get" action="index.php" style="display: inline">
 	<input type="hidden" name="page" value="report"/>
 	<input type="hidden" name="id" value="<?php echo $this->_tpl_vars['row']['id']; ?>
"/>
 	<input type="hidden" name="edit" value="1"/>
	<input type="submit" value="edytuj"/>
</form>

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

<div style="text-align: left">
	<?php if ($this->_tpl_vars['row_prev']): ?><a id="article_prev" href="index.php?page=report_html&amp;id=<?php echo $this->_tpl_vars['row_prev']; ?>
"><< poprzedni</a><?php else: ?>poprzedni<?php endif; ?>
	| <a href="html.php?id=<?php echo $this->_tpl_vars['row']['id']; ?>
">html</a> | 
	<?php if ($this->_tpl_vars['row_next']): ?><a id="article_next" href="index.php?page=report_html&amp;id=<?php echo $this->_tpl_vars['row_next']; ?>
">następny >></a><?php else: ?>następny<?php endif; ?>
</div>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc_report_menu_view.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>


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

	<code class="html" style="white-space: pre-wrap"><br/><?php echo $this->_tpl_vars['content']; ?>
</code>
		
</div>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc_footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>