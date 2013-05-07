{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
<h1>Statystyki błędów</h1>

<div>Znaczniki: <em>&lt;corr resp="author"></em> dla typu błędu "<em>author</em>" i <em>&lt;corr resp="editor" type="..."></em> dla pozostałych</li>.</div>

<div style="width: 250px; float: left; margin-right: 10px">
	<h2>Lista błędów</h2>
	<table id="error_types" class="tablesorter" cellspacing="1">
	  <thead>
	    <tr>
	      <th>Tyb błędu</th>
	      <th title="Liczba wystąpień">Wyst.</th>
	      <th title="Liczba dokumentów">Dok.</th>
	      <th title="Liczba autorów">Aut.</th>
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
</div>    

<div style="float: right; width: 130px">
    <h2>Dokumenty</h2>
    <table class="tablesorter" cellspacing="1" id="documents_with_errors">
        <thead>        
	        <tr>
	           <th title="Liczba dokumentów">Dok.</th>
	           <th title="Liczba wystąpień">Wyst.</th>
	        </tr>
	    </thead>
	    <tbody>
	       
	    </tbody> 
    </table>
</div>

<div style="margin-left: 260px; margin-right: 140px;">
    <h2>Statystyki atrybutów dla wybranej kategorii błędu</h2>
    <table class="tablesorter" cellspacing="1" id="error_items">
      <thead>
        <tr>
          <th>Znacznik</th>
          <th>Atrybuty type</th>
          <th>Atrybut sic</th>
          <th>Treść znacznika</th>
          <th title="wystąpień">Wyst.</th>
          <th title="dokumentów">Dok.</th>
        </tr>
      </thead>
      <tbody>
      {foreach from=$error_type_tags item=item name=errors}
        <tr key="{$item.type}_{$smarty.foreach.errors.index}">
            <td class="tag"><a href="#">{$item.tag}</a></td>
            <td>type="<b>{$item.type}</b>"</td>
            <td>sic="<b>{$item.sic}</b>"</td>
            <td>{$item.content}</td>
            <td style="text-align: right">{$item.count}</td>
            <td style="text-align: right">{$item.count_docs}</td>
        </tr>
      {/foreach}
      </tbody>
    </table>
</div>

<br style="clear: both"/>
