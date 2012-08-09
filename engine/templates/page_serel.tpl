{include file="inc_header.tpl"}
<div style="margin: 0 auto">

	<div style="margin: 20px">
	    <div style="font-family: 'Chango', cursive; font-size: 40px; text-align: center">SEREL</div>
	    <div style="font-family: 'Shanti', sans-serif; font-size: 12px; text-align: center">Protyp systemu odpowiedzi na pytania <br/>o relacje semantyczne</div>
	</div>
	
	<div class="ui-state-highlight ui-corner-all" style="padding: 0pt 0.7em; margin: 10px; display: none"> 
	    <p style="padding: 10px"><span class="ui-icon ui-icon-info" style="float: left; margin-right: 0.3em;"></span>
	    Jest to prototyp systemu odpowiedzi na pytania o relacje semantyczne 8 kategorii zachodzące między obiektami reprezentowanymi przez nazwy własne.<br/><br/>
	    <b>Przykładowe pytania:</b>
	   <ul style="margin: 5px">
	      <li>Jakie miasta leżą w Polsce?</li>
	      <li>W jakich kraju znajduje się Nowy Jork?</li>
	   </ul>
	</p></div>
		
	<div style="background: #f0f0ff; padding: 10px;">
		<div style="margin: 5px; margin: 0 auto; width: 800px">
		    <p style="font-size: 16px; font-weight: bold; color: #999">Twoje pytanie:
			<input type="text" class="question" style="width: 600px; font-size: 16px; padding: 5px; font-weight: bold" value="Jakie miasta leżą w Polsce?"/>
			<input type="submit" class="buttonRun" style="padding: 5px" value="Wyślij"/>
		</div>
	</div>

    <div id="ajax-big" style="text-align: center; display: none;">
       <img src="gfx/ajax-big.gif" style="margin: 10px auto; "/>
    </div>
    
	
	<div style="display: none">
	    <h2 style="padding: 5px; background: #555; color: #eee; margin: 0;">
	        Wynik <small>(kliknij nazwę, aby zobaczyć szczegóły)</small>
	    </h2>
	
	    <div class="results_list" style="padding: 5px; background: #eee; font-size: 150%"></div>
	 </div>
	
	<div style="display: none">
	    <h2 class="result_element_title" style="padding: 5px; background: #999; color: #eee; margin: 0">Szczegóły dla <small>&raquo;Osiek&laquo;</small></h2>
	
		<div class="result_element" style="padding: 5px; background: #eee; display: none">
		    <ol>
		    </ol>        
		</div>
	</div>
	
	<div id="box-interpretation" style="display: none">
	   <div style="margin: 5px auto; width: 800px">
	    <h2 style="padding: 5px; background: #eee; color: #555; margin: 0">Interpretacja pytania <small>(<a href="#" class="show_hide_semql" style="color: navy; text-decoration: underline">pokaż/ukryj</a>)</small></h2>
		<div class="semquel_results" style="padding: 5px; background: #eee; ">
			<table class="tablesorter" style="margin: 5px; width: 750px" cellspacing="1">
			    <tr>
			        <th style="width: 140px">Pytanie:</th>
			        <td class="question_result"><b></b></td>
			    </tr>
			    <tr>
			        <th>Pewność:</th>
			        <td class="measure"></td>
			    </tr>
			    <tr>
			        <th>Typ relacji:</th>
			        <td class="relation_type"></td>
			    </tr>
			    <tr>
			        <th>Podtyp relacji:</th>
			        <td class="relation_subtype"></td>
			    </tr>
			    <tr>
			        <th>Pytanie o typ obiektu:</th>
			        <td class="type"></td>
			    </tr>
			    <tr>
			        <th>Argument:</th>
			        <td class="argument"></td>
			    </tr>
			    <tr>
			        <th>Zapytanie SQL:</th>
			        <td class="sql_code"><code style="white-space: pre; overflow: scroll"></code></td>
			    </tr>
			</table>
		</div>
		</div>
	</div>
</div>

<br/>
{include file="inc_footer.tpl"}