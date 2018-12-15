<?php
namespace Unionity\OpenVK4\Views;
require_once "OpenVKView.php";
use Unionity\Maximizer\HMVC\View;

class UserView extends OpenVKView
{
    protected $template = "user";
    
    function __construct($user, $posts)
    {
        $this->additional_params["user"] = $user;
        $this->additional_params["posts"] = $posts;
        parent::__construct();
    }
}