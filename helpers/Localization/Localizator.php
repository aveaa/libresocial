<?php
namespace Unionity\Maximizer\Localization;

class Localizator
{

    protected static function parse($file)
    {
        if(isset($GLOBALS["localizationCache"])) return $GLOBALS["localizationCache"];
        $string = file_get_contents($file);
        $array  = [];
        foreach(explode(";\n", $string) as $statement) {
            $s = explode(" = ", $statement);
            $array[eval("return $s[0];")] = eval("return $s[1];");
        }
        $GLOBALS["localizationCache"] = $array;
        return $array;
    }

    static function _($id, $lang = NULL)
    {
        $lang  = is_null($lang) ? \Config::default_lang : $lang;
        $array = @self::parse(\Config::locales."/".\Config::default_lang.".strings");
        return $array[$id];
    }

}
