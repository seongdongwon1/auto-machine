<?php
    // require_once($_SERVER['DOCUMENT_ROOT'].'/config/func.php');

    $route = new Route();

    /**
     * pages
     */
    $route->get('/'                             , checkDevice('/view/index.html'));
    $route->get('/dd'                             , checkDevice('/view/dd.html'));

    $route->notFound(checkDevice('/view/error.html'));

    
    /**
     *  device check
     */
    function checkDevice ($path) {
        $mobile_agent = "/(iPod|iPhone|Android|BlackBerry|SymbianOS|SCH-M\d+|Opera Mini|Windows CE|Nokia|SonyEricsson|webOS|PalmOS)/";

        if(preg_match($mobile_agent, $_SERVER['HTTP_USER_AGENT'])){
        	return "mo".$path;
        }else{
        	return "pc".$path;
        }
    }

    class Route {

        private function simpleRoute ($file, $route) {
            if (!empty($_REQUEST['uri'])) {
                $route = preg_replace("/(^\/)|(\/$)/","",$route);
                $reqUri =  preg_replace("/(^\/)|(\/$)/","",$_REQUEST['uri']);
            } else {
                $reqUri = "/";
            }
            
            if ($reqUri == $route) {
                $params = [];
                include($file);
                exit();

            }

        }

        function get ($route, $file) {
            $params = [];
            $paramKey = [];

            preg_match_all("/(?<={).+?(?=})/", $route, $paramMatches);

            if (empty($paramMatches[0])) {
                $this->simpleRoute($file,$route);
                return;
            }

            foreach ($paramMatches[0] as $key) {
                $paramKey[] = $key;
            }

            if (!empty($_REQUEST['uri'])) {
                $route = preg_replace("/(^\/)|(\/$)/","",$route);
                $reqUri =  preg_replace("/(^\/)|(\/$)/","",$_REQUEST['uri']);
            } else {
                $reqUri = "/";
            }
            
            $uri = explode("/", $route);
            $indexNum = []; 

            foreach ($uri as $index => $param) {
                if (preg_match("/{.*}/", $param)) {
                    $indexNum[] = $index;
                }
            }

            $reqUri = explode("/", $reqUri);

            foreach ($indexNum as $key => $index) {
                if (empty($reqUri[$index])) {
                    return;
                }

                $params[$paramKey[$key]] = $reqUri[$index];
                $reqUri[$index] = "{.*}";
            }

            $reqUri = implode("/",$reqUri);
            $reqUri = str_replace("/", '\\/', $reqUri);

            if (preg_match("/$reqUri/", $route)) {
                include($file);
                exit();
            }
        }

        function notFound ($file) {
            include($file);
            exit();
        }
    }
?>
