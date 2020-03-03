<?php

namespace Leochenftw\Extension;

use SilverStripe\ORM\DataExtension;

class DataListExtension extends DataExtension
{
    public function getTileData($param = null)
    {
        $result         =   [];
        foreach ($this->owner as $item) {
            if (!is_null($param)) {
                if (!is_array($param)) {
                    $result[]   =   $item->getTileData($param);
                } else {
                    $result[]   =   call_user_func_array([$item, 'getTileData'], $param);
                }
            } else {
                $result[]   =   $item->getTileData();
            }
        }

        return $result;
    }

    public function getMiniData($param = null)
    {
        $result         =   [];
        foreach ($this->owner as $item) {
            if (!is_null($param)) {
                if (!is_array($param)) {
                    $result[]   =   $item->getMiniData($param);
                } else {
                    $result[]   =   call_user_func_array([$item, 'getMiniData'], $param);
                }
            } else {
                $result[]   =   $item->getMiniData();
            }
        }

        return $result;
    }

    public function getData($param = null)
    {
        $result         =   [];
        foreach ($this->owner as $item) {
            if (!is_null($param)) {
                if (!is_array($param)) {
                    $result[]   =   $item->getData($param);
                } else {
                    $result[]   =   call_user_func_array([$item, 'getData'], $param);
                }
            } else {
                $result[]   =   $item->getData();
            }
        }

        return $result;
    }
}
