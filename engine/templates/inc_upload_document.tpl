{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div class="panel panel-info">
    <div class="panel-heading" role="tab" id="headingZero">
        <h4 class="panel-title">
            <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseZero" aria-expanded="false" aria-controls="collapseZero">
                View configuration</a>
        </h4>
    </div>
        <div id="collapseZero" class="panel-collapse collapse in">
            <form enctype="multipart/form-data" method="POST">
                <div class="scrolling scrollingAccordion" style="padding: 2px;">
                    <input type="hidden" name="MAX_FILE_SIZE" value="3145728" />
                    <table class="tablesorter" cellspacing="1">
                        <tr>
                            <th style="width: 100px">Ccl file</th>
                            <td><input type="file" name="ccl_file"/></td>
                        </tr>
                    </table><br/>
                    <table class="tablesorter" cellspacing="1">
                        <tr>
                            <th colspan="2">Optional</th>
                        </tr>
                        <tr>
                            <th style="width: 100px">Pre-morph file</th>
                            <td><input type="file" name="pre_morph"/></td>
                        </tr>
                        <tr>
                            <th style="width: 100px">Relations file</th>
                            <td><input type="file" name="relations_file"/></td>
                        </tr>
                    </table>
                </div>
                <div class="panel-footer scrollingFix">
                    <input type="submit" value="Upload" class="btn btn-primary"/>
                </div>
            </form>
        </div>
</div>
