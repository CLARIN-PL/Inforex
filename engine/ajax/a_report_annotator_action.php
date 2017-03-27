<?php
class Ajax_report_annotator_action extends CPage {

    var $isSecure = false;

    function execute(){
        global $db;

        $user_id = $_SESSION['_authsession']['data']['user_id'];
        $id = $_POST['id'];
        $shortlist = $_POST['shortlist'];

        //Handles all checkbox operations.
        if($user_id != null) {
            $params = array(
                'user_id' => $user_id,
                'annotation_type_id' => $id,
                'shortlist' => $shortlist
            );
            $db->replace("annotation_types_shortlist", $params);
        }

        return "";
    }
}