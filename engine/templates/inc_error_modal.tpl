<div class="modal ajax_error_modal" id="ajax_access_error_modal">
    <div class="modal-dialog ajax_error_modal_dialog ajax_access_error">
        <div class="modal-content ajax-error-modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title"><i class="fa fa-lock" aria-hidden="true"></i> Access required</h4>
            </div>
            <div class="modal-body ajax-modal-body">
                <div class="ajax-error-modal-card ajax-error-modal-card-access">
                    <div class="ajax-error-modal-intro">You do not currently have permission to access this page.</div>
                    <div class="ajax-error-modal-heading" id="ajax_error_message"></div>
                    <div class="ajax-error-modal-section-label">Required roles</div>
                    <div class="ajax-error-modal-tags" id="ajax_roles_required"></div>
                    <div class="ajax-error-modal-section-label">Granted roles</div>
                    <div class="ajax-error-modal-tags" id="ajax_roles_granted"></div>
                </div>
            </div>
            <div class="modal-footer ajax-error-modal-footer">
                <button type="button" class="btn btn-default ajax-error-copy-button" id="ajax_error_copy_button"><i class="fa fa-clipboard" aria-hidden="true"></i> Copy details</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal ajax_error_modal" id="ajax_error_modal">
    <div class="modal-dialog ajax_error_modal_dialog ajax_error">
        <div class="modal-content ajax-error-modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> An error has occurred</h4>
            </div>
            <div class="modal-body ajax-modal-body">
               <div class="ajax-error-modal-card">
                   <div class="ajax-error-modal-heading" id="ajax_error_heading"></div>
                   <div class="ajax-error-modal-section-label ajax-error-modal-details-label">Technical details</div>
                   <pre class="ajax-error-modal-pre" id="ajax_error_message_code"></pre>
               </div>
            </div>
            <div class="modal-footer ajax-error-modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
