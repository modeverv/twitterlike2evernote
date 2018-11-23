<?php
class Parameter
{
    public static function getString(string $key) :?string
    {
        $value = Parameter::get($key);
        return null == $value ? null : (string)$value;
    }
    public static function getInteger(string $key) :?integer
    {
        $value = Parameter::get($key);
        return null == $value ? null : (integer)$value;
    }
    public static function getArray(string $key) :?array
    {
        $value = Parameter::get($key);
        return null == $value ? null : (array)$value;
    }
    private static function get(string $key)
    {
        if(isset($_REQUEST[$key])){
            return $_REQUEST[$key];
        }else{
            return null;
        }
    }
}
