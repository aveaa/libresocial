<?php
namespace Unionity\OpenVK4\Models;
require_once "app/Models/Postable.php";

class Post extends Postable
{
    protected $table = "posts";
    public $timestamps = false;
}
