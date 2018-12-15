<?php
namespace Unionity\Maximizer\HMVC;

abstract class Middleware {
    private $uri;
    
    abstract function onBefore();
    
    abstract function onAfter();
    
    function __construct($request_uri)
    {
        $this->uri = $request_uri;
    }
}