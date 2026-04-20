
<div class="container-fluid admin_tables corpus-settings-relation-sets">
    <div class="row corpus-settings-relation-sets-grid">
        <div class="col-md-10 col-md-offset-1 corpus-settings-relation-sets-column">
        <div class="panel administration-content-panel corpus-settings-relation-sets-panel">
            <div class="panel-heading administration-content-heading">
                <span class="administration-content-heading-icon"><i class="fa fa-link" aria-hidden="true"></i></span>
                <span>Relation sets</span>
            </div>
            <div class="panel-body">
                <div class="administration-table-wrapper corpus-settings-relation-sets-table-wrapper">
                <table class="table table-striped table-hover administration-table corpus-settings-relation-sets-table" id="corpus_set_relation_sets" cellspacing="1">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Owner</th>
                        <th class="text-center">Use</th>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach from=$relationSets item=set}
                        <tr>
                            <td class="corpus-settings-relation-set-id">{$set.relation_set_id}</td>
                            <td><span class="corpus-settings-relation-set-name">{$set.name}</span></td>
                            <td><span class="corpus-settings-relation-set-description" title="{$set.description|escape}">{$set.description}</span></td>
                            <td>
                                <span class="corpus-settings-relation-set-owner" title="{$set.screename|escape}">
                                    {$set.screename|regex_replace:"/(^\\S).*\\s(\\S)\\S*$/":"$1$2"|truncate:2:"":true|upper}
                                </span>
                            </td>
                            <td class="text-center corpus-settings-relation-set-use-cell {if $set.assigned != null}corpus-settings-relation-set-use-cell-active{/if}">
                                <label class="corpus-settings-relation-set-checkbox" title="Use relation set">
                                    <input type="checkbox" class="relation_set_checkbox" relation_set_id="{$set.relation_set_id}" {if $set.assigned != null}checked{/if}>
                                    <span aria-hidden="true"></span>
                                    <span class="sr-only">Use relation set</span>
                                </label>
                            </td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>
