<?php
namespace Unionity\OpenVK4\Controllers;
use Unionity\OpenVK4\Middlewares\Auth;
use Unionity\Maximizer\HMVC\Controller;
use Unionity\OpenVK4\Models\Post;
use Unionity\OpenVK4\Models\Comment;
use Unionity\OpenVK4\Models\Club;
use Unionity\OpenVK4\Models\FollowerPair;

class PostController extends Controller
{

    const TYPES = [
        "p" => "Post",
        "c" => "Comment",
        "i" => "Photo",
    ];

    protected function getTargets($id)
    {
        return FollowerPair::where("follower", $id)->get();
    }

    protected function getFeedPosts($id)
    {
        $targets = $this->getTargets($id);
        $posts   = Post::where("target", 0);
        foreach($targets as $target) {
            $target = $target->getAttributes()["target"];
            $posts->orWhere([["owner", "=", $target], ["target", "=", $target]]);
        }
        
        return $posts->orderBy("date", "DESC")->get()->forPage(abs($_GET["page"] ?? 1), 20)->all();
    }

    protected function getModelName($type)
    {
        return "Unionity\OpenVK4\Models\\".(isset(self::TYPES[$type]) ? self::TYPES[$type] : "Post");
    }

    protected function getPost($type, $id)
    {
        try {
            $class = $this->getModelName($type);
            return $class::findOrFail($id);
        } catch(\Exception $ex) {}
    }
    
    protected function assertPostOwnership($post)
    {
        if(!$post->isOwner(Auth::getUser()["id"])) {
            header("HTTP/1.1 403 Forbidden");
            exit("EACCESS");
        }
    }

    function feed()
    {
        $posts = $this->getFeedPosts(Auth::getUser()["id"]);
        $this->v("Unionity\OpenVK4\Views\NewsView", [$posts]);
    }

    function post()
    {
        $as_g = false;
        if($_POST["as_club"] === "on" && $_POST["wall"] < 0) {
            $club = Club::findOrFail($_POST["wall"])->getAttributes();
            $as_g = $club['owner'] === Auth::getUser()['id'] || in_array(Auth::getUser()['id'], json_decode($club['coadmins']));
        }
        
        $post              = new Post;
        $post->owner       = $as_g ? $_POST["wall"] : Auth::getUser()["id"];
        $post->liked_by    = "[]";
        $post->attachments = "[]";
        $post->content     = $_POST["post"];
        $post->target      = $_POST["wall"];
        $post->save();
        
        header("HTTP/1.1 302 Found");
        header("Location: ".($_POST["wall"] > 0 ? "id" : "public").abs($_POST["wall"]));
    }

    function edit($type, $id)
    {
        $post = $this->getPost($type, $id);
        $this->assertPostOwnership($post);
        
        $post->content = $_POST["post"];
        $post->save();
    }

    function remove($type, $id)
    {
        $post = $this->getPost($type, $id);
        $this->assertPostOwnership($post);
        
        $post->delete();
        exit("deleted");
    }
    
    function like($type, $id)
    {
        $post = $this->getModelName($type);
        
        echo $post::like($id, Auth::getUser()["id"]) ? "liked" : "disliked";
    }
    
    function comment($post)
    {
        $comment                   = new Comment;
        $comment->owner            = Auth::getUser()["id"];
        $comment->commentable_id   = $post;
        $comment->commentable_type = "Unionity\OpenVK4\Models\Post";
        $comment->liked_by         = "[]";
        $comment->content          = $_POST["post"];
        $comment->save();
        header("HTTP/1.1 302 Found");
        header("Location: ".$_SERVER["HTTP_REFERER"]);
    }
}
