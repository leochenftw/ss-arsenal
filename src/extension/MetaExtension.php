<?php

namespace Leochenftw\Extension;

use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;

class MetaExtension extends DataExtension
{
    /**
     * Database fields
     * @var array
     */
    private static $db = [
        'MetaKeywords'  => 'Varchar(256)',
        'MetaRobots'    => 'Varchar(128)',
        'ConanicalURL'  => 'Varchar(256)'
    ];

    /**
     * Add default values to database
     * @var array
     */
    private static $defaults = [
        'MetaRobots'    => 'INDEX, FOLLOW'
    ];

    /**
     * Update Fields
     * @return FieldList
     */
    public function updateCMSFields(FieldList $fields)
    {
        if ($meta = $fields->fieldbyName('Root.Main.Metadata')) {

            $fields->insertBefore(TextField::create('ConanicalURL'), 'MetaDescription');

            $meta->push(
                TextField::create('MetaKeywords')
            );

            $meta->push(
                TextField::create('MetaRobots')
            );
        }

        return $fields;
    }
}
