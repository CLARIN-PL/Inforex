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
            } else {
                // error opening the file.
            }
        }

        $ajax_info= self::getAjaxAccessInfo($ajax_list);
        return $ajax_info;
    }

    private function getAjaxAccessInfo($ajax_list)
    {
        global $config;
        $validatorAjax = new PageAccessValidator($config->path_engine, "ajax");
        $validatorAjax->process();
        $ajax_access = $validatorAjax->items;

        $pages = self::getPageAccessInfoByFilename();

        foreach ($ajax_access as $ajax) {
            $ajax_list[$ajax->className]['anyAjaxCorpusRole'] = $ajax->anyCorpusRole;
            $ajax_list[$ajax->className]['anyAjaxSystemRole'] = $ajax->anySystemRole;
            $ajax_list[$ajax->className]['parentClassName'] = $ajax->parentClassName;

            $ajax_list[$ajax->className]['parentClassName'] = $ajax->parentClassName;

            //Save the roles of a CPage class
            if ($ajax_list[$ajax->className]['file_names'] !== null) {
                $ajax_list[$ajax->className]['anyPageCorpusRole'] = array();
                $ajax_list[$ajax->className]['anyPageSystemRole'] = array();

                foreach ($ajax_list[$ajax->className]['file_names'] as $file => $value) {
                    if ($pages[$file] !== null) {
                        $ajax_list[$ajax->className]['CPages'][] = $pages[$file];
                        $ajax_list[$ajax->className]['anyPageCorpusRole'] = array_unique(array_merge($ajax_list[$ajax->className]['anyPageCorpusRole'], $pages[$file]->anyCorpusRole));
                        $ajax_list[$ajax->className]['anyPageSystemRole'] = array_unique(array_merge($ajax_list[$ajax->className]['anyPageSystemRole'], $pages[$file]->anySystemRole));

                    }
                }

                //If the intersection of the page roles & ajax roles is not empty - no access problem
                $accessProblem = false;

                //Check if there are any corpus roles.
                if (empty($ajax_list[$ajax->className]['anyPageCorpusRole']) && empty($ajax_list[$ajax->className]['anyAjaxCorpusRole'])) {
                    $hasCorpusRoles = false;
                } else {
                    $hasCorpusRoles = true;
                }

                $commonCorpusRoles = array_intersect($ajax_list[$ajax->className]['anyPageCorpusRole'], $ajax_list[$ajax->className]['anyAjaxCorpusRole']);
                $commonSystemRoles = array_intersect($ajax_list[$ajax->className]['anyPageSystemRole'], $ajax_list[$ajax->className]['anyAjaxSystemRole']);
                if (empty($commonSystemRoles)) {
                    $accessProblem = true;
                }

                if (empty($commonCorpusRoles) && $hasCorpusRoles) {
                    $accessProblem = true;
                }

                if ($accessProblem) {
                    $ajax_list[$ajax->className]['access_problem'] = true;
                }

            } else {
                $ajax_list[$ajax->className]['CPages'] = null;
                $ajax_list[$ajax->className]['access_problem'] = true;
            }

            if ($ajax_list[$ajax->className]['keywords'] != null && empty($ajax->anyCorpusRole) && !(in_array("public_user", $ajax->anySystemRole))) {
                $ajax_list[$ajax->className]['access_problem'] = true;
            }
        }
        ksort($ajax_list);

        ChromePhp::log($ajax_list);
        return $ajax_list;
    }


    /**
     * Returns the PageAccessValidator list of pages formatted in such a way that
     * the page filename without the extension is the key of the array.
     * @return array
     */
    private function getPageAccessInfoByFilename(){
        global $config;

        $validatorPage = new PageAccessValidator($config->path_engine, "page");
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