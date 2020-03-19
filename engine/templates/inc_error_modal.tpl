<div class="modal ajax_error_modal" id="ajax_access_error_modal">
    <div class="modal-dialog ajax_error_modal_dialog ajax_access_error">
        <div class="modal-content"  style = "overflow: auto;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">An error has occurred</h4>
            </div><div class="container"></div>
            <div class="modal-body ajax-modal-body">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-1" style="padding: 40px"><i class="fa fa-lock fa-5x" aria-hidden="true"></i></div>
                            <div class="col-md-11">
                                <div class="row"><h1 id = "ajax_error_message"></h1></div>
                                <div class="row">This page requires one of the following roles:</div>
                                <div class="row" id = "ajax_roles_required"></div>
                                <div class="row">You are granted the following roles:</div>
                                <div class="row" id = "ajax_roles_granted"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>

<div class="modal ajax_error_modal" id="ajax_error_modal">
    <div class="modal-dialog ajax_error_modal_dialog ajax_error">
        <div class="modal-content"  style = "overflow: auto;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">An error has occurred</h4>
            </div><div class="container"></div>
            <div class="modal-body ajax-modal-body">
               <div class = "col-lg-12">
                   <div class = 'row'>
                       <h1 id = "ajax_error_heading"></h1>
                   </div>
                   <div class = "row">
                       <pre style = "overflow: auto; height: 400px;" id="ajax_error_message_code"></pre>
                   </div>
               </div>
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>

