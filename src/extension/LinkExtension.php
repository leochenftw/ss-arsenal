<?php

namespace Leochenftw\Extension;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;

class LinkExtension extends DataExtension
{
    public function getData()
    {
        if (!$this->owner->exists()) return null;
        $data = [
            'id'            =>  $this->owner->ID,
            'title'         =>  $this->owner->Title,
            'url'           =>  $this->owner->getLinkURL(),
            'is_internal'   =>  $this->owner->Type == 'SiteTree',
            'open_in_blank' =>  $this->owner->OpenInNewWindow ? true : false
        ];

        $this->owner->extend('getMoreData', $data);

        return $data;
    }
}
