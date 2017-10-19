{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 *}

<div id="tabs" style = "width: 100%; margin: 5px; height: 20%;">
    <nav class="navbar navbar-report">
        <div class="container-fluid">
                <ul class="nav navbar-nav">
                    {if "admin"|has_role}
                        <li class="{if $page=="annotation_edit"}active{/if}">
                            <a href="index.php?page=annotation_edit">Annotations</a>
                        </li>
                        <li class="{if $page=="relation_edit"}active{/if}">
                            <a href="index.php?page=relation_edit">Relations</a>
                        </li>
                        <li class="{if $page=="event_edit"}active{/if}">
                            <a href="index.php?page=event_edit">Events</a>
                        </li>
                        <li class="{if $page=="sens_edit"}active{/if}">
                            <a href="index.php?page=sens_edit">WSD senses</a>
                        </li>
                        <li class="{if $page=="user_admin"}active{/if}">
                            <a href="index.php?page=user_admin">Users</a>
                        </li>
                        <li class="{if $page=="user_activities"}active{/if}">
                            <a href="index.php?page=user_activities">User activities</a>
                        </li>
                        <li class="{if $page=="anonymous_user_activities"}active{/if}">
                            <a href="index.php?page=anonymous_user_activities">Anonymous user activities</a>
                        </li>
                        <li class="{if $page=="shared_attribute_edit"}active{/if}">
                            <a href="index.php?page=shared_attribute_edit">Shared attributes</a>
                        </li>
                    {/if}
                </ul>
        </div><!-- /.container-fluid -->
    </nav>
</div>

<div class="modal fade settingsModal" id="deleteModal" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Are you sure you want to delete this?</h4>
            </div>
            <div class="modal-body" id = "deleteContent">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger confirmDelete" data-dismiss="modal">Delete</button>
            </div>
        </div>
    </div>
</div>