<?php
class ajax_image_delete extends CPageAdministration {

    function __construct(){
        parent::__construct();
        $this->anySystemRole[] = USER_ROLE_LOGGEDIN;
    }

    function execute(){
        $image_id = $_POST['image_id'];
        $image_name = $_POST['image_name'];
        ChromePhp::log($image_id, $image_name);
        DbImage::deleteImage($image_id, $image_name);
    }

}