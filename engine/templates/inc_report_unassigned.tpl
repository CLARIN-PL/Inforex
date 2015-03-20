{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<input type="hidden" id="unassigned_subpage" value="{$unassigned_subpage}"/>
 
<div style="padding: 0pt 0.7em; margin: 10px;" class="ui-state-highlight ui-corner-all"> 
    <p style="padding: 10px"><span style="float: left; margin-right: 0.3em;" class="ui-icon ui-icon-info"></span>
    <strong>Info</strong> The <b>{$perspective.title}</b> perspective is not assigned to this corpora.</p>
</div>

<div style="margin-left: 10px;">
    To enable the <b>{$perspective.title}</b> perspective click the button: <input type="submit" class="button" id="enable" value="Enable" title="Enable {$perspective.title} for this corpora"/>
</div>