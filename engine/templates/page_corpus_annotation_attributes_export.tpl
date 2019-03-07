{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
sep=, 
"Value","Count"
{foreach from=$rows item=row}
{$row.value},{$row.c}
{/foreach}