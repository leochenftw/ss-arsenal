<?php
/**
 * @file Permission functions
 * @author Simon Winter <simon@saltedherring.com>
 *
 * Provides CRUD can functions.
 *
 * */
namespace Leochenftw\Extension;

use SilverStripe\Core\ClassInfo;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Security\Permission;

class CanCRUDPermissionFunctions extends DataExtension
{
    public function canView($member = null)
    {
        if (is_numeric(Permission::check('ADMIN'))) {
            return true;
        }

        $className = ClassInfo::shortName($this->owner);

        return Permission::check('VIEW_' . strtoupper($className));
    }
    //
    public function canEdit($member = null)
    {
        if (is_numeric(Permission::check('ADMIN'))) {
            return true;
        }

        $className = ClassInfo::shortName($this->owner);

        return Permission::check('EDIT_' . strtoupper($className));
    }
    //
    public function canDelete($member = null)
    {
        if (is_numeric(Permission::check('ADMIN'))) {
            return true;
        }

        $className = ClassInfo::shortName($this->owner);

        return Permission::check('DELETE_' . strtoupper($className));
    }

    public function canCreate($member = null, $context = [])
    {
        if (is_numeric(Permission::check('ADMIN'))) {
            return true;
        }

        $className = ClassInfo::shortName($this->owner);

        return Permission::check('CREATE_' . strtoupper($className));
    }

    public function canPublish($member = null)
    {
        if (is_numeric(Permission::check('ADMIN'))) {
            return true;
        }

        $className = ClassInfo::shortName($this->owner);

        return Permission::check('PUBLISH_' . strtoupper($className));
    }
    //
    public function canUnpublish($member = null)
    {
        if (is_numeric(Permission::check('ADMIN'))) {
            return true;
        }

        $className = ClassInfo::shortName($this->owner);

        return Permission::check('UNPUBLISH_' . strtoupper($className));
    }
    //
    public function canArchive($member = null)
    {
        if (is_numeric(Permission::check('ADMIN'))) {
            return true;
        }

        $className = ClassInfo::shortName($this->owner);

        return Permission::check('ARCHIVE_' . strtoupper($className));
    }

    public function canConfigure($member = null)
    {
        if (is_numeric(Permission::check('ADMIN'))) {
            return true;
        }

        $className = ClassInfo::shortName($this->owner);

        return Permission::check('CONFIGURE_' . strtoupper($className));
    }

    public function can($perm, $member = null, $context = [])
    {
        if (is_numeric(Permission::check('ADMIN'))) {
            return true;
        }

        $className = ClassInfo::shortName($this->owner);

        return Permission::check(strtoupper($perm) . '_' . strtoupper($className));
    }
}
