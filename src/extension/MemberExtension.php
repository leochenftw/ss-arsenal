<?php

namespace Leochenftw\Extension;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\FieldList;
use App\Web\Email\PasswordRecoveryEmail;

class MemberExtension extends DataExtension
{
    /**
     * Database fields
     * @var array
     */
    private static $db = [
        'DateLoggedIn'      =>  'Datetime',
        'ValidationKey'     =>  'Varchar(40)'
    ];

    /**
     * Update Fields
     * @return FieldList
     */
    public function updateCMSFields(FieldList $fields)
    {
        $owner = $this->owner;
        $fields->addFieldsToTab(
            'Root.Misc',
            [
                $fields->fieldByName('Root.Main.ValidationKey'),
                $fields->fieldByName('Root.Main.DateLoggedIn')->performReadonlyTransformation()
            ]
        );
        return $fields;
    }

    public function populateDefaults()
    {
        $this->owner->ValidationKey =   sha1(time() . rand());
    }

    public function password_recovery()
    {
        if ($this->owner->isActivated()) {
            $this->owner->populateDefaults();
            $this->owner->write();
        }

        $email  =   PasswordRecoveryEmail::create($this->owner);
        $email->send();
    }

    public function getData()
    {
        return [
            'id'        =>  $this->owner->ID,
            'email'     =>  $this->owner->Email,
            'firstname' =>  $this->owner->FirstName,
            'surname'   =>  $this->owner->Surname,
            'is_admin'  =>  $this->owner->inGroup('administrators') ? true : false
        ];
    }

    public function isActivated()
    {
        return empty($this->owner->ValidationKey);
    }
}
