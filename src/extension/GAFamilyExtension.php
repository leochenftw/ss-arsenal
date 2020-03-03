<?php

namespace Leochenftw\Extension;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\ToggleCompositeField;
use SilverStripe\ORM\DataExtension;

class GAFamilyExtension extends DataExtension
{
    /**
     * Database fields
     * @var array
     */
    private static $db = [
        'GoogleSiteVerificationCode' => 'HTMLText',
        'GoogleAnalyticsCode'        => 'HTMLText',
        'GTMHead'                    => 'HTMLText',
        'GTMBody'                    => 'HTMLText'
    ];

    /**
     * Update Fields
     * @return FieldList
     */
    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldsToTab(
            'Root.GoogleTrackings',
            [
                TextareaField::create(
                    'GoogleAnalyticsCode',
                    'Google Analytics Tracking Code'
                ),
                TextareaField::create(
                    'GTMHead',
                    'Google Tag Manager - head part'
                ),
                TextareaField::create(
                    'GTMBody',
                    'Google Tag Manager - body part'
                ),
                TextareaField::create(
                    'GoogleSiteVerificationCode',
                    'Google webmaster meta tag'
                )->setDescription('Full Google webmaster meta tag For example &lt;meta name="google-site-verification" content="hjhjhJHG12736JHGdfsdf" &gt;')
            ]
        );

        return $fields;
    }
}
