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
            $hash = hash("whirlpool", $_POST["new_password"]);
            if(hash_equals( $user["password_hash"], hash("whirlpool", $_POST["password"]) )) {
                $user->password_hash = $hash;
                $user->save();
            } else {
                //TODO: Bad password
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
