<?php
namespace Unionity\Maximizer\Util;

class BlobVerifier
{

    private $blob;
    
    private $verified;

    function __construct($blob)
    {
        $this->blob = $blob;
    }
    
    function onVerified($callback)
    {
        return $this->verified ? $this->blob($callback) : $this;
    }
    
    function blob($callback)
    {
        $callback($this->blob);
        return $this;
    }
    
    function verify($callback)
    {
        if($callback($this->blob)) {
            $this->blob->_move();
            $this->verified = true;
        }
        return $this;
    }

}
