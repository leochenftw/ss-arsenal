<?php

namespace Leochenftw\Extension;

use SilverStripe\ORM\DataExtension;

class DataListExtension extends DataExtension
{
    public function getTileData($param = null)
    {
        return array_map(function($item) use ($param) {
            if (!is_null($param)) {
                if (!is_array($param)) {
                    return $item->getTileData($param);
                }

                return call_user_func_array([$item, 'getTileData'], $param);
            }

            return $item->TileData;

        }, $this->owner->toArray());
    }

    public function getMiniData($param = null)
    {
      return array_map(function($item) use ($param) {
          if (!is_null($param)) {
              if (!is_array($param)) {
                  return $item->getMiniData($param);
              }

              return call_user_func_array([$item, 'getMiniData'], $param);
          }

          return $item->MiniData;

      }, $this->owner->toArray());
    }

    public function getData($param = null)
    {
      return array_map(function($item) use ($param) {
          if (!is_null($param)) {
              if (!is_array($param)) {
                  return $item->getData($param);
              }

              return call_user_func_array([$item, 'getData'], $param);
          }

          return $item->Data;

      }, $this->owner->toArray());
    }
}
