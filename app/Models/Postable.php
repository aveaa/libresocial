<?php
namespace Unionity\OpenVK4\Models;
require_once "app/Models/Comment.php";
require_once "app/Models/Club.php";
use Illuminate\Database\Eloquent\Model;

class Postable extends Model
{
    function author()
    {
        $type = $this->getAttributes()["owner"] > 0 ? "User" : "Club";
        return $this->hasOne("Unionity\OpenVK4\Models\\".$type, "id", "owner");
    }
    
    function isOwner($id)
    {
        $author = $this->author;
        if($author instanceof Club) {
            $author = $author->getAttributes();
            if($author["owner"] == $id)
                return true;
            else
                return in_array($id, json_decode($author["coadmins"]));
        } else {
            return $author->getAttributes()["id"] == $id;
        }
    }
    
    static function like($id, $as)
    {
        $post = self::find($id);
        $likes = json_decode($post->getAttributes()["liked_by"], true);
        if(in_array($as, $likes)) {
            $key = array_search($as, $likes);
            unset($likes[$key]);
            $likes = array_values($likes);
            $j = !1;
        } else {
            array_unshift($likes, $as);
            $j = true;
        }
        $post->liked_by = json_encode($likes);
        $post->save();
        return $j;
    }
    
    function comments()
    {
        return $this->morphMany('Unionity\OpenVK4\Models\Comment', 'commentable')->orderBy("date", "asc");
    }
    
    function setContentAttribute($val)
    {
        $val = preg_replace(["%(\<)%", "%(\>)%", "%(\&)%", "%(\")%"], ["&lt;", "&gt;", "&amp;", "&quot;"], $val);
        $val = preg_replace([
            "%(\*|@)id([0-9]+) \(([^\(\)]+)\)%iuJs",
            "%(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@|\d{1,3}(?:\.\d{1,3}){3}|(?:(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)(?:\.(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)*(?:\.[a-z\x{00a1}-\x{ffff}]{2,6}))(?::\d+)?(?:[^\s]*)?%iuJ",
        ], [
            "<a href=\"/id$2\">$3</a>",
            "<a href=\"$0\" rel=\"nofollow\">$0</a>",
        ], $val);
        
        $this->attributes["content"] = $val;
    }
}
