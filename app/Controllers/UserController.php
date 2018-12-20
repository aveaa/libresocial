<?php
namespace Unionity\OpenVK4\Controllers;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Unionity\OpenVK4\Middlewares\Auth;
use Unionity\Maximizer\HMVC\Controller;
use Unionity\OpenVK4\Models\User;
use Unionity\OpenVK4\Models\Club;
use Unionity\OpenVK4\Models\Post;
use Unionity\OpenVK4\Views\UserView;
use Unionity\OpenVK4\Views\SettingsView;

class UserController extends Controller
{
    function view($user)
    {
        if($user == 0 && !is_null( Auth::getUser() )) {
            header("HTTP/1.1 308 Internal Redirect");
            exit(header("Location: /id".Auth::getUser()["id"]));
        }
        try {
            $view = new UserView(User::findOrFail($user), Post::where("target", $user)->orderBy("date", "DESC")->get());
            $view->render();
        } catch(ModelNotFoundException $ex) {
            $error = new ErrorController;
            $error->error(404, "Not Found", "Пользователь не найден");
        }
    }
    
    protected function entity($entity)
    {
        $entity = intval($entity, 0);
        if($entity < 0) {
            $entity = Club::find($entity);
        } else {
            $entity = User::find($entity);
        }
        if(!$entity) exit("ENOENT");
        return $entity;
    }
    
    function sub($id)
    {
        $entity = $this->entity($id);
        $entity->follow( Auth::getUser()["id"] );
        echo "followed";
    }
    
    function unsub($id)
    {
        $entity = $this->entity($id);
        $entity->unfollow( Auth::getUser()["id"] );
        echo "unfollowed";
    }
    
    function edit()
    {
        if($_SERVER["REQUEST_METHOD"] === "GET") {
            $view = new SettingsView;
            return $view->render();
        }
        
        $user = User::find(Auth::getUser()["id"]);
        if(isset($_POST["password"])) {
            $salt         = openssl_random_pseudo_bytes(10, $strong);
            $salt         = base64_encode($salt);
            $new_password = hash("whirlpool", $_POST["new_password"].$salt)."\$$salt";
            $hash         = explode("$", $user["password_hash"]);
            if(hash_equals( $hash[0], hash("whirlpool", $_POST["password"].$hash[1]) )) {
                $user->password_hash = $new_password;
                $user->save();
            } else {
                exit($this->redirect("?f=1", 3));
            }
        } else if(isset($_FILES["ava"])) {
            $this->setAvatar($user);
        } else {
            $user->first_name = $_POST["fn"];
            $user->last_name = $_POST["ln"];
            $user->pseudo = $_POST["pseudo"];
            $user->status = $_POST["status"];
            $user->info = $_POST["about"];
            $user->save();
        }
        header("HTTP/1.1 302 Found");
        header("Location: /id0");
    }
    
    use EntityTrait;
}
