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

class Grid
{
    public static function make($name, $label, $source, $sortable = true, $gridHeaderType = 'GridFieldConfig_RecordEditor', $no_add = false)
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
            $config->addComponent($sortable = new GridFieldSortableRows('Sort'));
            $sortable->setUpdateVersionedStage('Live');
        }

        $grid->setConfig($config);
        return $grid;
    }
}
