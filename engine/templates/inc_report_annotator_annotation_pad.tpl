<div class="panel panel-info" {if $annotation_mode == 'relation_agreement'}style = "display: none;"{/if}>
    <div class="panel-heading" role="tab" id="headingPad">
        <h4 class="panel-title">
            <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapsePad" aria-expanded="false" aria-controls="collapsePad">
                Annotation types</a>
        </h4>
    </div>
    <div id="collapsePad" class="panel-collapse collapse {if $active_accordion=="collapsePad"}in{/if}" style="padding: 2px;">
        <div class="scrollingAccordion">
        <div class="column scrolling" id="widget_annotation">
        <div id="annotation-types" style="padding: 5px;" class="annotations scrolling">
            <button id="quick_add_cancel" style="display:none">Cancel quick add</button>
            <input type="radio" name="default_annotation" id="default_annotation_zero" style="display: none;" value="" checked="checked"/>
            <div class="tree" data-loaded="0"></div>
            <input type="hidden" id="report_id" value="{$row.id}"/>
        </div>
        </div>
        </div>
    </div>
</div>
