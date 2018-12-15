<?php
namespace Unionity\OpenVK4\Models;
require_once "app/Models/Postable.php";

class Comment extends Postable
{
    protected $table = "comments";
    public $timestamps = false;
}
