<?php
namespace Unionity\OpenVK4\Controllers;
require_once "app/Views/IndexView.php";
use Unionity\Maximizer\HMVC\Controller;
use Unionity\OpenVK4\Views\IndexView;

class IndexController extends Controller
{
    function index()
    {
        $view = new IndexView;
        $view->render();
    }
}