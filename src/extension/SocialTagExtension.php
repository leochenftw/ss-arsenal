<?php

namespace Leochenftw\Extension;
use SilverStripe\Core\Convert;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\Assets\Image;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\ToggleCompositeField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Control\Director;
use Leochenftw\Debugger;
use SaltedHerring\Salted\Cropper\SaltedCroppableImage;
use SaltedHerring\Salted\Cropper\Fields\CroppableImageField;

/**
 * @file SocialTagExtension
 *
 * Extension to provide Open Graph tags to page types.
 */
class SocialTagExtension extends DataExtension
{
    private static $db = [
        'SocialBaseURL'         =>  'Varchar(1024)',
        'FBAppID'               =>  'Varchar(128)',
        'OGType'                =>  'Enum("website,article,blog,product")',
        'OGTitle'               =>  'Varchar(255)',
        'OGTitleAsDefault'      =>  'Boolean',
        'OGDescription'         =>  'Varchar(255)',
        'OGDescAsDefault'       =>  'Boolean',
        'TwitterCard'           =>  'Enum("summary,summary_large_image")',
        'TwitterTitle'          =>  'Varchar(255)',
        'TwTitleAsDefault'      =>  'Boolean',
        'TwitterDescription'    =>  'Varchar(255)',
        'TwDescAsDefault'       =>  'Boolean'
    ];

    private static $has_one =  [
        'OGImage'               =>  SaltedCroppableImage::class,
        'OGImageLarge'          =>  SaltedCroppableImage::class,
        'TwitterImage'          =>  SaltedCroppableImage::class,
        'TwitterImageLarge'     =>  SaltedCroppableImage::class
    ];

    /**
     * Add default values to database
     * @var array
     */
    private static $defaults = [
        'OGTitleAsDefault'      =>  true,
        'OGDescAsDefault'       =>  true,
        'TwTitleAsDefault'      =>  true,
        'TwDescAsDefault'       =>  true
    ];

    /**
     * Event handler called before writing to the database.
     */
    public function onBeforeWrite()
    {
        parent::onBeforeWrite();
        $is_site_config                             =   $this->owner->ClassName == 'SilverStripe\SiteConfig\SiteConfig';

        if ($this->owner->OGTitleAsDefault) {
            $this->owner->OGTitle                   =   !empty($this->owner->MetaTitle) ?
                                                        $this->owner->MetaTitle :
                                                        $this->owner->Title;
        }

        if (!$is_site_config) {
            if ($this->owner->OGDescAsDefault) {
                $this->owner->OGDescription         =   $this->owner->MetaDescription;
            }
        }

        if ($this->owner->TwTitleAsDefault) {
            $this->owner->TwitterTitle              =   !empty($this->owner->MetaTitle) ?
                                                        $this->owner->MetaTitle :
                                                        $this->owner->Title;
        }

        if (!$is_site_config) {
            if ($this->owner->TwDescAsDefault) {
                $this->owner->TwitterDescription    =   $this->owner->MetaDescription;
            }
        }
    }


