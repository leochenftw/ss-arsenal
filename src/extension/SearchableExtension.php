<?php

namespace Leochenftw\Extension;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms\FieldList;
use App\Web\Model\MembershipApplication;
use SilverStripe\ORM\Connect\MySQLSchemaManager;

class SearchableExtension extends DataExtension
{
    private static $db = [
        "Title"     =>  "Varchar(255)",
        "Content"   =>  "HTMLText",
    ];

    private static $indexes = [
        'SearchFields' => [
            'type'      =>  'fulltext',
            'columns'   =>  ['Title', 'Content']
        ]
    ];

    private static $create_table_options = [
        MySQLSchemaManager::ID  =>  'ENGINE=MyISAM'
    ];
}
