<h1>Korelacja błędów ze względu na autora</h1>

<div style="margin: 5px;">
Im wyższa wartość w komórce (ciemniejszy kolor tła) tym wyższa korelacja między wystąpieniem kategorii błędów u autora.
</div>

<table class="tablesorter" cellspacing="1" id="error_correlation">
    <thead>
        <tr>
            <th></th>
            {foreach from=$matrix_error_types item=x}
                <th style="text-align: center">{$x}</th>
            {/foreach}    
       </tr>
    </thead>
    <tbody>
{foreach from=$matrix_error_types item=x}
    <tr>
        <th>{$x}</th>
        {foreach from=$matrix_error_types item=y}
            {if $x == $y}
                <th style="text-align: center">{$matrix.$x.$y|string_format:"%.2f"}</th>
            {else}            
                <td style="text-align: center; background: rgb({math equation="250 - x * 250" x=$matrix.$x.$y format="%.0f"},250,250)">{$matrix.$x.$y|string_format:"%.2f"}</td>
            {/if}
        {/foreach}
    </tr>        
{/foreach}
    </tbody>
</table>