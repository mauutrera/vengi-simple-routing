<?php

namespace Vengi;

class Route
{
    public static function view($route,$function)
    {
        if ($_SERVER['REQUEST_URI'] === $route) {
            return $function();
        }
    }

    public static $get = [];
    public static $post = [];
    public static $put = [];
    public static $delete = [];
    
    public static function get($route,$class,$method)
    {
        $url_route = parse_url($route);
        self::$get[$url_route['path']][0]="$class::$method";
        self::$get[$url_route['path']][1]=isset($url_route['query']) ? $url_route['query'] : null;

        if (!str_ends_with($url_route['path'],'/')) {
            self::$get[$url_route['path'].'/'][0]="$class::$method";
            self::$get[$url_route['path'].'/'][1]=isset($url_route['query']) ? $url_route['query'] : null;
        }
    }
    
    public static function post($route,$class,$method)
    {
        $url_route = parse_url($route);
        self::$post[$url_route['path']][0]="$class::$method";
        self::$post[$url_route['path']][1]=isset($url_route['query']) ? $url_route['query'] : null;
    }
    
    public static function put($route,$class,$method)
    {
        $url_route = parse_url($route);
        self::$put[$url_route['path']][0]="$class::$method";
        self::$put[$url_route['path']][1]=isset($url_route['query']) ? $url_route['query'] : null;
    }

    public static function delete($route,$class,$method)
    {
        $url_route = parse_url($route);
        self::$delete[$url_route['path']][0]="$class::$method";
        self::$delete[$url_route['path']][1]=isset($url_route['query']) ? $url_route['query'] : null;
    }

    public static function run()
    {
        $url = parse_url($_SERVER['REQUEST_URI']);
        $url_path = $url['path'];
        $url_query = isset($url['query']) ? $url['query'] : null;
        $url_query_clean = explode('=',$url_query,2)[0];

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