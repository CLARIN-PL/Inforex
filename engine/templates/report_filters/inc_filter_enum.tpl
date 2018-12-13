{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<ul>
    {foreach from=$filter->getItems() item=item}
        <li{if $item->isSelected()} class="active"{/if}>
            <span class="num">&nbsp;{$item->count}</span>
            [<a href="index.php?page={$page}&amp;corpus={$corpus.id}&amp;{$filter->getKey()}={","|implode:$filter->getValue()},{$item->getKey()}&amp;filter_order={$filter->getOrder()}">{if $item->isSelected()}&ndash;{else}+{/if}</a>]
            <a href="index.php?page={$page}&amp;corpus={$corpus.id}&amp;{$filter->getKey()}={$item->getKey()}&amp;filter_order={$filter->getOrder()}">{$item->getName()|default:"<i>none</i>"}</a>
        </li>
    {/foreach}
</ul>

