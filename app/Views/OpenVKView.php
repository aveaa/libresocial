<?php
namespace Unionity\OpenVK4\Views;
use Unionity\Maximizer\HMVC\View;

class OpenVKView extends View
{
    protected $params = [
      "website_name" => \Config::website_name,
    ];
    
    function __construct()
    {
        $this->params["logged_user"]     = \Unionity\OpenVK4\Middlewares\Auth::getUser();
        $this->params["obj_logged_user"] = \Unionity\OpenVK4\Middlewares\Auth::getUser(true);
        parent::__construct(NULL !== \Config::skin ? "skins/".\Config::skin."/" : "app/templates/");
    }
}