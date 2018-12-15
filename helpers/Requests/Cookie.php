<?php
namespace Unionity\Maximizer\Requests;

class Cookie {
    
    protected static function encodeCookie($cookie)
    {
        $salt   = bin2hex(random_bytes(8));
        $object = convert_uuencode(serialize($object));
        $digest = hash("adler32", $object.$salt);
        return rawurlencode($object.$digest.$salt);
    }
    
    protected static function decodeCookie($cookie)
    {
        $cookie  = rawurldecode($cookie);
        $salt    = substr($cookie, -1, 16);
        $digest  = substr($cookie, -16, 8);
        $content = substr($cookie, -24);
        if(hash_equals($digest, hash("adler32", $content.$salt))) {
            try {
                return unserialize(convert_uudecode($content));
            } catch(\Exception $e) {
                return NULL;
            }            
        }
        return NULL;
    }
    
    static function exists($name)
    {
        return isset($_COOKIE[$name]);
    }
    
    static function get($name)
    {
        if(!exists($name)) return NULL;
        return Cookie::decodeCookie($_COOKIE[$name]);
    }
    
    static function invalid($name)
    {
        return Cookie::exists($name) && is_null(Cookie::get($name));
    }
    
    static function valid($name)
    {
        return !Cookie::invalid($name);
    }
    
    static function set($name, $val, $opts)
    {
        return setcookie($name, Cookie::encodeCookie($val), $opts);
    }
    
    static function del($name)
    {
        return setcookie($name, "", time()*-1);
    }
    
    // static function unset($name)
    // {
        // return Cookie::del($name);
    // }
    
    static function rm($name)
    {
        return Cookie::del($name);
    }
    
    static function destroy($name)
    {
        return Cookie::del($name);
    }
    
    static function delete($name)
    {
        return Cookie::del($name);
    }
}