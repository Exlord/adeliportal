<?php
namespace User\View\Helper;

use System\View\Helper\BaseHelper;
use User\API\User;

/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Email adeli.farhad@gmail.com
 * Date: 6/1/13
 * Time: 8:36 PM
 */
class UserInfo extends BaseHelper
{
    public function __invoke($id, $columnSizes = array('col-md-3 col-sm-6','col-md-9 col-sm-6'))
    {
        return $this->view->render(User::getUserProfile($id, false, $columnSizes));
//        $hasFieldsApi = getSM()->has('fields_api');
//        $fields_table = null;
//        if ($hasFieldsApi) {
//            /* @var $fields_api \Fields\API\Fields */
//            $fields_api = $this->getFieldsApi();
//            $fields_table = $this->getFieldsApi()->init('user_profile');
//        }
//        $user = getSM()->get('user_table')->get($id,$fields_table);
//        // custom profile
//        $roles = $this->getUserRoleTable()->getRolesArray($id);
//        $user_config_advance = getSM()->get('config_table')->getByVarName('user_config');
//
//        $customFields = null;
//        if ($hasFieldsApi) {
//            $fields = null;
//            if (isset($user_config_advance->varValue['roleType_fields'])) {
//                $roleType_fields = $user_config_advance->varValue['roleType_fields'];
//                foreach ($roles as $role) {
//                    if (isset($roleType_fields[$role])) {
//                        foreach ($roleType_fields[$role] as $id => $value) {
//                            if ($value == '1')
//                                $fields[] = $id;
//                        }
//                    }
//                }
//            }
//
//            $selectField = getSM('fields_table')->getById($fields)->toArray();
//            $customFields = $fields_api->generate($selectField, (array)$user);
//        }
//        return $this->view->render('user/user/view',
//            array(
//                'user' => $user,
//                'specialInformationShow' => FALSE,
//                'path' => $path,
//                'customFields' => $customFields,
//            )
//        );
    }

    /**
     * @return \Fields\API\Fields
     */
    protected function getFieldsApi()
    {
        return getSM()->get('fields_api');
    }

    protected function hasFieldsApi()
    {
        return getSM()->has('fields_api');
    }

    /**
     * @return \User\Model\UserRoleTable
     */
    private function getUserRoleTable()
    {
        return getSM('user_role_table');
    }
}