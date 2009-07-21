<?php /* Smarty version 2.6.22, created on 2009-07-20 22:24:29
         compiled from inc_menu.tpl */ ?>
<td style="vertical-align: top; width: 100px; border: 1px solid #444; background: linen">
	<div id="menu_box">
	<h3>Menu:</h3>
	<ul>
		<li><a href="index.php?page=browse"<?php if ($this->_tpl_vars['page'] == 'browse'): ?> class="active"<?php endif; ?>>Raporty</a></li>
		<li><a href="index.php?page=list_total"<?php if ($this->_tpl_vars['page'] == 'list_total'): ?> class="active"<?php endif; ?>>Statystyki</a></li>
		<li><a href="index.php?page=backup"<?php if ($this->_tpl_vars['page'] == 'backup'): ?> class="active"<?php endif; ?>>SQL backup</a></li>
		<li><a href="index.php?page=titles"<?php if ($this->_tpl_vars['page'] == 'titles'): ?> class="active"<?php endif; ?>>Nagłówki</a></li>
		<!--<li><a href="">Statystyki</a></li>-->
	</ul>
	</div>
</td>