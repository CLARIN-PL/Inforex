{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 *}

<div id="tabs">
    <nav class="navbar navbar-report">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    {*<li class="active"><a href="#">Link <span class="sr-only">(current)</span></a></li>*}
                    {foreach from=$pages item=p}
                        <li class="{if $page==$p.name}active{/if}">
                            <a href="index.php?page={$p.name}">{$p.title}</a>
                        </li>
                    {/foreach}
                </ul>
            </div>
        </div>
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