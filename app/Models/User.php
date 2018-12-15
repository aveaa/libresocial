<?php
namespace Unionity\OpenVK4\Models;
use Illuminate\Database\Capsule\Manager as DB;

class User extends Entity
{
    protected $table = "users";
    public $timestamps = false;
    
    function getRawTargets($clubs = false)
    {
        $targets = FollowerPair::where([["target", $clubs ? "<" : ">", 0], ["follower", "=", $this->id]])->cursor();
        foreach($targets as $target) yield $target->target;
    }
    
    function getRawfollowingAttribute()
    {
        return array_diff(iterator_to_array($this->getRawTargets()), iterator_to_array($this->getRawfriendsAttribute()));
    }
    
    function getRawgroupsAttribute()
    {
        return $this->getRawTargets(true);
    }
    
    function getFollowingAttribute()
    {
        foreach($this->getRawfollowingAttribute() as $target) yield User::find($target);
    }
    
    function getGroupsAttribute()
    {
        foreach($this->getRawgroupsAttribute() as $id) yield Club::find($id);
    }
}
