<?php
class Config
{

    public static $config_obj = null;

    private static function load_ini()
    {
        Config::$config_obj = parse_ini_file("config.ini");
    }

    public static function get(string $key): string
    {
        if (null == Config::$config_obj) {
            Config::load_ini();
        }
        if (isset(Config::$config_obj[$key])) {
            return Config::$config_obj[$key];
        } else {
            return "";
        }
    }
}
