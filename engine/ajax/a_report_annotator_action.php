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
                $params = array(
                    'user_id' => $user_id,
                    'annotation_type_id' => $id,
                    'shortlist' => $shortlist
                );

                //$sql = "SELECT ats.shortlist AS 'user', at.shortlist AS 'default' FROM `annotation_types_shortlist` ats JOIN `annotation_types` at ON at.annotation_type_id = ats.annotation_type_id WHERE (user_id = " . $user_id . " AND ats.annotation_type_id = " . $id . ");";

                //$sql = "SELECT ats.shortlist as s1, ant.shortlist as s2 from `annotation_types_shortlist` ats JOIN `annotation_types` ant ON ant.annotation_type_id = ats.annotation_type_id WHERE ats.annotation_type_id = ?";
                $sql = "SELECT `shortlist` FROM `annotation_types` WHERE annotation_type_id = ?";

                //ChromePhp::log($sql);

                $status = $db->fetch_rows($sql, array($id));

                //ChromePhp::log($shortlist);
                //ChromePhp::log($status);
                //ChromePhp::log($params);

                $sql_delete = "DELETE FROM `annotation_types_shortlist` WHERE (user_id = ? AND annotation_type_id = ?)";
                $db->execute($sql_delete, array($user_id, $id));
                ChromePhp::log($params);
                $db->replace("annotation_types_shortlist", $params);

                if(($status[0]['shortlist'] == 0 && $shortlist == 1) || ($status[0]['shortlist'] == 1 && $shortlist == 0)){
                    ChromePhp::log("Not default");
                    return 1;
                }
            } else if($action == 'refresh_default'){
                $sql_delete = "DELETE FROM `annotation_types_shortlist` WHERE (user_id = ? AND annotation_type_id = ?)";
                $db->execute($sql_delete, array($user_id, $id));
            }

        }

        return 0;
    }
}