<?php
namespace Unionity\OpenVK4\Models;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    function avatar()
    {
        return $this->hasOne("Unionity\OpenVK4\Models\Avatar", "owner", "id")->withDefault([
            "owner"              => 0,
            "filename"           => "storage/undefined.jpeg?ava=true",
            "filename_optimized" => "storage/undefined.jpeg?ava=true",
            "filename_min"       => "storage/undefined.jpeg?ava=true",
        ]);
    }

    function follow($as)
    {
        if(FollowerPair::where([["follower", "=", $as], ["target", "=", $this->getAttributes()["id"]]])->exists()) exit("ESTATECHANGED");
        $pair = new FollowerPair;
        $pair->follower = $as;
        $pair->target   = $this->getAttributes()["id"];
        $pair->save();
    }
    
    function unfollow($as)
    {
        $pair = FollowerPair::where([["follower", "=", $as], ["target", "=", $this->getAttributes()["id"]]])->delete();
    }
    
    function getRawfriendsAttribute()
    {
        $query = "SELECT B.follower AS id FROM followers AS A INNER JOIN followers AS B on A.follower = B.target";
        $query .= " WHERE A.follower = B.target AND B.follower = A.target AND B.target = ?";
        $friends = DB::select($query, [$this->attributes["id"]]);
        foreach($friends as $friend) yield $friend->id;
    }
    
    function getFriendsAttribute()
    {
        foreach($this->getRawfriendsAttribute() as $id) yield User::find($id);
    }
    
    function befriend($as) { $this::follow($as); }
    function unfriend($as) { $this::unfollow($as); }
    
    //This method returns Array, but not iterator. Keep this in mind please.
    function getRawfollowersAttribute()
    {
        $real_followers = [];
        $followers = DB::select("SELECT follower AS id FROM followers WHERE target = ?", [$this->attributes["id"]]);
        foreach($followers as $follower) $real_followers []= $follower->id;
        
        return array_diff($real_followers, iterator_to_array($this->getRawfriendsAttribute()));
    }
    
    function getFollowersAttribute()
    {
        foreach($this->getRawfollowersAttribute() as $follower) yield User::find($follower);
    }
    
    function getDespacitoAttribute()
    {
        return !!rand(0, 1);
    }
}
