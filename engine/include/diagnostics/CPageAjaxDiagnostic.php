<?php
class PageAjaxDiagnostic{

    static function findAjaxUsage($file_keywords){
        global $config;
        $js_folder = $config->path_www . "/js";
        $files = scandir($js_folder);
        $regex_pattern = "/doAjax(.+|)[(]('|\")([^'\"]+)('|\")/";
        $ajax_list = array();

        foreach($files as $file) {
            $handle = fopen($js_folder . "/" . $file, "r");
            if ($handle) {
                $line_number = 1;
                while (($line = fgets($handle)) !== false) {
                    preg_match_all($regex_pattern, $line, $matches);
                    $found_ajax = $matches[3];
                    if($found_ajax){
                        foreach($found_ajax as $ajax){
                            $ajax_list["Ajax_".$ajax]['files'][$file] = $line_number;
                            foreach($file_keywords as $keyword){
                                if(strpos($file, $keyword) !== false){
                                    $ajax_list["Ajax_".$ajax]['keywords'][$keyword] = true;
                                }
                            }
                        }
                    }
                    $line_number++;
                }

                fclose($handle);
            } else {
                // error opening the file.
            }
        }

        $ajax_info= self::getAjaxAccessInfo($ajax_list);
        return $ajax_info;
    }

    private function getAjaxAccessInfo($ajax_list){
        global $config;
        $validatorAjax = new PageAccessValidator($config->path_engine, "ajax");
        $validatorAjax->process();
        $ajax_access = $validatorAjax->items;

        foreach($ajax_access as $ajax){
            $ajax_list[$ajax->className]['anyCorpusRole'] = $ajax->anyCorpusRole;
            $ajax_list[$ajax->className]['anySystemRole'] = $ajax->anySystemRole;
            $ajax_list[$ajax->className]['parentClassName'] = $ajax->parentClassName;

            if($ajax_list[$ajax->className]['keywords'] != null && empty($ajax->anyCorpusRole) && !(in_array("ROLE_SYSTEM_USER_PUBLIC", $ajax->anySystemRole))){
                $ajax_list[$ajax->className]['access_problem'] = true;
            }
        }
        ksort($ajax_list);

        return $ajax_list;
    }

}