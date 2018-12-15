<?php
namespace Unionity\Maximizer\HMVC;

/**
* View base class, must be extended first.
*/
class View
{
    /**
    * @var array base template parameters. Useful for creating another base view.
    */
    protected $params = [];
    
    /**
    * @var array additional parameters. Useful for creating children views.
    */
    protected $additional_params = [];
    
    /**
    * @var string template name, will be used to lookup template file.
    * @example index
    */
    protected $template = "";
    
    function __construct($base_dir = "app/templates/") {
        $this->base = $base_dir;
        $this->params = array_merge_recursive($this->params, $this->additional_params);
    }
    
    /**
    * Rendering method.
    * @param boolean $return - return instead of outputting.
    */
    function render($return = false)
    {
        $latte = new \Latte\Engine;
        $latte->addFilter("l", function($s) {
            return \Unionity\Maximizer\Localization\Localizator::_($s);
        });
        $latte->setTempDirectory('cache/templates');
        $p = [$this->base.$this->template.".latte", $this->params];
        return $return ? $latte->renderToString(...$p) : $latte->render(...$p);
    }
}
