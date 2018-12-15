<?php
namespace Unionity\Maximizer\Routing;

trait Methods
{
    /**
    * Route a GET request.
    * Synonym of Route::route("GET", $path, $handler);
    *
    * @see Route::route
    */
    static function get($path, $handler, $middlewares = [])
    {
        Route::route("GET", $path, $handler, $middlewares);
    }
    
    /**
    * Route a POST request.
    * Synonym of Route::route("POST", $path, $handler);
    *
    * @see Route::route
    */
    static function post($path, $handler)
    {
        Route::route("POST", $path, $handler);
    }

    /**
    * Route a PUT request.
    * Synonym of Route::route("PUT", $path, $handler);
    *
    * @see Route::route
    */
    static function put($path, $handler)
    {
        Route::route("PUT", $path, $handler);
    }

    /**
    * Route a PATCH request.
    * Synonym of Route::route("PATCH", $path, $handler);
    *
    * @see Route::route
    */
    static function patch($path, $handler)
    {
        Route::route("PATCH", $path, $handler);
    }

    /**
    * Route a DELETE request.
    * Synonym of Route::route("DELETE", $path, $handler);
    *
    * @see Route::route
    */
    static function delete($path, $handler)
    {
        Route::route("DELETE", $path, $handler);
    }

    /**
    * Route an OPTIONS request.
    * Synonym of Route::route("OPTIONS", $path, $handler);
    *
    * @see Route::route
    */
    static function options($path, $handler)
    {
        Route::route("OPTIONS", $path, $handler);
    }
}