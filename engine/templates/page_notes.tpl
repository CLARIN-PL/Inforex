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
</ul>

<h2>Zostawione na później:</h2>
<ul>
	<li><a href="http://localhost/gpw/index.php?page=report&id=162">http://localhost/gpw/index.php?page=report&id=162</a></li>
</ul>
</div>

<h1>Next <a href="http://localhost/gpw/index.php?page=report&subpage=annotator&id=399">http://localhost/gpw/index.php?page=report&subpage=annotator&id=399</a></h1>

<br style="clear: both"/>

<h1>Ontologia</h1>

<ul>
	<li><b>spotkanie</b>
		<ul>
			<li>Walne Zgromadzenie (Akcjonariuszy)</li>
			<li>Nadzwyczajne Walne Zgromadzenie (Akcjonariuszy)</li>
		</ul>
	</li>
	<li><b>funkcje jednoosobowe</b>
		<ul>
			<li>Przewodniczący (Rady Nadzorczej)</li>
			<li>Sekretarz (Rady Nadzorczej)</li>
			<li>Zarządca Komisaryczny</li>
			<li>Członek Rady Nadzorczej Spółki</li>
		</ul>
	<li>
	<li><b>grupa ludzi</b>
		<ul>
			<li>Zarząd</li>
			<li>Rada Nadzorcza (Spółki)</li>
		</ul>
	</li>
	<li><b>dokument</b>
		<ul>
			<li>prospekt emisyjny</li>
			<li>raport</li>
			<li>umowa</li>
		</ul>
	</li>
	<li><b>inne</b>
		<ul>
			<li>stawka WIBOR 3M</li>
		</ul>
	</li>
	<li><b>papiery wartościowe</b>
		<ul>
			<li><i>wierzycielskie</i>
				<ul>
					<li>weksel<li>
					<li>czek</li>
					<li>obligacja</li>
					<li>list zastawny</li>
					<li>świadectwo udziałowe NFI</li>
					<li>bon skarbowy</li>
					<li>obligacja skarbowa</li>
					<li>komunalny papier wartościowy</li>
					<li>papier wartościowy NBP</li>
					<li>bankowy papier wartościowy</li>
					<li>warrant subskrypcyjny</li>
				</ul>
			</li>
			<li><i>udziałowe (korporacyjne)</i>
				<ul>
					<li>akcja</li>
					<li>certyfikat inwestycyjny</li>
					<li>bon (korporacyjny)</li>
				</ul>
			</li>
			<li><i>towarowe</li>
				<ul>
					<li>konosament</li>
					<li>dowód składowy</li>
				</ul>
			</li>
		</ul>
	</li>
</ul>

</td>

{include file="inc_footer.tpl"}