<?php
namespace Unionity\OpenVK4\Controllers;
use Unionity\Maximizer\Util\Blob;
use Unionity\OpenVK4\Models\Avatar;

trait EntityTrait
{
    private function getCoordinates($image)
    {
        $resolution = [$image->getimagewidth(), $image->getimageheight()];
        $fallback   = [0, 0, $resolution[0], $resolution[1]];
        if(!( isset($_POST["crop_x"]) && isset($_POST["crop_y"]) && isset($_POST["crop_w"]) && isset($_POST["crop_h"]) ))
            return $fallback;
        else if($_POST["crop_x"] < 0 || $_POST["crop_y"] < 0 || $_POST["crop_w"] < 0 || $_POST["crop_h"] < 0)
            return $fallback;
        
        return [
            min($_POST["crop_w"], $resolution[0] - $_POST["crop_x"]),
            min($_POST["crop_h"], $resolution[1] - $_POST["crop_y"]),
            min($_POST["crop_x"], $resolution[0]),
            min($_POST["crop_y"], $resolution[1]),           
        ];
    }

    private function setAvatar($entity)
    {
        $blob = new Blob($_FILES["ava"]);
        $blob->mv()->verify(function($j){return true;})->onVerified(function($blob) use ($entity) {
            $filename = explode(".", $blob->getDest())[0];
            $dirname  = "$filename.d";
            $filename = explode("/", $filename);
            $filename = $filename[sizeof($filename)];
            if(!is_dir($dirname)) mkdir($dirname);
            $image    = new \Gmagick($blob->getDest());
            $image->cropimage(...$this->getCoordinates($image));
            $image->thumbnailimage(768, 0);
            $image->write("$dirname/2b0ba183cd6e6085989a6795998552d3.jpeg");
            $image->thumbnailimage(128, 128);
            $image->write("$dirname/803456eac15335fa58004fa7fa1ffb3c.jpeg");

            Avatar::updateOrCreate(["owner" => $entity["id"]], [
                "filename" => $blob->getDest(),
                "filename_optimized" => "$dirname/2b0ba183cd6e6085989a6795998552d3.jpeg",
                "filename_min" => "$dirname/803456eac15335fa58004fa7fa1ffb3c.jpeg",
            ]);
        });
        return true;
    }
}
