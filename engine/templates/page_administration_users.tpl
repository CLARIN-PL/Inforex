{extends file="admin-layout.tpl"}
{block name=body}
    <div class="row border-bottom bd-lightGra m-3">
        <div class="cell-md-4 d-flex flex-align-center">
            <h5 class="dashboard-section-title text-left text-left w-100">Users</h5>
        </div>
        <div class="cell-md-8 d-flex flex-justify-start flex-justify-end flex-align-center">
            <ul class="breadcrumbs bg-transparent">
                <li class="page-item"><a href="#" class="page-link"><span class="mif-meter"></span></a></li>
                <li class="page-item"><a href="#" class="page-link">Users</a></li>
            </ul>
        </div>
    </div>
    {if "admin"|has_role}
        {include file="inc_system_messages.tpl"}
        <div class="m-3">
            <div class="">
                <div class="bg-white p-4">
                    <div class="d-flex flex-wrap flex-nowrap-lg flex-align-center flex-justify-center flex-justify-start-lg mb-2">
                        <div class="w-100 mb-2 mb-0-lg" id="t1_search"></div>
                        <div class="ml-2 mr-2" id="t1_rows"></div>
                    </div>
                    <table id="usersTable" class="table striped"
                           data-role="table"
                           data-search-wrapper="#t1_search"
                           data-rows-wrapper="#t1_rows"
                           data-info-wrapper="#r1_info"
                           data-pagination-wrapper="#t1_pagination"
                           data-horizontal-scroll="true">
                        <thead>
                        <tr>
                            <th class="sortable-column">ID</th>
                            <th class="sortable-column">Login</th>
                            <th class="sortable-column">Name</th>
                            <th class="sortable-column">Email</th>
                            <th>Roles</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody id="usersTableBody">
                        {foreach from=$all_users item=user}
                            <tr>
                                <td>{$user.user_id}</td>
                                <td>{$user.login}</td>
                                <td>{$user.screename}</td>
                                <td>{$user.email}</td>
                                <td>
                                    {foreach from=explode(",", $user.roles) item=role}
                                        {if $role!=''}
                                            <span class="badge inline bg-green fg-white">{$role}</span>
                                        {/if}
                                    {/foreach}
                                </td>
                                <td>
                                    <a href="#" class="edit_user_button" data-toggle="modal"
                                       data-target="#edit_user_modal">
                                        <button class="button mt-1"><span class="mif-floppy-disk"></span></button>
                                    </a>
                                </td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
                    <div class="d-flex flex-column flex-justify-center">
                        <div id="t1_info"></div>
                        <div id="t1_pagination"></div>
                    </div>
                </div>
            </div>
        </div>
    {/if}
{/block}