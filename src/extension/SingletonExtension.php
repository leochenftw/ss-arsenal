<?php

namespace Leochenftw\Extension;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use Leochenftw\Debugger;
use SilverStripe\Versioned\Versioned;

class SingletonExtension extends DataExtension
{
    /**
     * DataObject create permissions
     * @param Member $member
     * @param array $context Additional context-specific data which might
     * affect whether (or where) this object could be created.
     * @return boolean
     */
    public function canCreate($member = null, $context = [])
    {
        if (empty($member)) return false;

        if (Versioned::get_by_stage($this->owner->ClassName, 'Stage')->count() > 0) {
            return false;
        }

        return $member->inGroup('administrators') || $member->inGroup('content-authors');
    }
}
