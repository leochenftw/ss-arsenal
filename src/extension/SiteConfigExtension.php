<?php

namespace Leochenftw\Extension;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\TextField;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Assets\Image;
use SilverStripe\Assets\File;
use SilverStripe\Forms\LiteralField;
use SilverStripe\ORM\DataExtension;
use Page;
use SilverStripe\Core\Environment;

/**
 * @file SiteConfigExtension
 *
 * Extension to provide Open Graph tags to site config.
 */
class SiteConfigExtension extends DataExtension
{
    /**
     * Database fields
     * @var array
     */
    private static $db = [
        'UnderMaintenance'  =>  'Boolean',
        'ContactRecipients' =>  'Text',
        'ContactBcc'        =>  'Text'
    ];

    /**
     * Has_one relationship
     * @var array
     */
    private static $has_one = [
        'Logo' =>  Image::class,
        'LogoSVG' => File::class,
        'TermsConditions' => Page::class,
        'PrivacyPolicy' => Page::class
    ];

    /**
     * Relationship version ownership
     * @var array
     */
    private static $owns = [
        'Logo',
        'LogoSVG'
    ];

    /**
     * Update Fields
     * @return FieldList
     */
    public function updateCMSFields(FieldList $fields)
    {
        $owner = $this->owner;

        $fields->addFieldsToTab(
            'Root.Main',
            [
                UploadField::create(
                    'Logo',
                    'Logo'
                )->setAllowedExtensions(['jpg', 'jpeg', 'png', 'svg'])
            ],
            'Title'
        );

        $fields->addFieldsToTab(
            'Root.Contact',
            [
                TextField::create('ContactRecipients', 'Contact Recipients')->setDescription('Use "," to separate multiple email addresses'),
                TextField::create('ContactBcc', 'Contact Bcc')->setDescription('Use "," to separate multiple email addresses'),
            ]
        );

        $fields->addFieldsToTab(
            'Root.Main',
            [
                DropdownField::create(
                    'TermsConditionsID',
                    'Terms & Conditions',
                    Page::get()->filter(['ParentID' => 0])->map()
                )->setEmptyString('- select one -'),
                DropdownField::create(
                    'PrivacyPolicyID',
                    'Privacy Policy',
                    Page::get()->filter(['ParentID' => 0])->map()
                )->setEmptyString('- select one -')
            ]
        );

        $fields->addFieldToTab(
            'Root.Main',
            CheckboxField::create(
                'UnderMaintenance',
                'Site under maintenance'
            )
        );

        $fields->addFieldToTab(
            'Root.Main',
            LiteralField::create('CacheFlusher', '<p><a style="color: red;" target="_blank" href="/?flush=all">Flush cached data</a></p>')
        );

        return $fields;
    }

    private function read_vue_entry_file()
    {
        $file   =   Environment::getEnv('FRONTEND_PATH');
        if (!empty($file)) {
            if (file_exists($file)) {
                return file_get_contents($file);
            }
        }

        return false;
    }

    public function getVueCSS()
    {
        if ($file = $this->read_vue_entry_file()) {
            $style_pattern  =   "/\<link href=\"(.*?)\" rel=\"stylesheet\">/i";
            preg_match_all($style_pattern, $file, $matches);
            $styles         =   count($matches) > 0 ? $matches[0] : [];
            if (empty($styles)) {
                $style_pattern  =   "/\<link href=(.*?) rel=stylesheet>/i";
                preg_match_all($style_pattern, $file, $matches);
                $styles         =   count($matches) > 0 ? $matches[0] : [];
            }

            return implode("\n", $styles);
        }

        return null;
    }

    public function getVueJS()
    {
        if ($this->owner->UnderMaintenance) {
            return null;
        }

        if ($file = $this->read_vue_entry_file()) {
            $script_pattern =   "/\<script type=\"text\/javascript\" src=\"(.*?)\"\>\<\/script\>/i";
            preg_match_all($script_pattern, $file, $matches);
            $scripts        =   count($matches) > 0 ? $matches[0] : [];

            if (empty($scripts)) {
                $script_pattern =   "/\<script type=text\/javascript src=(.*?)\>\<\/script\>/i";
                preg_match_all($script_pattern, $file, $matches);
                $scripts        =   count($matches) > 0 ? $matches[0] : [];
            }

            return implode("\n", $scripts);
        }

        return null;
    }
}
