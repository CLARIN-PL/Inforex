{include file="inc_header.tpl"}

<td class="table_cell_content">

<h1>Notatki</h1>

<ul>
	<li><b>Tryb szybkiego ustalania typu dokumentu.</b> Dla dokumentu, który nie ma określonego typu, wyświetlana jest lista (nie combo box, tylko np. lista linków) wszystkich typów dokumentów. Po wyboru jednej z pozycji następuje zmiana i przejście do następnego dokumentu.
	<li>Poszukać terminów <b>proper name categorization</b>, <b>named entity categorization</b>, <b>named entity hierarchy</b>.</li>
</ul>

<div class="ui-state-highlight">
<h2>Task 1</h2>
Oznaczyć następujące typy nazw własnych:
<ul>
	<li>miasta: <code>city</code>,</li>
	<li>państwa: <code>country</code>,</li>
	<li>firmy, zakłady itp.: <code>company</code>. Poprzez nazwę własną firmy rozumiem: pełną nazwę firmy, skróconą nazwę firmy, nazwę będącą akronimem, aliasy obowiązujące w ramach jednego dokumentu.</li>
</ul>

<h2>Struktura adnotacji</h2>
<ul>
	<li>firmy, zakłady, fundusze, spółki, itp.:
		<ul>
			<li>company</li>
			<li>company_alias &mdash; alias zastępczy identyfikujący firmę w obrębie dokumentu, będący pospolitym słowem np. Fundusz, Spółka, itd.</li> 
			<li>company_short &mdash; skrócona nazwa firmy nie będąca aliasem.</li>
		</ul>
	</li>
	<li><i>aliasy</li>
		<ul>
			<li>firmy</li>
			<li>dokumenty</li>
		</ul>
	</li>
</ul>

<h2>Zostawione na później:</h2>
<ul>
	<li><a href="http://localhost/gpw/index.php?page=report&id=162">http://localhost/gpw/index.php?page=report&id=162</a></li>
</ul>
</div>

<h1>Next <a href="http://localhost/gpw/index.php?page=report&subpage=annotator&id=399">http://localhost/gpw/index.php?page=report&subpage=annotator&id=399</a></h1>

<br style="clear: both"/>

</td>

{include file="inc_footer.tpl"}