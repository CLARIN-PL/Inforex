{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header.tpl"}

<td class="table_cell_content">

<h1>Ontologia</h1>

<div class="ui-widget ui-widget-content ui-corner-all fixonscroll" style="float: right; border: 1px solid #777; background: PeachPuff; padding: 2px;">
<b>Legenda</b>
<ul class="ontology">
	<li><i>kategoria semantyczna nie będąca anotacją</i></li>
	<li><em>kategoria semantyczna będąca anotacją</em></li>
	<li><code>:nazwa anotacji przypisanej do kategorii</code></li>
	<li><b>jednostka leksykalna występująca w tekście</b></li>
</ul>
</div>

<h2>Sklasyfikowane</h2>

<ul class="ontology">
	<li><i>Agent</i>
		<ul>
			<li><i>Indywidualny</i>
				<ul>
					<li><em>Osoba</em> :<code>one_person_pos</code> &mdash; jako nazwa własna osoby (imię, nazwisko, pseudonim)</li>
					<li><em>Stanowisko jednoosobowe</em> :<code>one_person_pos</code>
						<ul>
							<li><b>Przewodniczący</b> (Rady Nadzorczej)</li>
							<li><b>Sekretarz</b> (Rady Nadzorczej)</li>
							<li><b>Zarządca Komisaryczny</b></li>
							<li><b>Członek</b> (Rady Nadzorczej Spółki)</li>
						</ul>
					<li>
					<li><em>Zawód</em> &mdash; jako nazwa zawodu pisana z małej litery</li>
				</ul>
			</li>
			<li><i>Grupa</i>
				<ul>
					<li><em>Grupa ludzi w hierarchi organizacji</em> <code>:group_org</code>
						<ul>
							<li><b>Zarząd</b></li>
							<li><b>Rada Nadzorcza</b> (Spółki)</li>
						</ul>
					</li>
				</ul>			
			</li>			
		</ul>
	</li>
	<li><i>Zdarzenie</i>
		<ul>
			<li><i>spotkanie</i>
				<ul>
					<li>(Nadzwyczajne) <b>Walne Zgromadzenie</b> (Akcjonariuszy)</li>
				</ul>
			</li>		
		</ul>
	</li>
</ul>

<h2>Nie sklasyfikowane</h2>

<ul class="ontology">

	<li><i>dokument</i>
		<ul>
			<li><b>decyzja</b></li>
			<li><b>prospekt emisyjny</b></li>
			<li><b>raport</b></li>
			<li><b>raport bieżący</b></li>
			<li><b>raport kwartalny</b></li>
			<li><b>raport okresowy</b></li>
			<li><b>Stały Regulamin</b></li>
			<li><b>umowa</b></li>
		</ul>
	</li>
	<li><i>papiery wartościowe</i>
		<ul>
			<li><i>wierzycielskie</i>
				<ul>
					<li><b>weksel</b><li>
					<li><b>czek</b></li>
					<li><b>obligacja</b></li>
					<li><b>list zastawny</b></li>
					<li><b>świadectwo udziałowe NFI</b></li>
					<li><b>bon skarbowy</b></li>
					<li><b>obligacja skarbowa</b></li>
					<li><b>komunalny papier wartościowy</b></li>
					<li><b>papier wartościowy NBP</b></li>
					<li><b>bankowy papier wartościowy</b></li>
					<li><b>warrant subskrypcyjny</b></li>
				</ul>
			</li>
			<li><i>udziałowe (korporacyjne)</em></i>
				<ul>
					<li><b>akcja</b></li>
					<li><b>certyfikat inwestycyjny</b></li>
					<li><b>bon (korporacyjny)</b></li>
				</ul>
			</li>
			<li><i>towarowe</i>
				<ul>
					<li><b>konosament</b></li>
					<li><b>dowód składowy</b></li>
				</ul>
			</li>
		</ul>
	</li>
	<li><i>opcje papierów wartościowych</i>
		<ul>
			<li><b>put</b></li>
			<li><b>call</b></li>
		</ul>
	</li>
	<li><i>identyfikator, kod, oznaczenie</i>
		<ul>
			<li><b>ISIN</b><br/> <small>identyfikator #<b>papierów wartościowych</b></small></li>
			<li><b>seria</b><br/> <small>atrybut #<b>papierów wartościowych</b></small> 
				<ul>
					<li><b>A</b></li>
					<li>...</li>
					<li><b>Z</b></li>
				</ul>
			</li>
		</ul>
	</li>
	<li>
		<i>stawka</i> &mdash; pewna wartość reprezentowana przez liczbę lub oznaczenie
			<ul>
				<li>(stopa, stawka) <b>	WIBOR</b>
					<ul>
						<li><b>1SW</b></li>
						<li><b>1M</b>, <b>1-m</b></li>
						<li><b>3M</b></li>
						<li><b>6M</b></li>
						<li><b>9M</b></li>
						<li><b>12M</b></li>
					</ul>
				</li>
			</ul>
	</li>
	<li>
		<i>przywilej</i>
			<ul>
				<li><b>PDA</b> &mdash; Prawo Do Akcji</li>
			</ul>
	</li>
	<li>
		<i>organizacja</i></li>
		<ul>
			<li><i>podmiot gospodarczy</i>
				<ul>
					<li><em>spółka</em> <code>:company</code>
						<ul>
							<li><i>spółka akcyjna</i></li>
							<li><i>spółka z ograniczoną odpowiedzialnością</i></li>	
						</ul>
					</li>
				</ul>
			</li>
		</ul>
	</li>
	<li>
		<i>adres</i>
		<ul>
			<li>zawiera <em>kraj</em> <code>:country</code></li>
			<li>zawiera <em>miejscowość</em> <code>:city</code></li>
			<li>zawiera <i>położenie w miejscowości</i>
				<ul>
					<li><em>ulica</em> <code>:street</code></li>
					<li><i>plac</i></li>
					<li><i>aleja</i></li>
				</ul>
			</li>
		</ul>
	</li>
</ul>

<h2>Ontologia bazowa</h2>

<h4><a gref="http://www.cs.umd.edu/projects/plus/SHOE/onts/general1.0.html">http://www.cs.umd.edu/projects/plus/SHOE/onts/general1.0.html</a></h4>

<ul class="ontology">
   <li><span>[base.SHOEEntity]</span>
   		<ul>
      		<li><span>Address</span></li>
      		<li><span>Agent</span>
      			<ul>
         			<li><span>Person</span> (Organism)
            			<ul>
            				<li><span>Employee</span></li>
            			</ul>
            		</li> 
         			<li><span>SocialGroup</span>
         				<ul>
         					<li><span>Organization</span>
         						<ul>
         							<li><span>CommercialOrganization</span></li>
	       							<li><span>EducationOrganization</span></li>
	       							<li><span>GovernmentOrganization</span></li>
	       							<li><span>NonprofitOrganization</span></li>
	       						</ul>
	       					</li>
	       				</ul>
	       			</li>
	       			<li><span>ArtificialAgent</span></li>
         		</ul>
         	</li>
         	<li><span>Activity</span>
         		<ul>
         			<li><span>Work</span></li>
         			<li><span>Recreation</span></li>
         			<li><span>Process</span></li>
         		</ul>
         	</li>
         	<li><span>Event</span>
         	<li><span>Location</span>
         	<li><span>PhysicalObject</span>
         		<ul>
         			<li><span>Substance</span>
         				<ul>
         					<li><span>Liquid</span></li>
         					<li><span>Solid</span></li>
         				</ul>
         			</li>
         			<li><span>Thing</span>
         				<ul>
         					<li><span>Artifact</span></li>
         					<li><span>Organism</span></li>
         				</ul>
         			</li>
         		</ul>
         	</li>
         	<li><span>WebResource</span>
         		<ul>
         			<li><span>Audio</span></li>
         			<li><span>Image</span></li>
         			<li><span>Video</span></li>
         			<li><span>WebPage</span></li>
         		</ul>
         	</li>
         </ul>
      </li>
</ul>

</td>


{include file="inc_footer.tpl"}