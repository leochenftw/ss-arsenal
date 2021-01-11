<?php

namespace Leochenftw\Extension;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;

class ImageExtension extends DataExtension
{
    private static $db = [
      'Copyright' => 'Varchar(256)',
    ];

    public function getData($resample = 'ScaleWidth', $width = 600, $height = null)
    {
        if (!$this->owner->exists()) return null;
        // if is array, the [0] = phone, [1] = tablet, [2] = desktop
        if (is_array($width)) {
            $base_data  =   null;

            if (!empty($width) && !empty($height)) {
                $base_data  =   $this->get_base_data($resample, $width[count($width) - 1], $height[count($height) - 1]);
                if (count($width) > 0) {
                    $base_data['small'] =  $this->owner->$resample($width[0] * 2, $height[0] * 2)->getAbsoluteURL();
                }

                if (count($width) > 1) {
                    $base_data['medium'] =  $this->owner->$resample($width[1] * 2, $height[1] * 2)->getAbsoluteURL();
                }

                if (count($width) > 2) {
                    $base_data['large'] =   $base_data['url'];
                }

            } elseif (empty($width) && !empty($height)) {
                $base_data  =   $this->get_base_data($resample, null, $height[count($height) - 1]);
                if (count($width) > 0) {
                    $base_data['small'] =   $this->owner->$resample($height[0] * 2)->getAbsoluteURL();
                }

                if (count($width) > 1) {
                    $base_data['medium'] =   $this->owner->$resample($height[1] * 2)->getAbsoluteURL();
                }

                if (count($width) > 2) {
                    $base_data['large'] =   $base_data['url'];
                }

            } else {
                $base_data  =   $this->get_base_data($resample, $width[count($width) - 1]);
                if (count($width) > 0) {
                    $base_data['small'] =   $this->owner->$resample($width[0] * 2)->getAbsoluteURL();
                }

                if (count($width) > 1) {
                    $base_data['medium'] =   $this->owner->$resample($width[1] * 2)->getAbsoluteURL();
                }

                if (count($width) > 2) {
                    $base_data['large'] =   $base_data['url'];
                }

            }

            return $base_data;
        }

        return $this->get_base_data($resample, $width, $height);
    }

    private function get_base_data($resample = 'ScaleWidth', $width = 600, $height = null)
    {
        $re_height  =   !empty($height) ? $height : round($this->get_ratio($this->owner->Width, $width) * $this->owner->Height);
        return [
            'id'        =>  $this->owner->ID,
            'title'     =>  $this->owner->Title,
            'ratio'     =>  round((empty($height) ? ($this->owner->Height / $this->owner->Width) : ($height / $width)) * 10000) / 100,
            'url'       =>  empty($height) ?
                            $this->owner->$resample($width * 2)->getAbsoluteURL() :
                            (empty($width) ? $this->owner->$resample($height * 2)->getAbsoluteURL() :
                            $this->owner->$resample($width * 2, $height * 2)->getAbsoluteURL()),
            'width'     =>  $width,
            'height'    =>  $re_height,
            'copyright' =>  $this->owner->Copyright,
        ];
    }

    private function get_ratio($original, $target = 800)
    {
        if (empty($original)) return 1;
        return $target/$original;
    }
}
