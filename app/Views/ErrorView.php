<?php
namespace Unionity\OpenVK4\Views;
require_once "OpenVKView.php";
use Unionity\Maximizer\HMVC\View;

class ErrorView extends OpenVKView
{
    protected $template = "error";
    
    function __construct($code, $title, $details)
    {
        $this->additional_params["error"] = [
            "code" => $code,
            "desc" => $title,
            "details" => $details,
        ];
        parent::__construct();
    }
}