<?php /* Smarty version 2.6.22, created on 2009-04-19 18:47:19
         compiled from /home/czuk/nlp/workspace/GPWKorpusWeb/engine/templates/report.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc_header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<script type="text/javascript">
jQuery(document).ready(function(){
	demoOnLoad( userid, 'gpw#r<?php echo $this->_tpl_vars['row']['id']; ?>
', serviceRoot, '' );
});
</script>


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
<div style="text-align: right">
	<?php if ($this->_tpl_vars['row_prev']): ?><a id="article_prev" href="index.php?page=report&amp;id=<?php echo $this->_tpl_vars['row_prev']; ?>
"><< poprzedni</a><?php else: ?>poprzedni<?php endif; ?>
	| <a href="html.php?id=<?php echo $this->_tpl_vars['row']['id']; ?>
">html</a> | 
	<?php if ($this->_tpl_vars['row_next']): ?><a id="article_next" href="index.php?page=report&amp;id=<?php echo $this->_tpl_vars['row_next']; ?>
">następny >></a><?php else: ?>następny<?php endif; ?>
</div>
<hr/>
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

	<?php if (( ( $this->_tpl_vars['row']['status'] == 2 && $this->_tpl_vars['row']['formated'] == 0 ) || $this->_tpl_vars['edit'] == 1 )): ?>
		<hr/>
		<form method="post" action="index.php?page=report&amp;id=<?php echo $this->_tpl_vars['row']['id']; ?>
">
		<div style="text-align: right">
			<input type="submit" value="Zapisz formatowanie" name="formatowanie" id="formating"/>
		</div>
		<textarea name="content" style="width: 100%; height: 400px;" wrap="on"><?php echo $this->_tpl_vars['content_formated']; ?>
</textarea>
		</form>
		<hr/>
	<?php endif; ?>

	<ol id="articles">
	<li id="r<?php echo $this->_tpl_vars['row']['id']; ?>
" class="hentry">
		<?php if ($this->_tpl_vars['row']['status'] == 2 && $this->_tpl_vars['row']['formated'] == 1 && $this->_tpl_vars['edit'] == 0): ?>
		<br/>	
		<!-- -->
		<div class="markers">
		</div>
		<?php endif; ?>
	 
		<div class="entry-content">
		<?php echo $this->_tpl_vars['row']['content']; ?>

		</div>
		
		<?php if ($this->_tpl_vars['row']['status'] == 2 && $this->_tpl_vars['row']['formated'] == 1 && $this->_tpl_vars['edit'] == 0): ?>
		<!-- The metadata class is something I added for styling.  It isn't used by 
		annotation. -->
		<p class="metadata">
			<!-- The entrylink is the URL for this entry.  It is the key used to look up 
			annotations for this page (see the wildcard match in showAllAnnotations above).  
			In Moodle, this is nothing like the URL passed to showAllAnnotations;  the 
			necessary logic to connect the two is in the AnnotationSummaryQuery class in 
			lib.php.  This stand-alone implementation is simpler.  Note that this URL 
			should always be complete, starting with the protocol (http://), so that users
			can follow the link to find the annotated resource (e.g. by clicking on a link
			in a summary list of annotations, or in the Atom feed emitted by the server).
			For security reasons, only http and https protocols are permitted.  The 
			fragment identifier (#m1) is used here because there is more than one 
			annotatable region on this page (also the case in Moodle). -->
			<a rel="bookmark" href="gpw#r<?php echo $this->_tpl_vars['row']['id']; ?>
">#</a>
		</p>
	
		<!-- There must be an element with a class of "notes", and that element must contain
		exactly one ol element.  The ol is the actual annotation margin.  For note positioning to
		work, it should be horizontally adjacent to the content area.  It doesn't matter how you
		achieve that, whether through a nice CSS layout or a nasty table one. -->
		<div class="notes">
			<!-- Without this button to call createAnnotation there would be no way to make new 
			annotations.  All other annotation controls are automatically added by the Javascript, 
			but this one is under the control of the application (you could call this function 
			from a pop-up menu, a button at the top of the page, whatever you want). -->
			<button class="createAnnotation" onclick="myClickCreateAnnotation(event,'r<?php echo $this->_tpl_vars['row']['id']; ?>
')" title="Click here to create an annotation">&gt;</button>
			<ol>
				<li></li>
			</ol>
		</div>
		<?php endif; ?>
	</li>
	</ol>
		
</div>

<hr style="clear: both" />
<small><a href="<?php echo $this->_tpl_vars['row']['link']; ?>
" target="_blank"><?php echo $this->_tpl_vars['row']['link']; ?>
</a></small>
<hr/>
Skróty:
<ul>
<li>k - poprzedni komunikat,</li>
<li>l - następny komunikat,</li>
<li>s - zapisz komunikat.</li>
</ul>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "inc_footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>