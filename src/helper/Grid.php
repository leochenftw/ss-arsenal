<?php

namespace Leochenftw;

use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_Base;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordViewer;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use UndefinedOffset\SortableGridField\Forms\GridFieldSortableRows;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
// use SilverStripe\Forms\GridField\GridFieldArchiveAction;
// use SilverStripe\Versioned\GridFieldArchiveAction;
use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\Forms\GridField\GridFieldConfig;
use Symbiote\GridFieldExtensions\GridFieldAddExistingSearchButton;
use Symbiote\GridFieldExtensions\GridFieldEditableColumns;
use SilverStripe\Forms\GridField\GridFieldButtonRow;
use SilverStripe\Forms\GridField\GridFieldToolbarHeader;
use Symbiote\GridFieldExtensions\GridFieldTitleHeader;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use SilverStripe\Forms\GridField\GridFieldPageCount;
use SilverStripe\Forms\GridField\GridFieldPaginator;

class Grid
{
    public static function make($name, $label, $source, $sortable = true, $gridHeaderType = 'GridFieldConfig_RecordEditor', $no_add = false, $sort_field = 'Sort')
    {
        /*
        GridFieldConfig_Base
        GridFieldConfig_RecordViewer
        GridFieldConfig_RecordEditor
        GridFieldConfig_RelationEditor
        */
        if (empty($label)) {
            $label  =   $name;
        }

        $grid   =   GridField::create($name, $label, $source);

        if ($gridHeaderType == 'GridFieldConfig_Base') {
            $config =   GridFieldConfig_Base::create();
        }

        if ($gridHeaderType == 'GridFieldConfig_RecordViewer') {
            $config =   GridFieldConfig_RecordViewer::create();
        }

        if ($gridHeaderType == 'GridFieldConfig_RecordEditor') {
            $config =   GridFieldConfig_RecordEditor::create();
            $delete = $config->getComponentByType(GridFieldDeleteAction::class);
            $delete->setRemoveRelation(false);
        }

        if ($gridHeaderType == 'GridFieldConfig_RelationEditor') {
            $config =   GridFieldConfig_RelationEditor::create();
            $config->removeComponentsByType('SilverStripe\Versioned\GridFieldArchiveAction');
            $delete = $config->getComponentByType(GridFieldDeleteAction::class);
            $delete->setRemoveRelation(true);
            if ($no_add) {
                $config->removeComponentsByType($config->getComponentByType(GridFieldAddNewButton::class));
            }
        }

        if ($sortable) {
            $config->addComponent($sortable = new GridFieldSortableRows($sort_field));
            $sortable->setUpdateVersionedStage('Live');
        }

        $grid->setConfig($config);
        return $grid;
    }

    public static function manyExtraSortable($relation_name, $field_title, $relation_datalist, $class, $fields, $sort_field = 'Sort')
    {
        $featuresGridFieldSearchButton = new GridFieldAddExistingSearchButton();
        $featuresGridFieldSearchButton->setSearchList($class::get());

        // Features field editable columns
        $featuresGridFieldEditableColumns = new GridFieldEditableColumns();
        $featuresGridFieldEditableColumns->setDisplayFields($fields);

        // Features field config including base GridFieldConfig_RelationEditor components, custom search button, editable columns and orderable rows
        $featuresGridFieldConfig = GridFieldConfig::create();
        $featuresGridFieldConfig->addComponent(new GridFieldButtonRow('before'));
        $featuresGridFieldConfig->addComponent($featuresGridFieldSearchButton);
        $featuresGridFieldConfig->addComponent(new GridFieldToolbarHeader());
        $featuresGridFieldConfig->addComponent(new GridFieldTitleHeader());
        $featuresGridFieldConfig->addComponent($featuresGridFieldEditableColumns);
        $featuresGridFieldConfig->addComponent(new GridFieldDeleteAction(true));
        $featuresGridFieldConfig->addComponent(new GridFieldOrderableRows($sort_field));
        $featuresGridFieldConfig->addComponent(new GridFieldPageCount('toolbar-header-right'));
        $featuresGridFieldConfig->addComponent(new GridFieldPaginator());
        $featuresGridFieldConfig->addComponent(new GridFieldDetailForm());

        return GridField::create(
            $relation_name,
            $field_title,
            $relation_datalist,
            $featuresGridFieldConfig
        );
    }
}
