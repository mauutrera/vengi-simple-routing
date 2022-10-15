<?php

namespace Vengi;

class Route
{
    /*
     |-------------------------------------------------------------------------------
     | We receive a route and an auto-invoked function, 
     | when the route matches the current url, the auto-invoked function is executed.
     |-------------------------------------------------------------------------------
     */

    public static function view($route,$function)
    {
        if ($_SERVER['REQUEST_URI'] === $route) {
            return $function();
        }
    }

    /*
     |---------------------------------------------------------------- 
     | They contain the methods, and classes in relation to the 
     | assigned route, in relation to the method to which they belong.
     |----------------------------------------------------------------
     */

    public static $get = [];
    public static $post = [];
    public static $put = [];
    public static $delete = [];

    /*
     |--------------------------------------------------------------------------------- 
     | In this method the routes of type GET are saved, it receives the route, 
     | class and method to use, these data are saved in an array within an 
     | associative array with the value of the path that we pass in $route, 
     | in the array of inside this array at position 0 it stores $class::method, 
     | and at position 1, if it exists, the reference query passed in $route is passed.
     |---------------------------------------------------------------------------------
     */

    public static function get($route,$class,$method)
    {
        $url_route = parse_url($route);
        self::$get[$url_route['path']][0]="$class::$method";
        self::$get[$url_route['path']][1]=isset($url_route['query']) ? $url_route['query'] : null;

        /*
        |-------------------------------------------------------------
        | This is in case the user puts a '/' at the end of the url.
        | For example: https://host.com/home or https://host.com/home/
        |-------------------------------------------------------------
        */

        if (!str_ends_with($url_route['path'],'/')) {
            self::$get[$url_route['path'].'/'][0]="$class::$method";
            self::$get[$url_route['path'].'/'][1]=isset($url_route['query']) ? $url_route['query'] : null;
        }
    }
    
    # Same as GET but for POST.

    public static function post($route,$class,$method)
    {
        $url_route = parse_url($route);
        self::$post[$url_route['path']][0]="$class::$method";
        self::$post[$url_route['path']][1]=isset($url_route['query']) ? $url_route['query'] : null;
    }
    
    # Same as GET but for PUT.

    public static function put($route,$class,$method)
    {
        $url_route = parse_url($route);
        self::$put[$url_route['path']][0]="$class::$method";
        self::$put[$url_route['path']][1]=isset($url_route['query']) ? $url_route['query'] : null;
    }

    # Same as GET but for DELETE.

    public static function delete($route,$class,$method)
    {
        $url_route = parse_url($route);
        self::$delete[$url_route['path']][0]="$class::$method";
        self::$delete[$url_route['path']][1]=isset($url_route['query']) ? $url_route['query'] : null;
    }

    /*
     |---------------------------------------------------- 
     | Execute get(), post(), put(), delete() type routes, 
     | not necessary for view() routes.
     |----------------------------------------------------
     */

    public static function run()
    {
        $url = parse_url($_SERVER['REQUEST_URI']);                  //Get and parse(the Host URL).
        $url_path = $url['path'];                                   // Save into $url_path the Host URL['path].
        $url_query = isset($url['query']) ? $url['query'] : null;   // If exists save the URL['query'] into $url_query.
        $url_query_clean = explode('=',$url_query,2)[0];            // Clean characters after '='.

        /*
         |---------------------------------------------------------------------------------
         | Check the request method, according to each GET, POST, PUT, DELETE request, 
         | according to the request method, validate the content of the corresponding 
         | variable $get, $post, $put or $delete. And depending on the url and its content, 
         | it forms the method and the information that is sent to it.
         |---------------------------------------------------------------------------------
         */

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                if (array_key_exists($url_path,self::$get)) {
                    if (self::$get[$url_path][1] === null && $url_query === null) {
                        self::$get[$url_path][0]();
                    } elseif (self::$get[$url_path][1] === null && $url_query !== null) {
                        self::$get[$url_path][0]($_GET);
                    } elseif ($url_query !== null && $url_query_clean === self::$get[$url_path][1] && sizeof($_GET) === 1 && is_natural_int(reset($_GET))) {
                        self::$get[$url_path][0](reset($_GET));
                    } else {
                        http_response_code(404);
                    }
                } else {
                    http_response_code(404);
                }
                break;

            case 'POST':
                if (sizeof($_POST)>0) {
                    csrf_token_validation();
                }
                
                if (array_key_exists($url_path,self::$post)) {
                    if (self::$post[$url_path][1] === null && $url_query === null) {
                        if (sizeof($_POST) > 0) {
                            self::$post[$url_path][0]($_POST);
                        } else {
                            self::$post[$url_path][0](file_get_contents('php://input'));
                        }
                    } elseif ($url_query !== null && $url_query_clean === self::$post[$url_path][1] && sizeof($_GET) === 1 && is_natural_int(reset($_GET))) {
                        if (sizeof($_POST) > 0) {
                            self::$post[$url_path][0](reset($_GET),$_POST);
                        } else {
                            self::$post[$url_path][0](reset($_GET),file_get_contents('php://input'));
                        }
                    } else {
                        http_response_code(400);
                    }
                } else {
                    http_response_code(404);
                }
                break;

            case 'PUT':
                if (array_key_exists($url_path,self::$put)) {
                    if ($url_query !== null && $url_query_clean === self::$put[$url_path][1] && sizeof($_GET) === 1 && is_natural_int(reset($_GET))) {
                        self::$put[$url_path][0](reset($_GET),file_get_contents('php://input'));
                    } else {
                        http_response_code(400);
                    }
                } else {
                    http_response_code(404);
                }
                break;

            case 'DELETE':
                if (array_key_exists($url_path,self::$delete)) {
                    if ($url_query !== null && $url_query_clean === self::$delete[$url_path][1] && sizeof($_GET) === 1 && is_natural_int(reset($_GET))) {
                        self::$delete[$url_path][0](reset($_GET));
                    } else {
                        http_response_code(400);
                    }
                } else {
                    http_response_code(404);
                }
                break;
            
            default:
                http_response_code(400);
                break;
        }
    }
}