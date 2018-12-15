<?php
namespace Unionity\OpenVK4\Middlewares;
use Unionity\Maximizer\HMVC\Middleware;

class AntiCSRF extends Middleware
{
    function onBefore()
    {
        if($_SERVER["REQUEST_METHOD"] === "POST") {
            if(!(substr($_SERVER["HTTP_REFERER"], 0, strlen(\Config::website_url)) === \Config::website_url)) {
                header("HTTP/1.1 403 Forbidden");
                exit("<b>Security Violation:</b> Your request looked suspicious (ECSRF_DETECTED), so to protect your account we canceled it.<hr/><address>LS/1.0</address>");
            }
        }
    }
    
    function onAfter() {}
}
