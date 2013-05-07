{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header.tpl"}

<h1>Statystyki autorów listów</h1>

<div style="float: left; width: 400px;">
{include file="inc_document_filter.tpl"}
</div>

<div style="margin-left: 420px">
	
	<h2 class="middle">Pojedyncze cechy</h2>
	
	<table>
	    <tr>
	        <td style="vertical-align: top">
	            <h3>Wiek</h3>
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
	            <h3>Płeć</h3>
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
	            <h3>Stan cywilny</h3>
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
	            <h3>Rodzaj listu</h3>
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
	        
	<h2 class="middle">Cechy parami</h2>
	
	<table>
	    <tr>
	        <td style="vertical-align: top">
	            <h3>Wiek/płeć</h3>
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
	            <h3>Wiek/stan cywilny</h3>
	            <table cellspacing="1" class="tablesorter" >
	                <thead>
	                <tr>
	                    <th>Wiek</th>
	                    <th></th>
	                    <th>cohabitant</th>
	                    <th>married</th>
	                    <th>separeted</th>
	                    <th>single</th>
	                    <th>widowed</th>
	                </tr>
	                </thead>
	                <tbody>
	                    {foreach from=$age_maritial item=a}
	                    <tr>
	                        <th>{if $a.span_from}{$a.span_from} &ndash; {$a.span_to}{/if}</th>
                            <td style="text-align: right">{$a.none.count|default:"-"}</td>
	                        <td style="text-align: right">{$a.cohabitant.count|default:"-"}</td>
	                        <td style="text-align: right">{$a.married.count|default:"-"}</td>
	                        <td style="text-align: right">{$a.separeted.count|default:"-"}</td>
	                        <td style="text-align: right">{$a.single.count|default:"-"}</td>
	                        <td style="text-align: right">{$a.widowed.count|default:"-"}</td>
	                        
	                    </tr>
	                    {/foreach}
	                </tbody>
	            </table>
	        
	        </td>
	        <td style="vertical-align: top">
	            <h3>Stan cywilny/płeć</h3>
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
{include file="inc_footer.tpl"}