<?php
namespace Unionity\Maximizer\HMVC;

class Controller {
    /**
    * Performs particle-awared redirect.
    * @param string $to - redirect URI.
    * @param int $code - redirect status code, should be less than 99.
    * @throws warning, if code is incorrect.
    */
    protected function redirect($to, $code = 2)
    {
        if($code > 99 || $code < 1) return trigger_error("Invalid status code", E_USER_WARNING);
        $code = 300 + $code;
        $to = $to.(isset($GLOBALS["particleRequested"]) ? ".~particle" : "");
        header("HTTP/1.1 $code Redirect");
        header("Location: $to");
    }
    
    /**
    * View creation macro.
    * @param string $class - View class name.
    * @param array $params - array of arguments, that should be passed to constructor.
    * @param boolean $return - return result instead of printing it.
    * @returns NULL|string - result, if $return is true.
    */
    protected function v($class, $params = [], $return = false)
    {
        $view = new $class(...$params);
        return $view->render($return);
    }
}
