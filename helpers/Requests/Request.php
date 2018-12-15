<?php
namespace Unionity\Maximizer\Requests;

class Request {
    
    static function params()
    {
        return (object) ["get" => $_GET, "post" => $_POST];
    }
    
}