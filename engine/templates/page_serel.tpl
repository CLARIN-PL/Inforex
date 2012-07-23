{include file="inc_header.tpl"}
<div style="margin: 0 autp">

<h1>Prototyp systemu odpowiedzi na pytania</h1>

<div class="ui-state-highlight ui-corner-all" style="padding: 0pt 0.7em; margin: 10px;"> 
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
		<input type="text" style="width: 600px; font-size: 16px; padding: 5px; font-weight: bold" value="Jakie miasta leżą w Polsce?"/>
		<input type="submit" style="padding: 5px" value="Wyślij"/>
	</p>
</div>

<br/>
<h2 style="padding: 5px; background: #555; color: #eee; margin: 0">Interpretacja</h2>

<div style="padding: 5px; background: #eee">
<table class="tablesorter" style="margin: 5px;" cellspacing="1">
    <tr>
        <th style="width: 150px">Pytanie:</th>
        <td><b>Jakie miasta leżą w Polsce ?</b></td>
    </tr>
    <tr>
        <th>Pewność:</th>
        <td>0.77151674981</td>
    </tr>
    <tr>
        <th>Typ relacji:</th>
        <td>location</td>
    </tr>
    <tr>
        <th>Podtyp relacji:</th>
        <td>city_nam-country_nam</td>
    </tr>
    <tr>
        <th>Pytanie o typ obiektu:</th>
        <td>city_nam</td>
    </tr>
    <tr>
        <th>Argument:</th>
        <td>arg_country_nam=Polsce</td>
    </tr>
    <tr>
        <th>Zapytanie SQL:</th>
        <td><code style="white-space: pre">SELECT ans.text
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

<div style="padding: 5px; background: #eee; font-size: 150%">
    <a href="#" style="font-weight: bold">Osiek</a>, <a href="#">Sokołówek</a>, <a href="#">Pawłówek</a>, <a href="#">Polany</a>
</div>

<br/>
<h2 style="padding: 5px; background: #999; color: #eee; margin: 0">Szczegóły dla &raquo;Osiek&laquo;</small></h2>

<div style="padding: 5px; background: #eee">
    <b>Osiek</b> – wieś kociewska w <b>Polsce</b> położona w województwie pomorskim, w powiecie starogardzkim, w gminie Osiek.
</div>

</div>

<br/>
{include file="inc_footer.tpl"}
