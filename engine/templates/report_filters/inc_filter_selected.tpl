{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<ul>
    {if $filter->isActive() }
        <li class="active">
            <a href="index.php?page={$page}&amp;corpus={$corpus.id}&amp;{$filter->getKey()}=0&amp;filter_order={$filter->getOrderCancel()}">Selected documents</a>
        </li>
        <li>
            All documents
        </li>
    {else}
        <li>
            <a href="index.php?page={$page}&amp;corpus={$corpus.id}&amp;{$filter->getKey()}=1&amp;filter_order={$filter->getOrder()}">Selected documents</a>
        </li>
        <li class="active">
            All documents
        </li>
    {/if}
</ul>

