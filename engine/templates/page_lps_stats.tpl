{include file="inc_header.tpl"}

<td class="table_cell_content">

<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Autorzy listów</a></li>
        <li><a href="#tabs-2">Znaczniki</a></li>
        <li><a href="#tabs-3">Błędy</a></li>
    </ul>
    <div id="tabs-1">
		<h1>Statystyka danych autorów listów</h1>
		<div>
		    Liczone po 
		    {if $count_by <> 'author' } <b>listach</b> {else} <a href="index.php?page=lps_stats&amp;corpus={$corpus.id}&amp;count_by=letter">listach</a>{/if}
		    /
		    {if $count_by == 'author' } <b>autorach</b> {else} <a href="index.php?page=lps_stats&amp;corpus={$corpus.id}&amp;count_by=author">autorach</a>{/if}
		</div>
		
		<table>
		    <tr>
		        <td style="vertical-align: top">
		            <h2>Wiek</h2>
		            <table cellspacing="1" class="tablesorter" >
		                <thead>
		                <tr>
		                    <th>Wartość</th>
		                    <th>Liczba</th>
		                </tr>
		                </thead>
		                <tbody>
		                    {foreach from=$age item=a}
		                    <tr>
		                        <th>{if $a.span_from}{$a.span_from} &ndash; {$a.span_to}{/if}</th>
		                        <td style="text-align: right">{$a.count}</td>
		                    </tr>
		                    {/foreach}
		                </tbody>
		            </table>
		        
		        </td>
		        <td style="vertical-align: top; padding: 0 80px;">
		            <h2>Płeć</h2>
		            <table cellspacing="1" class="tablesorter" >
		                <thead>
		                <tr>
		                    <th>Wartość</th>
		                    <th>Liczba</th>
		                </tr>
		                </thead>
		                <tbody>
		                {foreach from=$gender item=g}
		                    <tr>
		                        <th>{$g.deceased_gender}</th>
		                        <td style="text-align: right">{$g.count}</td>
		                    </tr>
		                {/foreach}
		                </tbody>
		            </table>
		        
		        </td>
		        <td style="vertical-align: top; padding-right: 80px">
		            <h2>Stan cywilny</h2>
		            <table cellspacing="1" class="tablesorter" >
		                <thead>
		                <tr>
		                    <th>Wartość</th>
		                    <th>Liczba</th>
		                </tr>
		                </thead>
		                <tbody>
		                    {foreach from=$maritial item=m}
		                    <tr>
		                        <th>{$m.deceased_maritial}</th>
		                        <td style="text-align: right">{$m.count}</td>
		                    </tr>
		                    {/foreach}
		                </tbody>
		            </table>
		        
		        </td>
		        <td style="vertical-align: top">
		            <h2>Rodzaj listu</h2>
		            <table cellspacing="1" class="tablesorter" >
		                <thead>
		                <tr>
		                    <th>Wartość</th>
		                    <th>Liczba</th>
		                </tr>
		                </thead>
		                <tbody>
		                    {foreach from=$source item=s}
		                    <tr>
		                        <th>{$s.source}</th>
		                        <td style="text-align: right">{$s.count}</td>
		                    </tr>
		                    {/foreach}
		                </tbody>
		            </table>
		        
		        </td>
		
		    </tr>
		</table>
		        
		<h1>Cechy parami</h1>
		
		<table>
		    <tr>
		        <td style="vertical-align: top">
		            <h2>Wiek/płeć</h2>
		            <table cellspacing="1" class="tablesorter" >
		                <thead>
		                <tr>
		                    <th>Wiek</th>
		                    <th>male</th>
		                    <th>female</th>
		                </tr>
		                </thead>
		                <tbody>
		                    {foreach from=$age_gender item=a}
		                    <tr>
		                        <th>{if $a.male.span_from}{$a.male.span_from} &ndash; {$a.male.span_to}{/if}</th>
		                        <td style="text-align: right">{$a.male.count|default:"-"}</td>
		                        <td style="text-align: right">{$a.female.count|default:"-"}</td>
		                    </tr>
		                    {/foreach}
		                </tbody>
		            </table>
		        
		        </td>
		        <td style="vertical-align: top; padding: 0 40px;">
		            <h2>Wiek/stan cywilny</h2>
		            <table cellspacing="1" class="tablesorter" >
		                <thead>
		                <tr>
		                    <th>Wiek</th>
		                    <th>cohabitant</th>
		                    <th>single</th>
		                </tr>
		                </thead>
		                <tbody>
		                    {foreach from=$age_maritial item=a}
		                    <tr>
		                        <th>{if $a.span_from}{$a.span_from} &ndash; {$a.span_to}{/if}</th>
		                        <td style="text-align: right">{$a.cohabitant.count|default:"-"}</td>
		                        <td style="text-align: right">{$a.single.count|default:"-"}</td>
		                    </tr>
		                    {/foreach}
		                </tbody>
		            </table>
		        
		        </td>
		        <td style="vertical-align: top">
		            <h2>Stan cywilny/płeć</h2>
		            <table cellspacing="1" class="tablesorter" >
		                <thead>
		                <tr>
		                    <th>Wiek</th>
		                    <th>male</th>
		                    <th>female</th>
		                </tr>
		                </thead>
		                <tbody>
		                    {foreach from=$maritial_gender item=m key=key}
		                    <tr>
		                        <th>{$key}</th>
		                        <td style="text-align: right">{$m.male.count|default:"-"}</td>
		                        <td style="text-align: right">{$m.female.count|default:"-"}</td>
		                    </tr>
		                    {/foreach}
		                </tbody>
		            </table>
		        
		        </td>
		
		    </tr>
		</table>
    </div>
    <div id="tabs-2">
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
    </div>
    <div id="tabs-3">
        <h1>Statystyki błędów</h1>
        
        Statystyki błędów opisanych przy pomocy znacznika <pre>&lt;corr resp="editor" type="..."></pre>
        
        <table id="error_types" class="tablesorter" cellspacing="1" style="width: 200px; float: left; margin-right: 10px">
          <thead>
            <tr>
              <th>Tyb błędu</th>
              <th>Liczba wystąpień</th>
            </tr>
          </thead>
          <tbody>
          {foreach from=$error_types item=count key=item}
            <tr class="row_{$item}">
                <td><a href="#" class="corr_type" title="{$item}">{$item}</a></td>
                <td style="text-align: right">{$count}</td>
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
	            		<a href="index.php?page=report&amp;id={$key}">{$key}</a> ({$doc}), 
	            	{/foreach}	            	
	            	</td>
	            </tr>
	          {/foreach}
  	          </tbody>
	        </table>
	    </div>
    
        <br style="clear: both"/>
    
    </div>
</div>


{include file="inc_footer.tpl"}