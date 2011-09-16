<h1>Statystyki znaczników</h1>

<table class="tablesorter" cellspacing="1" style="width: 200px">
  <thead>
    <tr>
      <th>Znacznik</th>
      <th>Liczba</th>
    </tr>
  </thead>
  <tbody>
  {foreach from=$tags item=count key=item}
    <tr>
        <td>{$item|escape|trim}</td>
        <td style="text-align: right">{$count}</td>
    </tr>
{/foreach}
  </tbody>
</table>