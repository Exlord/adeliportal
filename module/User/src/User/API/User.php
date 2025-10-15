<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 12/1/13
 * Time: 1:56 PM
 */
namespace User\API;

use Application\Model\Config;
use System\API\BaseAPI;
use User\Model\RoleTable;
use User\Module;
use Zend\View\Model\ViewModel;

class User extends BaseAPI
{
    /**
     * The data of the latest rendered user profile
     * @var null
     */
    public static $LatestUser = null;

    private static $CURRENT_USER = null;

    public static $STATIC_FIELDS = array(
        'email' => 'Email',
        'firstName' => 'First Name',
        'lastName' => 'Last Name',
        'birthDate' => 'Birth Date',
        'phone' => 'Phone',
        'mobile' => 'Mobile',
        'countryTitle' => 'Country',
        'stateTitle' => 'State',
        'cityTitle' => 'City',
        'address' => 'Address',
        'aboutMe' => 'About Me',
    );

    public static function getCurrentUser()
    {
        if (is_null(self::$CURRENT_USER))
            self::$CURRENT_USER = getSM()->get('user_identity');
        return self::$CURRENT_USER;
    }

    public static function Save($modelData, $fieldIds = null)
    {
        $basic = $modelData['basic'];

        if (isset($basic['id']) && $basic['id'])
            $isNew = false;
        else
            $isNew = true;

      //  $isNew = (!isset($basic['id']) && $basic['id']);

        if (array_key_exists('password_verify', $basic))
            unset($basic['password_verify']);
        if (array_key_exists('password', $basic)) {
            if (isset($basic['password'])) {
                $bCrypt = new \Zend\Crypt\Password\Bcrypt();
                $securePass = $bCrypt->create($basic['password']);
                $basic['password'] = $securePass;
            } else
                unset($basic['password']);
        }

        $uid = self::getUserTable()->save($basic); //create the user
        if (isset($basic['id']))
            $uid = $basic['id'];

        if ($isNew)
            getSM('user_event_manager')->newUserIsCreated($uid);


        if (isset($modelData['roles']))
            self::changeRoles($uid, $modelData['roles']);
        else {
            $userConfig = getConfig('user_config')->varValue;
            $userRegRole = 2; //Default Member role
            if (isset($userConfig['userRegRole']))
                $userRegRole = $userConfig['userRegRole'];
            self::changeRoles($uid, $userRegRole);
        }

        $profile = isset($modelData['profile']) ? $modelData['profile'] : array();
        $profile['userId'] = $uid;
        self::getUserProfileTable()->save($profile);

        $profile2 = isset($modelData['profile2']) ? $modelData['profile2'] : null;
        if ($profile2 && $fieldIds) {
            self::getFieldsApi()->save('user_profile', $uid, $profile2, $fieldIds);
        }

        //TODO send email with details
        return $uid;
    }

    public
    static function Delete($uid)
    {
        self::getUserTable()->remove($uid);
        self::getUserProfileTable()->removeByUserId($uid);
        self::getUserRoleTable()->removeByUserId($uid);
        if (self::hasFieldsApi()) {
            self::getFieldsApi()->init('user_profile');
            self::getFieldsApi()->remove($uid);
        }
        getSM('user_event_manager')->userIsDeleted($uid);
    }

    public static function changeRoles($uid, $roles)
    {
        if (!count($roles))
            $roles = array(RoleTable::MEMBER);
        self::getUserRoleTable()->changeRoles($uid, $roles);
    }

    /**
     * @return \User\Model\UserTable
     */
    public static function getUserTable()
    {
        return getSM('user_table');
    }

    /**
     * @return \User\Model\UserRoleTable
     */
    public static function getUserRoleTable()
    {
        return getSM()->get('user_role_table');
    }

    /**
     * @return \User\Model\UserProfileTable
     */
    public static function getUserProfileTable()
    {
        return getSM()->get('user_profile_table');
    }

