<h1>Macierz współwystępowania błędów u autorów</h1>

<table class="tablesorter" cellspacing="1" >
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
                <th style="text-align: center">{$matrix.$x.$y}</th>
            {else}
                <td style="text-align: center">{$matrix.$x.$y}</td>
            {/if}
        {/foreach}
    </tr>        
{/foreach}
    </tbody>
</table>