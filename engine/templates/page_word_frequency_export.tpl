{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
sep=, 
"Word","Count","Documents","Documents %","Documents/Count"
{foreach from=$rows item=row}
{$row.base|escape_csv},{$row.c},"{$row.docs}",{$row.docs_c},{$row.docs_per}
{/foreach}
