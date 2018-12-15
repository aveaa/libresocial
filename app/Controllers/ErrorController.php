<?php
namespace Unionity\OpenVK4\Controllers;
require_once "app/Views/ErrorView.php";
use Unionity\Maximizer\HMVC\Controller;
use Unionity\OpenVK4\Views\ErrorView;

class ErrorController extends Controller
{
    function error($code, $title, $details = null)
    {
        header("HTTP/1.1 $code $title");
        $view = new ErrorView($code, $title, $details);
        $view->render();
    }
}