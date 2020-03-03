<?php
/**
 * @file canCRUD Extension
 * @author Simon Winter <simon@saltedherring.com>
 *
 * Allows can CRUD functions.
 *
 * */
namespace Leochenftw\Extension;

use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Config\Config;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Security\Permission;
use SilverStripe\Security\PermissionProvider;

class CanCRUD implements PermissionProvider
{
    public function providePermissions()
    {
        $className = get_class($this);
        $permissions = [];
        $permission_codes = Config::inst()->get($className, 'PermissionCode');
        $classes = Config::inst()->get($className, 'Permissions');

        if (!empty($classes)) {
            foreach ($classes as $className) {
                $shortName = ClassInfo::shortName($className);
                $singleton = $className::singleton()->singular_name();
                $config = Config::inst()->get($className, 'Permission');

                foreach ($permission_codes as $code) {
                    $permissions[strtoupper($code) . '_' . strtoupper($shortName)] = [
                        'name' => $code . ' a ' . $singleton,
                        'category' => $config['Category']
                    ];
                }
            }
        }

        return $permissions;
    }
}
