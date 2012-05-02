<h1>Interpunkcja</h1>

<table class="tablesorter" cellspacing="1" style="width: 200px">
  <thead>
    <tr>
        {foreach from=$interpunction_header item=h}
            <th>{$h}</th>
        {/foreach}
    </tr>
  </thead>
  <tbody>
  {foreach from=$interpunction item=arr key=item}
    <tr>
        <td>{$item|escape|trim}</td>
        {foreach from=$interpunction_header item=h key=kh}
            {if $h<>"Interpunkcja"}
            <td style="text-align: right">{$arr.$kh}</td>
            {/if}
        {/foreach}
    </tr>
{/foreach}
  </tbody>
</table>