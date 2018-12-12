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
            [<a href="index.php?page={$page}&amp;corpus={$corpus.id}&amp;{$filter->getKey()}={$item->getKey()}">{if $item->isSelected()}&ndash;{else}+{/if}</a>]
            <img src="gfx/flag_{$item->getKey()}.png" title="{$row.name}" style="vertical-align: baseline"/>
            <a href="index.php?page={$page}&amp;corpus={$corpus.id}&amp;{$filter->getKey()}={$item->getKey()}&amp;filter_order={$filter->getOrder()}">{$item->getName()|default:"<i>none</i>"}</a>
        </li>
    {/foreach}
</ul>

