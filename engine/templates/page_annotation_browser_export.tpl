{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
sep=, 
"Document id","Annotation id","Type","Left context","Annotation","Right context","Stage","Source"
{foreach from=$rows item=row}
{$row.report_id},{$row.id},"{$row.type}",{$row.left|escape_csv},{$row.annotation|escape_csv},{$row.right|escape_csv},"{$row.stage}","{$row.source}"
{/foreach}
{if $interupted}"Remaining annotations were omitted due to time limitation"{/if}