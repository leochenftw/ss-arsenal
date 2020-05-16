<?php

namespace Leochenftw\Extension;
use Cocur\Slugify\Slugify;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;

class SlugifyExtension extends DataExtension
{
    /**
     * Database fields
     * @var array
     */
    private static $db = [
        'Slug'  =>  'Varchar(128)'
    ];

    private static $indexes =   [
        'Slug'  =>  true
    ];

    /**
     * Event handler called before writing to the database.
     */
    public function onBeforeWrite()
    {
        parent::onBeforeWrite();
        if ($this->owner->hasField('Title') && !preg_match('/^[a-z0-9 .\-]+$/i', $this->owner->Title)) {
            $slugify            =   new Slugify();
            $this->owner->Slug  =   $slugify->slugify($this->owner->Title);
        }
    }

    /**
     * Update Fields
     * @return FieldList
     */
    public function updateCMSFields(FieldList $fields)
    {
        $owner = $this->owner;
        $fields->removeByName([
            'Slug'
        ]);

        if ($this->owner->hasField('Title') && !empty($this->owner->Slug)) {
            $fields->fieldByName('Root.Main.Title')->setDescription('Slug: ' . $this->owner->Slug);
        }
        return $fields;
    }
}
