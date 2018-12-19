<?php
namespace Unionity\OpenVK4\Controllers;
use Unionity\Maximizer\Util\Blob;
use Unionity\OpenVK4\Models\Avatar;

trait EntityTrait
{
    private function setAvatar($entity)
    {
        $blob = new Blob($_FILES["ava"]);
        $blob->mv()->verify(function($j){return true;})->onVerified(function($blob) use ($entity) {
            $filename = explode(".", $blob->getDest())[0];
            $image    = new \Gmagick($blob->getDest());
            $image->thumbnailimage(768, 0);
            $image->write("$filename.768.jpeg");
            $image->cropthumbnailimage(128, 128);
            $image->write("$filename.128.jpeg");
            
            Avatar::updateOrCreate(["owner" => $entity["id"]], [
                "filename" => $blob->getDest(),
                "filename_optimized" => "$filename.768.jpeg",
                "filename_min" => "$filename.128.jpeg",
            ]);
        });
        return true;
    }
}
