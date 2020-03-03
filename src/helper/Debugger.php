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
        Debug::dump($obj);
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
