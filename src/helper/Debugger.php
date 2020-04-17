<?php
/**
 * @file Debugger
 *
 * Debugging functions
 * */
namespace Leochenftw;

use SilverStripe\Dev\Debug;

class Debugger
{
    public static function inspect($obj, $die = true)
    {
        if (is_array($obj) || is_object($obj)) {
            print '<pre>';
            print_r($obj);
            print '</pre>';
        } else {
            Debug::show($obj);
        }

        if ($die) {
            die;
        }
    }

    public static function methods(&$obj)
    {
        if (!empty($obj)) {
            Debug::dump(get_class_methods($obj));
        } else {
            echo 'object is empty';
        }
        die;
    }

    public static function isRecursive($array) {
        foreach($array as $v) {
            if($v === $array) {
                return true;
            }
        }
        return false;
    }

    public static function props(&$obj)
    {
        if (!empty($obj)) {
            $list   =   get_object_vars($obj);
            $props  =   [];
            foreach ($list as $key => $item) {
                $props[$key]    =   gettype($item);
            }
            Debug::dump($props);
        } else {
            echo 'object is empty';
        }
        die;
    }
}
