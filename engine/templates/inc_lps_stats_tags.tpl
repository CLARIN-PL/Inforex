{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
<h1>Statystyki znaczników struktury listów</h1>

<table class="tablesorter" cellspacing="1" style="width: 400px" id="tags_frequences">
  <thead>
    <tr>
      <th>Znacznik</th>
      <th>Liczba</th>
      <th>Dokumenty</th>
      <th>%&nbsp;dokumentów</th>
    </tr>
  </thead>
  <tbody>
  {foreach from=$tags item=row key=item}
    <tr>
        <th>{$item|escape|trim}</th>
        <td style="text-align: right">{$row.count}</td>
        <td style="text-align: right">{$row.docset|@count}</td>
        <td style="text-align: right">{$row.docper|string_format:"%.2f"}%</td>
    </tr>
{/foreach}
  </tbody>
</table>