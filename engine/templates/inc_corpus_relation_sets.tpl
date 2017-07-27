
<div class="container-fluid admin_tables">
    <div class="row">
        <div class="panel panel-primary scrollingWrapper" style="margin: 5px; width: 60%;">
            <div class="panel-heading">Relation sets</div>
            <div class="tableContent panel-body scrolling" style="">
                <table class="table table-striped" id="corpus_set_relation_sets" cellspacing="1">
                    <thead>
                    <tr>
                        <th style="width: 40px">Id</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th class = "text-center">Use</th>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach from=$relationSets item=set}
                        <tr>
                            <td>{$set.relation_set_id}</td>
                            <td>{$set.name}</td>
                            <td>{$set.description}</td>
                            <td class = "text-center">
                                <input type = "checkbox" class = "relation_set_checkbox" relation_set_id = "{$set.relation_set_id}" {if $set.assigned != null}checked{/if}>
                            </td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
