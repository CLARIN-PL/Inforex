{extends file="admin-layout.tpl"}
{block name=body}
    <div class="row border-bottom bd-gray m-1">
        <div class="cell-md-4 d-flex flex-align-center">
            <ul class="breadcrumbs bg-transparent">
                <li class="page-item"><a href="#" class="page-link">Administration</a></li>
                <li class="page-item"><a href="#" class="page-link"><span class="mif-users"> Users</a></li>
            </ul>
        </div>
        <div class="cell-md-8 d-flex flex-justify-start flex-justify-end flex-align-center">
            <button class="image-button primary" onclick="Metro.dialog.open('#createNewUser')">
                <span class="mif-user-plus icon"></span>
                <span class="caption">Add user</span>
            </button>
        </div>
    </div>
    <div class="m-3">
        <div class="">
            <div class="bg-white p-4">
                <div class="d-flex flex-wrap flex-nowrap-lg flex-align-center flex-justify-center flex-justify-start-lg mb-2">
                    <div class="mr-2" id="t1_rows"></div>
                    <div class="w-100 mb-2 mb-0-lg" id="t1_search"></div>
                </div>
                <table id="usersTable" class="table row-border striped row-hover compact"
                       data-role="table"
                       data-search-wrapper="#t1_search"
                       data-rows-wrapper="#t1_rows"
                       data-info-wrapper="#r1_info"
                       data-pagination-wrapper="#t1_pagination"
                       data-horizontal-scroll="true"
                       data-cell-wrapper="false">
                    <thead>
                    <tr>
                        <th class="sortable-column" data-cls-column="id">ID</th>
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
                            <td class="id">{$user.user_id}</td>
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
                                    <button class="button"><span class="mif-pencil"></span></button>
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
    <div id="createNewUser" class="dialog" data-role="dialog">
        <div class="dialog-title">Create new user</div>
        <div class="dialog-content">
            <div class="bg-white p-2">
                <form id="create_user_form" action="index.php?page=user_admin" method="post">
                    <input type="hidden" name="action" value="user_add">
                    <div class="row mb-2">
                        <label class="cell-sm-4">Login</label>
                        <div class="cell-sm-8">
                            <input id="create_user_login" type="text" name="login" data-validate="required">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <label class="cell-sm-4">User name</label>
                        <div class="cell-sm-8">
                            <input id="create_user_username" type="text" name="name" data-validate="required">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <label class="cell-sm-4">Email</label>
                        <div class="cell-sm-8">
                            <input id="create_user_email" type="email" name="email" data-validate="required email">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <label class="cell-sm-4">Password</label>
                        <div class="cell-sm-8">
                            <input id="create_user_password" name="password" type="password"
                                   data-validate="required minlength=6"">
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="dialog-actions">
            <button class="button js-dialog-close">Cancel</button>
            <button class="button primary confirm_create_user">Create</button>
        </div>
    </div>
    <div id="editUser" class="dialog" data-role="dialog">
        <div class="dialog-title">Edit user</div>
        <div class="dialog-content">
            <div class="bg-white p-2">
                <form id="edit_user_form" action="index.php?page=user_admin" method="post">
                    <input type="hidden" name="action" value="user_edit">
                    <input type="hidden" name="user_id" value="" id="user_id">
                    <div class="row mb-2">
                        <label class="cell-sm-4">Login</label>
                        <div class="cell-sm-8">
                            <input id="edit_user_login" type="text" name="login" data-validate="required">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <label class="cell-sm-4">User name</label>
                        <div class="cell-sm-8">
                            <input id="edit_user_username" type="text" name="name" data-validate="required">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <label class="cell-sm-4">Email</label>
                        <div class="cell-sm-8">
                            <input id="edit_user_email" type="email" name="email" data-validate="required email">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <label class="cell-sm-4">Password</label>
                        <div class="cell-sm-8">
                            <input id="edit_user_password" name="password" type="password"
                                   data-validate="required minlength=6"">
                        </div>
                        <p class="cell-sm-12">(Password will not change if the field is empty.)</p>
                    </div>
                    <div class="row mb-2 roles">

                    </div>
                </form>
            </div>
        </div>
        <div class="dialog-actions">
            <button class="button js-dialog-close">Cancel</button>
            <button class="button primary confirm_edit_user">Save</button>
        </div>
    </div>
{/block}
{block name=scripts}
    <script src="js/page_administration_users.js"></script>
{/block}