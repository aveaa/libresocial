<?php
namespace Unionity\OpenVK4\Views;
require_once "OpenVKView.php";
use Unionity\Maximizer\HMVC\View;

class GroupsView extends OpenVKView
{
    protected $template = "groups";
    
    function __construct($user)
    {
        $this->additional_params["user"] = $user;
        parent::__construct();
    }
}