    /**
     * Update Fields
     * @return FieldList
     */
    public function updateCMSFields(FieldList $fields)
    {
        if ($this->owner->ClassName == 'SilverStripe\CMS\Model\RedirectorPage' ||
            $this->owner->ClassName == 'SilverStripe\CMS\Model\VirtualPage' ||
            $this->owner->ClassName == 'SilverStripe\ErrorPage\ErrorPage'
        ) {
            return false;
        }

        $is_site_config         =   $this->owner->ClassName == 'SilverStripe\SiteConfig\SiteConfig';

        $fields->removeFieldsFromTab('Root.Main', [
            'OGTitle',
            'OGDescription',
            'OGType',
            'OGTitleAsDefault',
            'OGDescAsDefault',
            'OGImageID',
            'OGImageLargeID'
        ]);

        if (empty($fields->fieldByName('Root.SEO.OG'))) {
            if ($this->owner->ClassName == SiteConfig::class) {
                $fields->addFieldsToTab(
                    'Root.SEO',
                    [
                        TextField::create(
                            'SocialBaseURL'
                        ),
                        TextField::create(
                            'FBAppID',
                            'Fb:app_id'
                        )
                    ]
                );
            }

            $og_type            =   DropdownField::create(
                                        'OGType',
                                        'Type',
                                        singleton($this->owner->ClassName)->dbObject('OGType')->enumValues()
                                    );
            $og_title_default   =   CheckboxField::create(
                                        'OGTitleAsDefault',
                                        'Use Meta Title as OG Title'
                                    )->setDescription('If meta title is not set, it will use page title instead');
            $og_title           =   TextField::create(
                                        'OGTitle',
                                        'Title'
                                    );
            $og_desc_default    =   CheckboxField::create(
                                        'OGDescAsDefault',
                                        'Use Meta Description as OG Description'
                                    );
            $og_desc            =   TextareaField::create(
                                        'OGDescription',
                                        'Description'
                                    );
            $og_image           =   CroppableImageField::create('OGImageID', 'Square Image')
                                        ->setCropperRatio(1)
                                        ->setDescription('Image must be uploaded at a ratio of 1:1 (square)');
            $og_large_image     =   CroppableImageField::create('OGImageLargeID', 'Landscape Image')
                                        ->setCropperRatio(1.91)
                                        ->setDescription('Image must be uploaded at a ratio of 1200/630 (landscape)');

            $og                 =   ToggleCompositeField::create(
                                        'OG',
                                        'Open Graph Tags',
                                        !$is_site_config ?
                                        [
                                            $og_type,
                                            $og_title_default,
                                            $og_title,
                                            $og_desc_default,
                                            $og_desc,
                                            $og_image,
                                            $og_large_image
                                        ] :
                                        [
                                            $og_desc,
                                            $og_image,
                                            $og_large_image
                                        ]
                                    );

            // $OGImage->setDescription('Image must be uploaded at a ratio of 1200px x 627px.');
            $fields->addFieldToTab('Root.SEO', $og);
        }

        if (empty($fields->fieldByName('Root.SEO.Twitter'))) {
            $tw_type            =   DropdownField::create(
                'TwitterCard',
                'Card Type',
                                        singleton($this->owner->ClassName)->dbObject('TwitterCard')->enumValues()
                                    );
            $tw_title_default   =   CheckboxField::create(
                                        'TwTitleAsDefault',
                                        'Use Meta Title as Twitter Title'
                                    );
            $tw_title           =   TextField::create(
                                        'TwitterTitle',
                                        'Title'
                                    );
            $tw_desc_default    =   CheckboxField::create(
                                        'TwDescAsDefault',
                                        'Use Meta Description as Twitter Description'
                                    );
            $tw_desc            =   TextareaField::create('TwitterDescription', 'Description');
            $tw_image           =   CroppableImageField::create('TwitterImageID', 'Square Image')
                                        ->setCropperRatio(1)
                                        ->setDescription('Image must be uploaded at a ratio of 1:1 (square)');
            $tw_large_image     =   CroppableImageField::create('TwitterImageLargeID', 'Landscape Image')
                                        ->setFolderName('SEO')
                                        ->setCropperRatio(2)
                                        ->setDescription('Image must be uploaded at a ratio of 2:1 (landscape)');

            $twitter            =   ToggleCompositeField::create(
                                        'Twitter',
                                        'Twitter Card Tags',
                                        !$is_site_config ?
                                        [
                                            $tw_type,
                                            $tw_title_default,
                                            $tw_title,
                                            $tw_desc_default,
                                            $tw_desc,
                                            $tw_image,
                                            $tw_large_image
                                        ] :
                                        [
                                            $tw_desc,
                                            $tw_image,
                                            $tw_large_image
                                        ]
                                    );
            // $TwitterImage->setDescription('Image must be uploaded at a ratio of 440px x 220px.');
            $fields->addFieldToTab('Root.SEO', $twitter);
        }
    }

