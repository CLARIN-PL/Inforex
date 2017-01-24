{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
sep=, 
"Word","POS","Total count","Total documents"{foreach from=$subcorpora item=c},Words in {$c.name},Documents in {$c.name}{/foreach}
{foreach from=$base_id_order item=base_id}{assign var=r value=$counts[$base_id]} 
{$r.base|escape_csv},{$r.pos|escape_csv},{$r.total_words},{$r.total_docs}{foreach from=$subcorpora item=c},{$r[$c.subcorpus_id].words},{$r[$c.subcorpus_id].docs}{/foreach}
{/foreach}
