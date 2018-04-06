<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

mb_internal_encoding("UTF-8");

/**
 * Main class of the system. It control the flow, i.e. handle the POST/GET request and generate a page content.
 *
 * @author Michał Marcińczuk
 *
 */
class InforexWeb
{

    function __construct()
    {
        global $config;
        set_exception_handler('InforexWeb::custom_exception_handler');

        /********************************************************************8
         * Aktywuj FireBug-a
         */
        FB::setEnabled(true);

        /********************************************************************8
         * Rozpocznij sesję
         */
        HTTP_Session2::useCookies(true);
        HTTP_Session2::start($config->sid);
        HTTP_Session2::setExpire(time() + $config->session_time);
    }

    /**
     * Function is used to display an exception thrown by PHP.
     * It replaces the default exception handler.
     * @param unknown $exception
     */
    static function custom_exception_handler($exception)
    {
        echo "<div style='background:red; margin: 0px; padding: 5px'>";
        echo "<div style='float: left; width: 100px; background:red; color: white; padding: 5px; font-weight: bold;'>Exception</div>";
        echo "<div style='background: white; color: red; padding: 5px; margin-left: 110px; white-space: pre-wrap'>";
        echo htmlspecialchars($exception->getMessage());
        echo "</div>";
        echo "</div><br/>";

        echo "<div style='background:red; color:white; margin: 0px; padding: 5px'>Exception stack</div>";
        echo "<pre style='border: 1px solid red; padding: 5px; background: #FFE1D0; margin: 0px; font-size: 10px; height: 400px; overflow: scroll'>";
        ob_start();
        print_r($exception);
        echo htmlspecialchars(ob_get_clean());
        echo "</pre>";
    }


    /********************************************************************
     *
     */
    private function ajaxError($type, $msg)
    {
        return json_encode(
            array(
                "error" => true,
                "error_code" => $type,
                "error_msg" => $msg
            )
        );
    }

    private function ajaxSuccess($result)
    {
        return json_encode(
            array(
                "error" => false,
                "result" => $result
            )
        );
    }

    /********************************************************************
     * Handles an Action request.
     */
    function doAction($action, &$variables)
    {
        global $user, $corpus, $config;

        include($config->path_engine . "/actions/a_{$action}.php");
        $class_name = "Action_{$action}";
        $o = new $class_name();

        // Autoryzuj dostęp do akcji.
        if ($o->isSecure && !$auth->getAuth()) {
            // Akcja wymaga autoryzacji, która się nie powiodła.
            fb("Auth required");
        } else {
            // Sprawdź dodatkowe ograniczenia dostępu do akcji.
            if (($permission = $o->checkPermission()) === true) {
                $page = $o->execute();
                $page = $page ? $page : $_GET['page'];

                $variables = array_merge($o->getVariables(), $o->getRefs());
                $variables["warnings"] = $o->getWarnings();
            } else {
                $variables = array('action_permission_denied' => $permission);
                fb("PERMISSION: " . $permission);
            }
        }

        return $page;
    }

    /********************************************************************
     * Handles an Ajax request.
     */
    function doAjax($ajax, &$variables)
    {
        global $user, $corpus, $config;

        header('Content-Type: application/json; charset=utf-8');

        /** Process an ajax request */
        $filename = $config->path_engine . "/ajax/ajax_{$ajax}.php";
        if (!file_exists($filename)) {
            echo $this->ajaxError("ERROR_APPLICATION", "Ajax not found: " . $filename);
            return;
        }
        include($filename);
        $class_name = "Ajax_{$ajax}";
        $o = new $class_name();
        $access = $o->hasAccess($user, $corpus);
        if ($access === true) {
            if (is_array($variables)) {
                $o->setVariables($variables);
            }
            try {
                $result = $o->execute();
                echo $this->ajaxSuccess($result);
            } catch (Exception $e) {
                echo $this->ajaxError("ERROR_APPLICATION", $e->getMessage());
            }
        } else {
            echo $this->ajaxError("ERROR_AUTHORIZATION", $access);
        }
    }