    public function get_og_twitter_meta()
    {
        $site_config    =   SiteConfig::current_site_config();
        if ((!empty($this->owner->OGType) || !empty($site_config->OGType)) && $this->owner->ClassName != SiteConfig::class) {
            $data   =   [
                [
                    'property'  =>  'og:type',
                    'content'   =>  !empty($this->owner->OGType) ? $this->owner->OGType : $site_config->OGType
                ],
                [
                    'property'  =>  'og:url',
                    'content'   =>  $this->owner->AbsoluteLink()
                ],
                [
                    'property'  =>  'og:title',
                    'content'   =>  !empty($this->owner->OGTitle) ? $this->owner->OGTitle : $this->owner->Title
                ],
                [
                    'property'  =>  'og:description',
                    'content'   =>  !empty($this->owner->OGDescription) ?
                                    $this->owner->OGDescription :
                                    (
                                        !empty($site_config->OGDescription) ?
                                        $site_config->OGDescription :
                                        $this->owner->get_meta_description()
                                    )
                ],
                [
                    'property'  =>  'og:image',
                    'content'   =>  $this->owner->OGImage()->exists() ?
                                    $this->owner->OGImage()->getCropped()->getAbsoluteURL() :
                                    ($site_config->OGImage()->exists() ?
                                    $site_config->OGImage()->getCropped()->getAbsoluteURL() : null)
                ],
                [
                    'property'  =>  'og:image:width',
                    'content'   =>  $this->owner->OGImage()->exists() ?
                                    $this->owner->OGImage()->getCropped()->Width :
                                    ($site_config->OGImage()->exists() ? $site_config->OGImage()->getCropped()->Width : null)
                ],
                [
                    'property'  =>  'og:image:height',
                    'content'   =>  $this->owner->OGImage()->exists() ?
                                    $this->owner->OGImage()->getCropped()->Height :
                                    ($site_config->OGImage()->exists() ? $site_config->OGImage()->getCropped()->Height : null)
                ],
                [
                    'property'  =>  'og:image',
                    'content'   =>  $this->owner->OGImageLarge()->exists() ?
                                    $this->owner->OGImageLarge()->getCropped()->getAbsoluteURL() :
                                    ($site_config->OGImageLarge()->exists() ? $site_config->OGImageLarge()->getCropped()->getAbsoluteURL() : null)
                ],
                [
                    'property'  =>  'og:image:width',
                    'content'   =>  $this->owner->OGImageLarge()->exists() ?
                                    $this->owner->OGImageLarge()->getCropped()->Width :
                                    ($site_config->OGImageLarge()->exists() ? $site_config->OGImageLarge()->getCropped()->Width : null)
                ],
                [
                    'property'  =>  'og:image:height',
                    'content'   =>  $this->owner->OGImageLarge()->exists() ?
                                    $this->owner->OGImageLarge()->getCropped()->Height :
                                    ($site_config->OGImageLarge()->exists() ? $site_config->OGImageLarge()->getCropped()->Height : null)
                ],
                [
                    'property'  =>  'fb:app_id',
                    'content'   =>  $site_config->FBAppID
                ],
                [
                    'name'      =>  'twitter:card',
                    'content'   =>  !empty($this->owner->TwitterCard) ? $this->owner->TwitterCard : $site_config->TwitterCard
                ],
                [
                    'name'      =>  'twitter:site',
                    'content'   =>  $this->owner->AbsoluteLink()
                ],
                [
                    'name'      =>  'twitter:title',
                    'content'   =>  !empty($this->owner->TwitterTitle) ? $this->owner->TwitterTitle : $this->owner->Title
                ],
                [
                    'name'      =>  'twitter:description',
                    'content'   =>  !empty($this->owner->TwitterDescription) ?
                                    $this->owner->TwitterDescription :
                                    (
                                        !empty($site_config->TwitterDescription) ?
                                        $site_config->TwitterDescription :
                                        $this->owner->get_meta_description()
                                    )
                ],
                [
                    'name'      =>  'twitter:image',
                    'content'   =>  $this->get_twitter_image()
                ],
                [
                    'itemprop'  =>  'name',
                    'content'   =>  !empty($this->owner->OGTitle) ? $this->owner->OGTitle : $this->owner->Title
                ],
                [
                    'itemprop'  =>  'description',
                    'content'   =>  !empty($this->owner->OGDescription) ?
                                    $this->owner->OGDescription :
                                    (
                                        !empty($site_config->OGDescription) ?
                                        $site_config->OGDescription :
                                        $this->owner->get_meta_description()
                                    )
                ],
                [
                    'itemprop'  =>  'image',
                    'content'   =>  !empty($this->owner->OGImage()->exists()) ?
                                    $this->owner->OGImage()->getCropped()->getAbsoluteURL() :
                                    ($site_config->OGImage()->exists() ? $site_config->OGImage()->getCropped()->getAbsoluteURL() : null)
                ]
            ];

            if ($base_url = $site_config->SocialBaseURL) {
                $refined    =   [];
                foreach ($data as $item) {
                    if (!empty($item['content'])) {
                        $refined_item   =   [];
                        foreach ($item as $key => $value) {
                            $refined_item[$key] =   str_replace(Director::absoluteBaseURL(), $base_url, $value);
                        }
                        $refined[]  =   $refined_item;
                    }
                }

                return $refined;
            }

            return $data;
        }
        return null;
    }

    private function get_twitter_image()
    {
        if (!empty($this->owner->TwitterCard)) {
            if ($this->owner->TwitterCard == 'summary') {
                if ($this->owner->TwitterImage()->exists()) {
                    return $this->owner->TwitterImage()->getCropped()->getAbsoluteURL();
                }
            } else {
                if ($this->owner->TwitterImageLarge()->exists()) {
                    return $this->owner->TwitterImageLarge()->getCropped()->getAbsoluteURL();
                }
            }
        }

        return null;
    }
}
