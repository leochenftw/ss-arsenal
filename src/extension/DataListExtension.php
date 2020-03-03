<?php

namespace Leochenftw\Extension;

use SilverStripe\ORM\DataExtension;

class DataListExtension extends DataExtension
{
    public function getTileData($param = null)
    {
        $result         =   [];
        foreach ($this->owner as $item) {
            $result[]   =   !is_null($param) ? $item->getTileData($param) : $item->getTileData();
        }

        return $result;
    }

    public function getMiniData($param = null)
    {
        $result         =   [];
        foreach ($this->owner as $item) {
            $result[]   =   !is_null($param) ? $item->getMiniData($param) : $item->getMiniData();
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
