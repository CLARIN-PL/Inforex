{include file="inc_header.tpl"}
<div style="margin: 0 autp">

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

<h2 style="padding: 5px; background: #88e; color: #ffe; margin: 0">Twoje pytanie</h2>

<div style="padding: 5px; background: #f0f0ff">
	<p style="margin: 5px">Wprowadź poniżej swoje pytanie i naciśij przycisk &raquo;Wyślij&laquo;.</p>
	
	<p style="margin: 5px">
		<input type="text" class="question" style="width: 600px; font-size: 16px; padding: 5px; font-weight: bold" value="Jakie miasta leżą w Polsce?"/>
		<input type="submit" class="buttonRun" style="padding: 5px" value="Wyślij"/>
	</p>
</div>

<br/>
<h2 style="padding: 5px; background: #555; color: #eee; margin: 0">Interpretacja <small>(<a href="#" class="show_hide_semql" style="color: white; text-decoration: underline">ukryj</a>)</small></h2>

<div class="semquel_results" style="padding: 5px; background: #eee; ">
<table class="tablesorter" style="margin: 5px;" cellspacing="1">
    <tr>
        <th style="width: 150px">Pytanie:</th>
        <td class="question_result"><b>Jakie miasta leżą w Polsce ?</b></td>
    </tr>
    <tr>
        <th>Pewność:</th>
        <td class="measure">0.77151674981</td>
    </tr>
    <tr>
        <th>Typ relacji:</th>
        <td class="relation_type">location</td>
    </tr>
    <tr>
        <th>Podtyp relacji:</th>
        <td class="relation_subtype">city_nam-country_nam</td>
    </tr>
    <tr>
        <th>Pytanie o typ obiektu:</th>
        <td class="type">city_nam</td>
    </tr>
    <tr>
        <th>Argument:</th>
        <td class="argument">arg_country_nam=Polsce</td>
    </tr>
    <tr>
        <th>Zapytanie SQL:</th>
        <td class="sql_code"><code style="white-space: pre">SELECT ans.text
FROM relations r
JOIN annotations ans ON r.annotation_source_id = ans.annotation_id
JOIN annotations ant ON r.annotation_target_id = ant.annotation_id
JOIN annotation_types ans_type ON ans.annotation_type_id = ans_type.annotation_type_id
JOIN annotation_types ant_type ON ans.annotation_type_id = ant_type.annotation_type_id
JOIN relation_types r_type ON r.relation_type_id = r_type.relation_type_id
WHERE ant.text = 'Polsce'
  AND ant_type.type = 'country_nam'
  AND ans_type.type = 'city_nam'
  AND r_type.type = 'location'
GROUP BY ans.text   </code></td>
    </tr>
</table>
</div>

<br/>
<h2 style="padding: 5px; background: #555; color: #eee; margin: 0">Wynik <small>(kliknij nazwę, aby zobaczyć szczegóły)</small></h2>

<div class="results_list" style="padding: 5px; background: #eee; font-size: 150%">
<a href="#"><b>Osiek</b></a> (3), 
<a href="#">Sokołówek</a> (2), 
<a href="#">Pawłówek</a> (2), 
<a href="#">Polany</a> (2), 
<a href="#">Probostwo Dolne</a> (2), 
<a href="#">Stara Wieś</a> (2), 
<a href="#">Sulejów</a> (2), 
<a href="#">Szóstka</a> (2), 
<a href="#">Ludwików</a> (2), 
<a href="#">Osówiec</a> (2), 
<a href="#">Mościska</a> (2), 
<a href="#">Borek</a> (2), 
<a href="#">Dzwonowo</a> (2), 
<a href="#">Michałów</a> (2), 
<a href="#">Rudawka</a> (2), 
<a href="#">Henrykowo</a> (2), 
<a href="#">Pogorzel</a> (2), 
<a href="#">Kamienna Góra</a> (2), 
<a href="#">Smardy Dolne</a> (1), 
<a href="#">Bełchatów</a> (1), 
<a href="#">Kanie - Stacja</a> (1), 
<a href="#">Wojnasy</a> (1), 
<a href="#">Kucerz</a> (1), 
<a href="#">Bobrowo</a> (1), 
<a href="#">Radom</a> (1), 
<a href="#">Łęgajny</a> (1), 
<a href="#">Podwierzbie</a> (1), 
<a href="#">Broncin</a> (1), 
<a href="#">Taraska</a> (1), 
<a href="#">Chrostkowo</a> (1) </div>

<br/>
<h2 class="result_element_title" style="padding: 5px; background: #999; color: #eee; margin: 0">Szczegóły dla <small>&raquo;Osiek&laquo;</small></h2>

<div class="result_element" style="padding: 5px; background: #eee">
    <ol>
        <li><b>Osiek</b> – wieś kociewska w <b>Polsce</b> położona w województwie pomorskim, w powiecie starogardzkim, w gminie Osiek.</li>
        <li><b>Osiek</b> – wieś kociewska w <b>Polsce</b> położona w województwie pomorskim, w powiecie starogardzkim, w gminie Osiek.</li>
        <li><b>Osiek</b> – wieś kociewska w <b>Polsce</b> położona w województwie pomorskim, w powiecie starogardzkim, w gminie Osiek.</li>
    </ol>        
</div>

</div>

<br/>
{include file="inc_footer.tpl"}