<?php
namespace Unionity\OpenVK4\Views;
require_once "OpenVKView.php";
use Unionity\Maximizer\HMVC\View;

class FriendsView extends OpenVKView
{
    protected $template = "friends";
    
    function __construct($user)
    {
        $this->additional_params["user"] = $user;
        parent::__construct();
    }
}