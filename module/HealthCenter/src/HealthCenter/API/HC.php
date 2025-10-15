<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 9/6/14
 * Time: 1:38 PM
 */

namespace HealthCenter\API;


use System\API\BaseAPI;

class HC extends BaseAPI
{
    private static $doctorRoles = null;

    public static function GetDoctorRoles()
    {
        if (self::$doctorRoles == null) {
            $doctorUserRoles = array();
            $config = getConfig('health-center');
            if (isset($config->varValue['doctorUserRole'])) {
                $doctorUserRoles = $config->varValue['doctorUserRole'];
            }
            self::$doctorRoles = $doctorUserRoles;
        }
        return self::$doctorRoles;
    }

    /**
     * Is the given user(or current user if none is provided) is a doctor
     */
    public static function IsDoctor()
    {
        $roles = self::GetDoctorRoles();
        foreach (current_user()->roles as $role)
            if (in_array($role['id'], $roles))
                return true;
        return false;
    }
} 