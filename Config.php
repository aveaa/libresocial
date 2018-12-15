<?php

class Config
{
    const website_name = NULL;
    const website_url  = NULL;
    const skin         = NULL; #should be NULL, if you're not using a skin
    
    const database = [
      "driver"    => NULL, #e.g, mysql
      "host"      => NULL,
      "database"  => NULL,
      "username"  => NULL,
      "password"  => NULL,
      "charset"   => "utf8",
      "collation" => "utf8_unicode_ci",
      "prefix"    => "",
    ];
    
    const locales      = "locales";
    const default_lang = "en-US"; #Want to know some examples? Check out locales folder.
}
