<?php
namespace Unionity\OpenVK4\Models;
use Illuminate\Database\Eloquent\Model;

class Avatar extends Model
{
    protected $table = "avatars";
    protected $primaryKey = "owner";
    public $incrementing = false;
    public $timestamps = false;
    public $fillable = ["owner", "filename", "filename_optimized", "filename_min"];
}
 
