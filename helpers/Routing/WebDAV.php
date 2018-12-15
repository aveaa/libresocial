<?php
namespace Unionity\Maximizer\Routing;

trait WebDAV
{
    /**
    * Route a MOVE request.
    * Synonym of Route::route("MOVE", $path, $handler);
    *
    * @see Route::route
    */
    static function move($path, $handler)
    {
        Route::route("MOVE", $path, $handler);
    }

    /**
    * Route a LOCK request.
    * Synonym of Route::route("LOCK", $path, $handler);
    *
    * @see Route::route
    */
    static function lock($path, $handler)
    {
        Route::route("LOCK", $path, $handler);
    }

    /**
    * Route a COPY request.
    * Synonym of Route::route("COPY", $path, $handler);
    *
    * @see Route::route
    */
    static function copy($path, $handler)
    {
        Route::route("COPY", $path, $handler);
    }

    /**
    * Route a MKCOL request.
    * Synonym of Route::route("MKCOL", $path, $handler);
    *
    * @see Route::route
    */
    static function mkcol($path, $handler)
    {
        Route::route("MKCOL", $path, $handler);
    }

    /**
    * Route an UNLOCK request.
    * Synonym of Route::route("UNLOCK", $path, $handler);
    *
    * @see Route::route
    */
    static function unlock($path, $handler)
    {
        Route::route("UNLOCK", $path, $handler);
    }

    /**
    * Route a PROPFIND request.
    * Synonym of Route::route("PROPFIND", $path, $handler);
    *
    * @see Route::route
    */
    static function propfind($path, $handler)
    {
        Route::route("PROPFIND", $path, $handler);
    }

    /**
    * Route a PROPPATCH request.
    * Synonym of Route::route("PROPPATCH", $path, $handler);
    *
    * @see Route::route
    */
    static function proppatch($path, $handler)
    {
        Route::route("PROPPATCH", $path, $handler);
    }
}