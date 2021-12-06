<?php

namespace Leochenftw\Extension;
use SilverStripe\Lumberjack\Model\Lumberjack;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use UndefinedOffset\SortableGridField\Forms\GridFieldSortableRows;

class LumberjackExtension extends DataExtension
{
    /**
     * Update Fields
     * @return FieldList
     */
    public function updateCMSFields(FieldList $fields)
    {
        $owner = $this->owner;
        if ($owner->hasExtension(Lumberjack::class)) {
            $children   =   $fields->fieldByName('Root.ChildPages.ChildPages');

            $config     =   $children->getConfig();
            $config->addComponent($sortable = new GridFieldSortableRows('Sort'));
            $sortable->setUpdateVersionedStage('Live');
        }
        
        return $fields;
    }
}
