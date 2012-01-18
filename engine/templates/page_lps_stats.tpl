{include file="inc_header.tpl"}

<td class="table_cell_content">

<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Autorzy listów</a></li>
        <li><a href="#tabs-2">Znaczniki</a></li>
        <li><a href="#tabs-3">Błędy</a></li>
        <li><a href="#tabs-4">Współwystępowanie błędów</a></li>
        <li><a href="#tabs-5">Porównanie długości dokumentów</a></li>
    </ul>
    <div id="tabs-1">
        {include file="inc_lps_stats_authors.tpl"}    
    </div>
    <div id="tabs-2">
        {include file="inc_lps_stats_tags.tpl"}    
    </div>
    <div id="tabs-3">
        {include file="inc_lps_stats_errors.tpl"}    
    </div>
    <div id="tabs-4">
        {include file="inc_lps_stats_errors_matrix.tpl"}    
    </div>
    <div id="tabs-5">
        {include file="inc_lps_stats_document_length.tpl"}    
    </div>
</div>


{include file="inc_footer.tpl"}