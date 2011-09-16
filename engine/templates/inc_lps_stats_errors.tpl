<h1>Statystyki błędów</h1>

Statystyki błędów opisanych przy pomocy znacznika <pre>&lt;corr resp="editor" type="..."></pre>

<table id="error_types" class="tablesorter" cellspacing="1" style="width: 200px; float: left; margin-right: 10px">
  <thead>
    <tr>
      <th rowspan="2">Tyb błędu</th>
      <th colspan="3">Liczba</th>
    </tr>
    <tr>
      <th title="wystąpień">wyst.</th>
      <th title="dokumentów">dok.</th>
      <th title="autorów">aut.</th>
    </tr>
  </thead>
  <tbody>
  {foreach from=$error_types item=v key=item}
    <tr class="row_{$item}">
        <td><a href="#" class="corr_type" title="{$item}">{$item}</a></td>
        <td style="text-align: right">{$v.count}</td>
        <td style="text-align: right">{$v.count_docs}</td>
        <td style="text-align: right">{$v.count_authors}</td>
    </tr>
  {/foreach}
  </tbody>
</table>    

<div style="margin-left: 210px">
    <table class="tablesorter" cellspacing="1" id="error_items">
      <thead>
        <tr>
          <th rowspan="2">Atrybuty type</th>
          <th rowspan="2">Atrybut sic</th>
          <th rowspan="2">Treść znacznika</th>
          <th colspan="2">Liczba</th>
          <th rowspan="2">Znacznik</th>
        </tr>
        <tr>
          <th title="wystąpień">wyst.</th>
          <th title="dokumentów">dok.</th>
        </tr>
      </thead>
      <tbody>
      {foreach from=$error_type_tags item=item name=errors}
        <tr>
            <td>type="<b>{$item.type}</b>"</td>
            <td>sic="<b>{$item.sic}</b>"</td>
            <td>{$item.content}</td>
            <td style="text-align: right">{$item.count}</td>
            <td style="text-align: right">{$item.count_docs}</td>
            <td>{$item.tag}</td>
        </tr>
        <tr id="row_{$smarty.foreach.errors.index}">
            <td colspan="6" style="background: #EEE">
            {foreach from=$item.docs item=doc key=key}
                <a href="index.php?page=report&amp;id={$key}">{$doc.name}</a> ({$doc.count}), 
            {/foreach}                  
            </td>
        </tr>
      {/foreach}
      </tbody>
    </table>
</div>

<br style="clear: both"/>
