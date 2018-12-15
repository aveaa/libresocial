<?php
/**
* WARNING! This file is deprecated and is here for historical purpose!
* Avoid using this mechanism in your modules, as it is potentially unsafe and error prone.
* Use Entity->avatar instead.
*/
$id = rawurlencode($_GET["uuid"]); #does actually protect!
$file = "$id.jpeg";
$file = isset($_GET["size"]) ? "$id.".$_GET["size"].".jpeg" : $file;

header("Content-Type: image/jpeg");
if(file_exists($file)) {
    header("ETag: W/\"".hash_file("sha512", $file)."\"");
    exit(readfile($file));
}
header("HTTP/1.1 410 Gone");
header("ETag: W/droplet410201821842033");
readfile("../undefined.jpeg");
