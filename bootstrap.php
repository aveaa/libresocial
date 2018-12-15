<?php

if(
    preg_match("/\.~particle/", $_SERVER["REQUEST_URI"])
      ||
    isset($_SERVER["HTTP_X_MXZR_PARTICLE"])
) {
    $_SERVER["REQUEST_URI"] = preg_replace("/\.~particle/", "", $_SERVER["REQUEST_URI"]);
    $GLOBALS["particleRequested"] = 1;
}

require "Config.php";
require "bootstrap_db.php";
require "app/autoload.php";
require "app/routes/web.php";
