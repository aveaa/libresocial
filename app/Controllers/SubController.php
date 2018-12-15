<?php
namespace Unionity\OpenVK4\Controllers;
require_once "app/Views/FriendsView.php";
require_once "app/Views/GroupsView.php";
use Unionity\OpenVK4\Middlewares\Auth;
use Unionity\Maximizer\HMVC\Controller;
use Unionity\OpenVK4\Models\User;
use Unionity\OpenVK4\Models\Club;
use Unionity\OpenVK4\Views\FriendsView;
use Unionity\OpenVK4\Views\GroupsView;

class SubController extends Controller
{
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
    
    function friends()
    {
        $view = new FriendsView(User::find(Auth::getUser()["id"]));
        $view->render();
    }
    
    function groups()
    {
        $view = new GroupsView(User::find(Auth::getUser()["id"]));
        $view->render();
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
}
