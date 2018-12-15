<?php
namespace Unionity\OpenVK4\Views;
require_once "OpenVKView.php";
use Unionity\Maximizer\HMVC\View;

class ClubEditView extends OpenVKView
{
    protected $template = "cedit";
    
    function __construct($club)
    {
        $this->additional_params["club"] = $club;
        parent::__construct();
    }
}