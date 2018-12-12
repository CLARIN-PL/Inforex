{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<form action="index.php?page={$page}&filter_order={$filter->getOrder()}">
    <input type="hidden" name="corpus" value="{$corpus.id}"/>
    <input type="text" name="search" value="{' '|implode:$filter->getValue()}" style="width: 150px"/>
    <input type="hidden" name="page" value="{$page}"/>
    <input type="submit" class="button" value="search"/>
</form>
