{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header2.tpl"}
<div class="container-fluid admin_tables corpus-relations-page">
    <div class="row corpus-relations-grid">
	<div class="col-md-4 scrollingWrapper corpus-relations-sidebar-column">
        <div class="panel administration-content-panel corpus-relations-panel corpus-relations-config-panel">
            <div class="panel-heading administration-content-heading">
                <span class="administration-content-heading-icon"><i class="fa fa-sliders" aria-hidden="true"></i></span>
                <span>View configuration</span>
            </div>
            <div class="panel-body corpus-relations-config-body">
                <div class="corpus-relations-config-fields">
                    <label for="mode_select">Mode</label>
                    <div class="corpus-relations-inline-field">
                        <select class="form-control" id="mode_select" name="mode_select">
                            <option value="final" {if $mode == 'final'}selected{/if}>Final</option>
                            <option value="agreement" {if $mode == 'agreement'}selected{/if}>Agreement</option>
                        </select>
                        <button class="btn btn-primary corpus-relations-confirm-btn" id="confirm_view_config">
                            <i class="fa fa-check" aria-hidden="true"></i>
                            <span>Apply</span>
                        </button>
                    </div>
                    <div class="user_selection" {if $mode != 'agreement'}style="display: none;"{/if}>
                        <label for="user_select">User</label>
                        <div class="corpus-relations-inline-field">
                            <select class="form-control" id="user_select" name="user_select">
                                <option value="-">-</option>
                                {foreach from = $users item = user}
                                    <option {if $user.user_id == $selected_user}selected{/if} value="{$user.user_id}">
                                        {$user.screename} ({$user.number_of_rels})
                                    </option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

		<div class="panel administration-content-panel corpus-relations-panel">
			<div class="panel-heading administration-content-heading">
                <span class="administration-content-heading-icon"><i class="fa fa-random" aria-hidden="true"></i></span>
                <span>Relation sets and types</span>
            </div>
			<div class="panel-body scrolling corpus-relations-table-panel-body">
                <div class="administration-table-wrapper corpus-relations-table-wrapper">
				<table cellspacing="1" class="table table-striped table-hover administration-table corpus-relations-type-table" id="relationlist">
					<thead>
						<tr>
							<th class="corpus-relations-set-column">Rel. Set</th>
							<th>Rel. Type</th>
							<th class="td-right corpus-relations-count-column">Count</th>
						</tr>
					</thead>
					<tbody>
					{foreach from=$relations_type key=key1 item=item}
						<tr class="setGroup">
							<td colspan="2" class="corpus-relations-set-name">{$item.relation_name}</td>
							<td class="relationNameCount td-right"><span class="corpus-relations-count-badge">{$item.relation_count}</span></td>
						</tr>
						{foreach from=$item.types key=key2 item=types}
							<tr class="subsetGroup{if $key2 eq '0' && $key1 eq '0'} selected{/if}" style="display:none" id="{$item.relation_id}">
								<td class="empty"><span class="corpus-relations-branch" aria-hidden="true"></span></td>
								<td class="relationName">{$types.relation_type}</td>
								<td class="relationCount td-right">{$types.relation_count}</td>
							</tr>
						{/foreach}
					{/foreach}
					</tbody>
				</table>
                </div>
			</div>
		</div>
	</div>

	<div class="col-md-8 scrollingWrapper corpus-relations-main-column">
		<div id="relation-list" class="panel administration-content-panel corpus-relations-panel">
			<div class="panel-heading administration-content-heading">
                <span class="administration-content-heading-icon"><i class="fa fa-list-ul" aria-hidden="true"></i></span>
                <span>List of relations for the selected type</span>
            </div>
			<div class="panel-body scrolling corpus-relations-table-panel-body">
                <div id="relations-loading" class="administration-wsd-loading corpus-relations-loading" style="display: none;">
                    <img src="gfx/ajax.gif" alt="Loading"/>
                    <span>Loading relations...</span>
                </div>
                <div class="administration-table-wrapper corpus-relations-table-wrapper">
				<table cellspacing="1" class="table table-striped table-hover administration-table corpus-relations-list-table">
					<thead>
						<tr>
							<th>Document</th>
							<th>Subcorpus</th>
							<th>Source phrase</th>
							<th>Source category</th>
							<th>Target phrase</th>
							<th>Target category</th>
						</tr>
					</thead>
					<tbody id="relation_statistic_items">
						<tr class="corpus-relations-empty-row">
							<td colspan="6"><div class="corpus-relations-empty">Choose relation type</div></td>
						</tr>
					</tbody>
				</table>
                </div>
			</div>
		</div>
	</div>
    </div>
</div>
{include file="inc_footer.tpl"}
