<?php
namespace Unionity\OpenVK4\Controllers;
require_once "app/Models/User.php";
require_once "app/Models/Token.php";
require_once "app/Views/LoginView.php";
require_once "app/Views/RevokeView.php";
require_once "app/Views/SignupView.php";
use Unionity\Maximizer\HMVC\Controller;
use Unionity\OpenVK4\Models\User;
use Unionity\OpenVK4\Models\Token;
use Unionity\OpenVK4\Middlewares\Auth;
use Unionity\OpenVK4\Views\LoginView;
use Unionity\OpenVK4\Views\RevokeView;
use Unionity\OpenVK4\Views\SignupView;

class AuthController extends Controller
{
    function login()
    {
        $view = new LoginView;
        $view->render();
    }
    
    function revoke()
    {
        if($_GET["confirm"]) {
            $tokens = Token::where("user", Auth::getUser()["id"]);
            $tokens->delete();
            exit("Ваш токен был удалён!");
        }
        $view = new RevokeView;
        $view->render();
    }
    
    function signup()
    {
        $view = new SignupView;
        $view->render();
    }
    
    function unauthorize()
    {
        setcookie("uuid", null, time()-0);
        setcookie("tok", null, time()-0);
        header("HTTP/1.1 307 Redirect");
        header("Location: /");
    }
    
    function authorize()
    {
        if(!( isset($_POST["login"]) || isset($_POST["pass"]) )) {
            $view = new LoginView;
            return $view->render();
        }
        header("HTTP/1.1 302 Found");
        $user = User::where("login", $_POST["login"])->first();
        if(is_null($user)) return header("Location: /login?f=1");
        $user = $user->getAttributes();
        $hash = explode("$", $user["password_hash"]); #0 -> salted hash, 1 -> salt
        if(hash_equals($hash[0] , hash("whirlpool", $_POST["pass"].$hash[1]) )) {
            Auth::authorize($user["id"], isset($_POST["remember"]));
            return header("Location: /feed");
        } else
            return header("Location: /login?f=2");
        header("Location: /login?f=1");
    }
}
