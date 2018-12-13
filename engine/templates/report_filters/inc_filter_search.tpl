{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
<div style="text-align: center">
    <form action="index.php" method="get">
        <input type="hidden" name="corpus" value="{$corpus.id}"/>
        <input type="text" name="{$filter->getKey()}" value="{' '|implode:$filter->getValue()}" style="width: 150px"/>
        <input type="hidden" name="page" value="{$page}"/>
        <input type="submit" class="button" value="search"/>
        <input type="hidden" name="filter_order" value="{$filter->getOrder()}">
    </form>
</div>
