<?php
namespace Unionity\OpenVK4\Views;
require_once "OpenVKView.php";
use Unionity\Maximizer\HMVC\View;

class ClubView extends OpenVKView
{
    protected $template = "club";
    
    function __construct($club, $posts)
    {
        $this->additional_params["club"] = $club;
        $this->additional_params["posts"] = $posts;
        parent::__construct();
    }
}