    public static function getUserProfile(
        $userId,
        $specialInformationShow = true,
        $columnSizes = array('col-md-3 col-sm-6', 'col-md-9 col-sm-6')
    )
    {
        $fields_table = null;
        $hasFieldsApi = getSM()->has('fields_api');
        if ($hasFieldsApi) {
            /* @var $fields_api \Fields\API\Fields */
            $fields_api = getSM()->get('fields_api');
            $fields_table = $fields_api->init('user_profile');
        }

        self::$LatestUser = $user = getSM('user_table')->get($userId, $fields_table);

        // custom profile
        $roles = getSM('user_role_table')->getRolesArray($userId);
        $user_config = getConfig('user_config');

        $customFields = null;
        if ($hasFieldsApi) {
            $fields = null;
            if (isset($user_config->varValue['roleType_fields'])) {
                $roleType_fields = $user_config->varValue['roleType_fields'];
                foreach ($roles as $role) {
                    if (isset($roleType_fields[$role])) {
                        foreach ($roleType_fields[$role] as $id => $value) {
                            if ($value == '1')
                                $fields[$id] = $id;
                        }
                    }
                }
            }

            if (current_user()->id) {
                if (isAllowed(Module::USER_VIEW_PRIVATE_FIELDS))
                    $currentUserFieldAccessLevel = 'private';
                else
                    $currentUserFieldAccessLevel = 'members';
            } else
                $currentUserFieldAccessLevel = 'public';

            //region User Config

            $allowedAccessLevels = array('private', 'members', 'public');
            $accessLevels = array();
            $fields_access = @$user_config->varValue['fields_access'];
            if ($fields_access) {
                if ($fields) {
                    foreach ($fields as $fieldId) {
                        $access = 'private'; //private is default if not set
                        if (isset($fields_access[$fieldId]) && in_array($fields_access[$fieldId], $allowedAccessLevels))
                            $access = $fields_access[$fieldId];

                        $accessLevels[$fieldId] = $access;
                    }
                }

                foreach (self::$STATIC_FIELDS as $name => $title) {
                    $access = 'private'; //private is default if not set
                    if (isset($fields_access[$name]) && in_array($fields_access[$name], $allowedAccessLevels))
                        $access = $fields_access[$name];

                    $accessLevels[$name] = $access;
                }
            }

            //override global config with personal user config
            if (isset($user->data['fields_access'])) {
                foreach ($user->data['fields_access'] as $fid => $access) {
                    $accessLevels[$fid] = $access;
                }
                $user->data['fields_access'] = $accessLevels;
            }

            $fields = array();
            foreach ($accessLevels as $fId => $access) {
                if (self::checkFieldAccess($currentUserFieldAccessLevel, $access)) {
                    $fields[] = $fId;
                } else {
                    //static fields
                    if (isset($user->{$fId}))
                        $user->{$fId} = null;
                }
            }
            //endregion

            if (count($fields)) {
                $selectField = getSM('fields_table')->getById($fields)->toArray();
                $customFields = $fields_api->generate($selectField, (array)$user);
            }
        }

        $viewModel = new ViewModel();
        $viewModel->setTemplate('user/user/view');
        $viewModel->setVariables(array(
            'user' => $user,
            'specialInformationShow' => $specialInformationShow,
            'path' => self::getPathPrefix(),
            'customFields' => $customFields,
            'columnSizes' => $columnSizes
        ));

        return $viewModel;
    }

    private static function getPathPrefix()
    {
        $route = getSM('Request')->getRequestUri();
        $admin_route = strpos($route, 'admin') > -1;
        return $admin_route ? 'admin' : 'app';
    }

    private static function checkFieldAccess($visitor, $field)
    {
        //visitor can see private fields so don't matter what access the field have
        if ($visitor == 'private')
            return true;
        if ($visitor == 'public' && $field == 'public')
            return true;
        if ($visitor == 'members' && $field != 'private')
            return true;

        return false;
    }
}