<?php
namespace Unionity\OpenVK4\Views;
require_once "OpenVKView.php";
use Unionity\Maximizer\HMVC\View;

class NewsView extends OpenVKView
{
    protected $template = "newsfeed";
    
    function __construct($posts)
    {
        $this->additional_params["posts"] = $posts;
        parent::__construct();
    }
} 
