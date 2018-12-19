<?php 

define("frameworkNS", "Unionity\Maximizer\\");
define("appNS", "Unionity\OpenVK4\\");

spl_autoload_register(function($class)
{
    $is_fw = substr($class, 0, strlen(frameworkNS)) === frameworkNS;
    $file  = ($is_fw ? "helpers" : "app")."/".str_replace("\\", "/", str_replace($is_fw ? frameworkNS : appNS, "", $class)).".php";
    file_exists($file) ? require($file) : NULL;
}, false);
