<?php
namespace Unionity\OpenVK4\Middlewares;
use Unionity\Maximizer\HMVC\Middleware;

class ForceAuth extends Middleware
{
    function onBefore()
    {
        if(is_null( Auth::getUser() )) {
            header("HTTP/1.1 302 Found");
            header("Location: /login?f=2");
            exit;
        }
    }
    
    function onAfter() {}
}
