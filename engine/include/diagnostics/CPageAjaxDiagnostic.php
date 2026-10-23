<?php
class PageAjaxDiagnostic{

    static function findAjaxUsage($file_keywords){
        $js_folder = Config::Cfg()->get_path_www() . "/js";
        $files = scandir($js_folder);
        $regex_pattern = "/doAjax(.+|)[(]('|\")([^'\"]+)('|\")/";
        $ajax_list = array();

        foreach($files as $file) {
            $path = $js_folder . "/" . $file;
            if (!is_file($path)) {
                continue;
            }
            $handle = fopen($path, "r");
            if ($handle) {
                $line_number = 1;
                while (($line = fgets($handle)) !== false) {
                    preg_match_all($regex_pattern, $line, $matches);
                    $found_ajax = $matches[3];

                    //Finds ajax calls used mainly in jQuery validation requests
                    if(!$found_ajax){
                        preg_match_all('/ajax:([\s+]|)(\'|")(.+)(\'|")/', $line, $matches);
                        $found_ajax = $matches[3];
                    }

                    if($found_ajax){
                        foreach($found_ajax as $ajax){
                            $ajax_list["Ajax_".$ajax]['files'][$file] = $line_number;
                            $ajax_list["Ajax_".$ajax]['file_names'][pathinfo($file, PATHINFO_FILENAME)] = true;
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
            }
        }
        return self::getAjaxAccessInfo($ajax_list);
    }

    private static function getAjaxUsedOnPages($ajaxClassName){
        $filename = Config::Cfg()->get_path_engine() . "/ajax/" . strtolower($ajaxClassName) . ".php";
        if ( file_exists($filename) ){
            require_once($filename);
            $p = new $ajaxClassName();
            return $p->usedOnPages;
        } else {
            return array();
        }
    }

    private static function getAjaxAccessInfo($ajax_list){
        $validatorAjax = new PageAccessValidator(Config::Cfg()->get_path_engine(), "ajax");
        $validatorAjax->process();
        $ajax_access = $validatorAjax->items;

        $pages = self::getPageAccessInfoByFilename();

        foreach ($ajax_access as $ajax) {
            $className = $ajax->className;
            if (!isset($ajax_list[$className])) {
                $ajax_list[$className] = array();
            }
            $item =& $ajax_list[$className];
            $item['files'] = isset($item['files']) ? $item['files'] : array();
            $item['file_names'] = isset($item['file_names']) ? $item['file_names'] : array();
            $item['keywords'] = isset($item['keywords']) ? $item['keywords'] : array();
            $item['anyAjaxCorpusRole'] = $ajax->anyCorpusRole;
            $item['anyAjaxSystemRole'] = $ajax->anySystemRole;
            $item['parentClassName'] = $ajax->parentClassName;
            $item['description'] = $ajax->description;

            $usedOnPages = self::getAjaxUsedOnPages($ajax->className);

            //Save the roles of a CPage class
            if (!empty($item['file_names']) || count($usedOnPages)>0 ) {
                $item['anyPageCorpusRole'] = array();
                $item['anyPageSystemRole'] = array();

                if (!empty($item['file_names'])) {
                    foreach ($item['file_names'] as $file => $value) {
                        if (isset($pages[$file])) {
                            $item['CPages'][] = $pages[$file];
                            $item['anyPageCorpusRole'] = array_unique(array_merge($item['anyPageCorpusRole'], $pages[$file]->anyCorpusRole));
                            $item['anyPageSystemRole'] = array_unique(array_merge($item['anyPageSystemRole'], $pages[$file]->anySystemRole));
                        }
                    }
                }

                foreach ($usedOnPages as $file){
                    if (isset($pages[$file])) {
                        $item['CPages'][] = $pages[$file];
                        $item['anyPageCorpusRole'] = array_unique(array_merge($item['anyPageCorpusRole'], $pages[$file]->anyCorpusRole));
                        $item['anyPageSystemRole'] = array_unique(array_merge($item['anyPageSystemRole'], $pages[$file]->anySystemRole));
                    }
                }

                //If the intersection of the page roles & ajax roles is not empty - no access problem
                $item['access_problem'] = self::hasAccessConflict($item);
            } else {
                $item['CPages'] = null;
                $item['anyPageCorpusRole'] = array();
                $item['anyPageSystemRole'] = array();
                $item['access_problem'] = true;
            }

            if (in_array(ROLE_SYSTEM_USER_PUBLIC, $ajax->anySystemRole)){
                $item['access_problem'] = false;
            } else if (!empty($item['keywords'])
                    && empty($ajax->anyCorpusRole)
                    && !(in_array(ROLE_SYSTEM_USER_PUBLIC, $ajax->anySystemRole))) {
                $item['access_problem'] = true;
            }
            unset($item);
        }
        $defaults = array(
            'files' => array(),
            'file_names' => array(),
            'keywords' => array(),
            'anyAjaxCorpusRole' => array(),
            'anyAjaxSystemRole' => array(),
            'parentClassName' => '',
            'description' => '',
            'CPages' => array(),
            'anyPageCorpusRole' => array(),
            'anyPageSystemRole' => array(),
            'access_problem' => true,
        );
        foreach ($ajax_list as $className => $item) {
            $ajax_list[$className] = array_merge($defaults, $item);
        }
        ksort($ajax_list);
        return $ajax_list;
    }

    /**
     * Checks if there is an access conflict in the ajax.
     * If the ajax roles do not contain ALL page roles, then there is an access error.
     * + some exceptions
     * @param $ajax
     * @return bool
     */

    private static function hasAccessConflict($ajax){
        //Check if there are any corpus roles.

        //$anyAjaxRole = array_unique(array_merge($ajax['anyAjaxCorpusRole'], $ajax['anyAjaxSystemRole']));
       // $anyPageRole = array_unique(array_merge($ajax['anyPageCorpusRole'], $ajax['anyPageSystemRole']));

        //$hasAllRoles = count(array_intersect($anyAjaxRole, $anyPageRole)) == count($anyPageRole);

        $hasAllSystemRoles = true;
        $hasAllCorpusRoles = true;

        foreach($ajax['anyPageSystemRole'] as $pageSystemRole){
            if(!in_array($pageSystemRole, $ajax['anyAjaxSystemRole'])){
                $hasAllSystemRoles = false;
            }
        }

        foreach($ajax['anyPageCorpusRole'] as $pageCorpusRole){
            if(!in_array($pageCorpusRole, $ajax['anyAjaxCorpusRole'])){
                $hasAllCorpusRoles = false;
            }
        }

        //Handling exceptions
        //1. If ajax has public_user role
        if(in_array('public_user', $ajax['anyAjaxSystemRole'])){
            return false;
        }

        //2 If ajax has loggedin and page does not have public_user
        if((in_array('loggedin', $ajax['anyAjaxSystemRole'])) && !in_array('public_user', $ajax['anyPageSystemRole'])){
            $hasAllSystemRoles = true;
        }

        if((in_array('loggedin', $ajax['anyAjaxCorpusRole'])) && !in_array('public_user', $ajax['anyPageCorpusRole'])){
            $hasAllCorpusRoles = true;
        }

        return !($hasAllSystemRoles && $hasAllCorpusRoles);
    }

    /**
     * Returns the PageAccessValidator list of pages formatted in such a way that
     * the page filename without the extension is the key of the array.
     * @return array
     */
    private static function getPageAccessInfoByFilename(){

        $validatorPage = new PageAccessValidator(Config::Cfg()->get_path_engine(), "page");
        $validatorPage->process();
        $pages = $validatorPage->items;
        $pages_ordered = array();

        foreach($pages as $page){
            $name_parts = explode(".", $page->filename);
            $filename_no_extension = $name_parts[0];

            $pages_ordered[$filename_no_extension] = $page;
        }

        return $pages_ordered;

    }

}
