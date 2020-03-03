<?php
/**
 * @file SortOrder Extension
 * @author Simon Winter <simon@saltedherring.com>
 *
 * Adds sort order to a dataobject.
 *
 * */
namespace Leochenftw\Extension;

use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;

class SortOrderExtension extends DataExtension
{
    private static $db = [
        'Sort' => 'Int'
    ];

    private static $default_sort = ['Sort' => 'ASC'];

    public function updateCMSFields(FieldList $fields)
    {
        $fields->removeByName([
            'Sort'
        ]);
    }
}
