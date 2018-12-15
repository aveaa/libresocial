<?php
namespace Unionity\OpenVK4\Controllers;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Unionity\OpenVK4\Middlewares\Auth;
use Unionity\Maximizer\HMVC\Controller;
use Unionity\OpenVK4\Models\Club;
use Unionity\OpenVK4\Models\Post;
use Unionity\OpenVK4\Views\ClubView;
use Unionity\OpenVK4\Views\ClubEditView;

class ClubController extends Controller
{
    protected function assert_group_ownership($clubAttributes, $error)
    {
        $uid = Auth::getUser()["id"];
        if($clubAttributes["owner"] !== $uid && !in_array($clubAttributes, json_decode($clubAttributes["coadmins"]))) {
            exit($error->error(403, "Forbidden", "Недостаточно прав"));
        }
    }
    
    function view($club)
    {
        $club = -1 * abs($club);
        try {
            $view = new ClubView(Club::findOrFail($club), Post::where("target", $club)->orderBy("date", "DESC")->cursor());
            $view->render();
        } catch(ModelNotFoundException $ex) {
            $error = new ErrorController;
            $error->error(404, "Not Found", "Группа не найдена");
        }
    }
    
    function edit()
    {
        $error = new ErrorController;
        if(isset($_GET["id"])) {
            try {
                $club = Club::findOrFail(-1 * abs($_GET["id"]));
                $this->assert_group_ownership($club, $error);
            } catch(ModelNotFoundException $ex) {
                $error->error(404, "Not Found", "Группа не найдена");
            }
        } else if($_GET["act"] === "new") {
            $club = new Club;
            $club->id        = -(Club::count() + 1);
            $club->owner     = Auth::getUser()["id"];
            $club->about     = json_encode([]);
            $club->coadmins  = json_encode([]);
            $club->verified  = "no";
        }
        
        if(isset($_FILES["ava"])) {
            $this->setAvatar($club);
        } else {
            $club->name   = $_POST["name"];
            $club->status = $_POST["status"];
            $club->info   = $_POST["about"];
        }
        $club->save();
        $_GET["act"] === "new" ? $club->follow(Auth::getUser()["id"]) : ''; #follow created group
        $this->redirect("/public".abs($club->id));
    }
    
    function control()
    {
        $error = new ErrorController;
        if(isset($_GET["id"])) {
            $club = Club::find(-1 * abs($_GET["id"]));
            if(!is_null($club)) {
                $this->assert_group_ownership($club, $error);
                $view = new ClubEditView($club);
                $view->render();
            } else {
                $error->error(404, "Not Found", "Группа не найдена");
            }
        } else if($_GET["act"] === "new") {
                $view = new ClubEditView("new");
                $view->render();
        } else {
            $error->error(400, "Bad Request", "Не указана группа");
        }
    }
    
    use EntityTrait;
}
