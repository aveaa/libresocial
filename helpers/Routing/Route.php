<?php
namespace Unionity\Maximizer\Routing;
use Siler\Route as Router;

class Route
{
    /**
    * Internal method for injecting and invoking middlewares.
    *
    * @param array $middlewares - middleware namespace
    * @param boolean $after - invoke onAfter instead of onBefore?
    */
    protected static function _middlewares($middlewares, $after = false)
    {
        if(isset($GLOBALS["mxRMid"])) $middlewares = array_merge_recursive($GLOBALS["mxRMid"], $middlewares);
        
        foreach($middlewares as $middleware)
        {
            if(isset($GLOBALS["mxRNs"])) $middleware = $GLOBALS["mxRNs"]."\\Middlewares\\".$middleware;
            require_once "app/Middlewares/".preg_replace("/\\\(.+)\\\/", "", $middleware).".php";
            $middleware = new $middleware($_SERVER["REQUEST_URI"]);
            if($after) {
                $middleware->onAfter();
                continue;
            }
            $middleware->onBefore();
        }
    }
    
    /**
    * Internal handler proxy.
    *
    * [Args<1, 2, 3>] => Args<2, 3>
    */
    protected static function _handle($real_handler, $middlewares)
    {
        return function($args) use ($real_handler, $middlewares)
        {
            Route::_middlewares($middlewares, false);
            $args = array_splice($args, 1);
            if(gettype($real_handler) === "array") {
                $real_handler[0]->{$real_handler[1]}(...$args);
                return;
            } else {
                $real_handler(...$args);
            }
            Route::_middlewares($middlewares, true);
        };
    }
    
    /**
    * Routing method, relies on Siler and automatically includes controller files.
    * 
    * Handler syntax: <controller>@<method>
    * Allowed delimiters: #, @, ->, ❤
    *
    * @param string|array $methods - HTTP Method
    * @param string $path - path, supports regex and parameters
    * @param object|string $handler - handler
    *
    * @example Route::route("GET", "/", "IndexController❤show");
    */
    static function route($methods, $path, $handler, $middlewares = [])
    {
        if(gettype($handler) !== "string") return Router\route($methods, $path, Route::_handle($handler, $middlewares));
        
        $ctrl = preg_split("/[#@\->❤]/", $handler)[0];
        if(isset($GLOBALS["mxRNs"])) $ctrl = $GLOBALS["mxRNs"]."\\Controllers\\".$ctrl;
        $m = preg_split("/[#@\->❤]/", $handler)[1];
        require_once "app/Controllers/".preg_replace("/\\\(.+)\\\/", "", $ctrl).".php";
        $controller = new $ctrl;
        Router\route($methods, $path, Route::_handle([$controller, $m], $middlewares));
    }
    
    /***/
    static function fromDSL($file)
    {
        //TODO: create fromDSL
    }
    
    /**
    * Sets global middlewares.
    *
    * Warning: namespace is going to be called twice if present both in global scope and route.
    *
    * @param NULL|array $middlewares - middlewares
    */
    static function setGlobalMiddlewares($middlewares)
    {
        $GLOBALS["mxRMid"] = $middlewares;
    }
    
    /**
    * Sets global namespace.
    *
    * Warning: namespaces are being set for both controllers and middlewares. Global namespaces cannot be overriden.
    *
    * @param NULL|string $namespace - namespace
    */
    static function setGlobalNamespace($namespace)
    {
        $GLOBALS["mxRNs"] = $namespace;
    }
    
    use Methods, WebDAV;
}
