<?php
namespace Unionity\OpenVK4\Middlewares;
require_once "app/Models/Token.php";
require_once "app/Models/User.php";
use Unionity\Maximizer\HMVC\Middleware;
use Unionity\OpenVK4\Models\Token;
use Unionity\OpenVK4\Models\User;

class Auth extends Middleware
{
    function onBefore()
    {
        if(isset($_COOKIE["tok"]) && isset($_COOKIE["uuid"])) {
            $token = Token::where([
                "user" => $_COOKIE["uuid"],
                "ip" => "".crc32($_SERVER["REMOTE_ADDR"]),
                "token" => $_COOKIE["tok"],
            ])->first();
            if(is_null($token)) {
                $GLOBALS["OpenVK4:authenticated_user"] = null;
            } else {
                $user = User::find($_COOKIE["uuid"]);
                $GLOBALS["OpenVK4:authenticated_user"] = $user;
            }
        } else {
            $GLOBALS["OpenVK4:authenticated_user"] = null;
        }
    }
    
    function onAfter() {}
    
    /**
    * Authorizes user by id.
    * Warning: does not check for user existance!
    *
    * @param int $user - user id.
    * @param boolean $remember - set cookie expiration date to 1 year.
    */
    static function authorize($user, $remember)
    {
        $token = Token::getToken($user, crc32($_SERVER["REMOTE_ADDR"]));
        
        $cookie_params = [
            $remember ? time()+60*60*24*30*12 : NULL,
            "/",
            NULL,
            false,
            true
        ];
        
        setcookie("uuid", $user, ...$cookie_params);
        setcookie("tok", $token, ...$cookie_params);
    }
    
    static function getUser($full = false)
    {
        $u = $GLOBALS["OpenVK4:authenticated_user"];
        return $full ? $u : (is_null($u) ? $u : $u->getAttributes());
    }
    
    function __invoke()
    {
        return Auth::getUser();
    }
    
    /**
    * Cheks whether user was substituted or not.
    */
    static function isUserTainted()
    {
        return false;
    }
}
