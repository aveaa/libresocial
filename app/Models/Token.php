<?php
namespace Unionity\OpenVK4\Models;
use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    protected $table = "tokens";
    public $timestamps = false;
    
    /**
    * Generates token
    */
    private static function _generate()
    {
        $strong = true;
        $token  = openssl_random_pseudo_bytes(64, $strong);
        $token  = base64_encode($token);
        return  $token;
    }
    
    static function getToken($user, $ip)
    {
        $token = Token::where([
            "user" => $user,
            "ip" => $ip,
        ])->first();
        if(is_null($token)) {
            $tok_str      = Token::_generate();
            $token        = new Token;
            $token->user  = $user;
            $token->ip    = $ip;
            $token->token = $tok_str;
            $token->save();
        } else {
            $token   = $token->getAttributes();
            $tok_str = $token["token"];
        }
        
        return $tok_str;
    }
}