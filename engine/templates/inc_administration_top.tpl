{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 *}

{if "admin"|has_role}
<div id="tabs" style = "width: 100%; margin: 5px 0; height: 20%;">
    <nav class="navbar navbar-report">
        <div class="container-fluid">
            <ul class="nav navbar-nav">
                {foreach from=$pages item=p}
                    <li class="{if $page==$p.name}active{/if}">
                        <a href="index.php?page={$p.name}">{$p.title}</a>
                    </li>
                {/foreach}
            </ul>
        </div>
    </nav>
</div>
{/if}

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