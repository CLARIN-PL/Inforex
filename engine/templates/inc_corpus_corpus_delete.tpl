{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div class="ui-widget">
    <div style="margin-top: 20px; padding: 0 .7em;" class="ui-state-highlight ui-corner-all">
        <p style="margin: 1em 0"><span style="float: left; margin-right: .3em;" class="ui-icon ui-icon-info"></span>
        <strong>Warning!</strong> Notice, that this operation is permament and cannot be undone.</p>
    </div>
</div> 
 
<button type="button" class="delete_corpora_button button">Delete corpora</button>
<input id="corpus_name" type="hidden" value="{$corpus.name}" />
<input id="corpus_id" type="hidden" value="{$corpus.id}" />
<input id="corpus_description" type="hidden" value="{$corpus.description}" />
