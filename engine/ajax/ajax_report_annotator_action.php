<?php
class Ajax_report_annotator_action extends CPage {

    var $isSecure = false;

    function execute(){
        global $db;

        $user_id = $_SESSION['_authsession']['data']['user_id'];
        $id = $_POST['id'];
        $shortlist = $_POST['shortlist'];
        $action = $_POST['action'];

        if($user_id != null) {
            if($action == 'visibility'){
                $annotation_info = array(
                    'user_id' => $user_id,
                    'annotation_type_id' => $id,
                    'shortlist' => $shortlist
                );

                $status = DbAnnotation::getAnnotationVisibility($id);
                DbAnnotation::deleteUserAnnotationStatus($user_id, $id);
                DbAnnotation::setUserAnnotationStatus($annotation_info);

                if(($status[0]['shortlist'] == 0 && $shortlist == 1) || ($status[0]['shortlist'] == 1 && $shortlist == 0)){
                    return 1;
                }
            } else if($action == 'refresh_default'){
                DbAnnotation::deleteUserAnnotationStatus($user_id, $id);
            }

        }

        return 0;
    }
}