    /********************************************************************
     * Handles a Page request.
     */
    function doPage($page, &$variables)
    {
        global $user, $corpus, $config, $auth, $db;

        $stamp_start = time();

        /** Show the content of the page */
        // If the page is not set the set the default 'home'
        $page = $page ? $page : 'home';

        // If the required module does not exist, change it silently to the default.
        $pageFile = $config->path_engine . "/page/page_{$page}.php";
        if (!file_exists($pageFile)) {
            return $this->doPage("home", $variables);
        }

        require_once($pageFile);
        $page_class_name = "Page_{$page}";
        $o = new $page_class_name();
        if (is_array($variables)) {
            $o->setVariables($variables);
            if (isset($variables["warnings"])) {
                $o->addWarnings($variables["warnings"]);
            }
        }

        // Assign objects to the page
        $o->set('user', $user);
        $o->set('page', $page);
        $o->set('corpus', $corpus);
        $o->set('release', RELEASE);
        $o->set('config', $config);
        $o->set('rev', $this->getRevisionKey());
        $o->loadAnnotationTypesCss();

        $access = $o->hasAccess($user, $corpus);
        if ($access === true) {
            $o->execute();
            $o->set('page_generation_time', (time() - $stamp_start));
            $o->set('compact_mode', $_COOKIE['compact_mode']);
            $o->set('warnings', $o->getWarnings());
            foreach ($variables as $k => $v) {
                $o->set($k, $v);
            }
            $o->display($page);

            if ($o->get("subpage")) {
                $page .= "/" . $o->get("subpage");
            }
        } else {
            $variables['access'] = $access;
            return $this->doPage("access_denied", $variables);
        }

        return $page;
    }

    /********************************************************************
     * Generate the output that will be send to the browser.
     * Determine the type of action according to $_GET and $_POST arrays.
     *
     * The function handles three types of request:
     * 1. Action
     * 2. Ajax
     * 3. Page
     *
     * Action is an operation which is performed in the background. No output is generated by the action.
     * The action can assign some variables which are passed to Ajax or Page.
     *
     * Ajax is an operation wich returns a json array. It is designed to execute operations in a dynamic way
     * by JS scripts.
     *
     * Page is an operation which returns a html page.
     *
     */
    function execute()
    {
        global $config, $user, $auth, $db, $corpus;

        $variables = array();
        $action = $_POST['action'];
        $ajax = $_POST['ajax'];
        $page = $_GET['page'];
        $stamp_start = time();

        /* Gather the data about an activity */
        $activity_page = array();
        $activity_page['ip_id'] = $db->get_entry_key("ips", "ip_id", array("ip" => $_SERVER["REMOTE_ADDR"]));
        $activity_page['user_id'] = isset($user) ? $user['user_id'] : null;
        $activity_page['corpus_id'] = isset($corpus) ? $corpus['id'] : null;
        $activity_page['report_id'] = RequestLoader::getDocumentId();
        $activity_page['datetime'] = date("Y-m-d H:i:s");

        if ($action && file_exists($config->path_engine . "/actions/a_{$action}.php")) {
            $page = $this->doAction($action, $variables);
            $activity_page['activity_type_id'] = $db->get_entry_key("activity_types", "activity_type_id", array("name" => $action, "category" => "action"));
            $activity_page['execution_time'] = time() - $stamp_start;
            $db->insert("activities", $activity_page);
        }

        if ($ajax) {
            $this->doAjax($ajax, $variables);
            $activity_page['activity_type_id'] = $db->get_entry_key("activity_types", "activity_type_id", array("name" => $ajax, "category" => "ajax"));
        } else {
            $page = $this->doPage($page, $variables);
            $activity_page['activity_type_id'] = $db->get_entry_key("activity_types", "activity_type_id", array("name" => $page, "category" => "page"));
        }

        $activity_page['execution_time'] = time() - $stamp_start;
        $db->insert("activities", $activity_page);

    }

    function getRevisionKey()
    {
        $commitHash = exec("git rev-list -n 1 master");
        $revKey = substr($commitHash, 0, 8);
        return $revKey;
    }

}

?>
