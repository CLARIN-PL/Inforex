{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
<div class="corpus-documents-filter-search-wrapper">
    <form action="index.php" method="get" class="corpus-documents-filter-search-form">
        <input type="hidden" name="corpus" value="{$corpus.id}"/>
        <input type="text" name="{$filter->getKey()}" value="{' '|implode:$filter->getValue()}" style="width: 150px" class="corpus-documents-filter-search-input"/>
        <input type="hidden" name="page" value="{$page}"/>
        <button type="submit" class="button corpus-documents-filter-search-button" title="Search">
            <i class="fa fa-search" aria-hidden="true"></i>
        </button>
        <input type="hidden" name="filter_order" value="{$filter->getOrder()}">
    </form>
</div>
