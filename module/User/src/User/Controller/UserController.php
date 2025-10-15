<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace User\Controller;


use Application\API\App;
use Application\API\Breadcrumb;
use File\API\File;
use Localization\API\Date;
use Mail\API\Mail;
use ___PHPSTORM_HELPERS\this;
use DataView\Lib\Button;
use DataView\Lib\Column;
use DataView\Lib\Custom;
use DataView\Lib\DataGrid;

use DataView\Lib\DeleteButton;
use DataView\Lib\EditButton;
use DataView\Lib\Visualizer;
use User\API\Flood;
use User\API\User;
use User\Model\RoleTable;
use User\Model\UserTable;
use Zend\Crypt\Password\Bcrypt;
use Zend\Db\Sql\Expression;
use System\Controller;
use Application\Model\Config;
use User\Form;
use Zend\Db\Sql\Predicate\NotIn;
use Zend\Db\Sql\Select;
use Zend\View\Model\JsonModel;
use User\Module as UserModule;
use Zend\View\Model\ViewModel;

class UserController extends Controller\BaseAbstractActionController
{
    private function getPathPrefix()
    {
        $route = $this->getEvent()->getRouteMatch()->getMatchedRouteName();
        $admin_route = strpos($route, 'admin') > -1;
        return $admin_route ? 'admin' : 'app';
    }

    public function registerAction()
    {
        if (current_user()->id) //user is already logged in
            return $this->redirect()->toRoute('app/user');

        $roles = null;
        $user_config = getConfig('user_config');

        $allow_register = isset($user_config->varValue['allow_register']) && $user_config->varValue['allow_register'] == '1';
        if (!$allow_register) { //new registration is not allowed
            $this->flashMessenger()->addErrorMessage(t('New user registration has been disabled by site admin!'));
            return $this->redirect()->toRoute('app/front-page');
        }


        $roles_data = isset($user_config->varValue['register_roles']) ? $user_config->varValue['register_roles'] : array();
        if ($roles_data && count($roles_data) && has_value($roles_data[0])) {
            $roles_data = $this->getRoleTable()->get($roles_data);
            if ($roles_data instanceof \Zend\Db\ResultSet\HydratingResultSet)
                $roles_data = $roles_data->toArray();
            else
                $roles_data = array($this->getRoleTable()->toArray($roles_data));

        } else {
            $roles_data = array(RoleTable::getMemberRole());
        }

        $selected_role = $this->params()->fromRoute('roleId', false);
        if (!$selected_role) {
            if (count($roles_data) == 1) { //there is only one role , so just redirect to register with that role
                $roles_data = array_shift($roles_data);
                return $this->redirect()->toRoute('app/user/register',
                    array('roleId' => $roles_data['id'], 'roleName' => $roles_data['roleName']));
            }

            $this->viewModel->setVariables(array('roles' => $roles_data));
            $this->viewModel->setTemplate('user/user/pre-register');
            return $this->viewModel;
        } else {
            $role = null;
            foreach ($roles_data as $current_role) {
                if ($current_role['id'] == $selected_role) {
                    $role = $current_role;
                    break;
                }
            }
            if (!$role) {
                $this->flashMessenger()->addErrorMessage('You are NOT authorized to register as the selected user role');
                return $this->redirect()->toRoute('app/front-page');
            } else
                $selected_role = $role;
        }

        //general profile
        $general_details = isset($user_config->varValue['general_details_in_register_form']) && $user_config->varValue['general_details_in_register_form'] == '1';
        $countryId = $stateId = $cityId = array();
        if ($general_details) {
            $countryId = $this->getCountryTable()->getArray();
            $profile = $this->params()->fromPost('profile', false);
            if ($profile) {
                $selected_country = isset($profile['countryId']) ? $profile['countryId'] : 0;
//                $selected_country = $selected_country ? $selected_country : current(array_slice(array_keys($countryId), 1, 1));
                if ($selected_country) {
                    $stateId = $this->getStateTable()->getArray($selected_country);
                    $selected_state = isset($profile['stateId']) ? $profile['stateId'] : 0;
//                    $selected_state = $selected_state ? $selected_state : current(array_slice(array_keys($stateId), 1, 1));
                    if ($selected_state)
                        $cityId = $this->getCityTable()->getArray($selected_state);
                }
            }
        }
        //custom profile
        $user_config_advance = getConfig('user_config_advance');
        $fields = null;
        if (isset($user_config_advance->varValue['roleType_fields'])) {
            $roleType_fields = $user_config_advance->varValue['roleType_fields'];
            if (isset($roleType_fields[$selected_role['id']])) {
                foreach ($roleType_fields[$selected_role['id']] as $id => $value) {
                    if ($value == '1')
                        $fields[] = $id;
                }
            }
        }
//        $user = new \User\Model\User();
        $form = new Form\Register(getSM()->get('db_adapter'), $general_details, $countryId, $stateId, $cityId, $fields);
        $form->get('buttons')
            ->remove('submit-new')
            ->get('submit')
            ->setValue('Register');
        $form->setAction(url('app/user/register', array('roleId' => $selected_role['id'], 'roleName' => App::prepareUrlString($selected_role['roleName']))));
//        $form->bind($user);
        if ($fields && count($fields)) {
            $inputs = $this->getFieldsApi()->loadFieldsById($fields, $form, $form->get('profile2'));
            $form->addInputFilters(array('profile2' => $inputs));
        } else
            $form->addInputFilters();

        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            if (isset($post['buttons']['cancel'])) {
                return $this->redirect()->toRoute('app/front-page');
            }
            if (isset($post['buttons']['submit'])) {
                $form->setData($this->request->getPost());
                if ($form->isValid()) {
                    $data = $form->getData();

                    $data['roles'] = array($selected_role['id']);
//                    if ((int)$selected_role['id'] != RoleTable::MEMBER)
//                        $data['roles'][] = RoleTable::MEMBER;


                    //region User Status
                    $status = -1;
                    if (isset($user_config_advance->varValue['user'])) {
                        if (isset($user_config_advance->varValue['user']['status'])) {
                            foreach ($data['roles'] as $val) {
                                if (isset($user_config_advance->varValue['user']['status'][$val])) {
                                    $__status = $user_config_advance->varValue['user']['status'][$val];
                                    if ($__status != -1) {
                                        if ($status != 1)
                                            $status = $__status;
                                    }
                                }
                            }
                        }
                    }
                    if ($status == -1) {
                        if (isset($user_config->varValue['new_user_unapproved'])) {
                            $new_user_unapproved = $user_config->varValue['new_user_unapproved'];
                            if ($new_user_unapproved == '1')
                                $status = 0;
                            else
                                $status = 1;
                        } else
                            $status = 1;
                    }
                    //endregion

                    $data['basic']['accountStatus'] = $status;
                    //end

                    $user = new \stdClass();
                    $user->username = $data['basic']['username'];
                    $user->password = $data['basic']['password'];

                    $userId = User::Save($data, $fields);
                    //TODO move to api
                    if (isset($data['basic']['displayName']))
                        $displayName = $data['basic']['displayName'];
                    else
                        $displayName = $data['basic']['username'];
                    //region send mail

                    $notify = getNotifyApi();
                    if ($notify) {
                        $email = $notify->getEmail();
                        $email->to = $data['basic']['email'];
                        $email->from = Mail::getFrom();
                        $email->subject = t('Account Registration');
                        $email->entityType = 'User';
                        $email->queued = 0;
                        $params = array(
                            '__USERNAME__' => $data['basic']['username'],
                            '__PASS__' => $data['basic']['password'],
                            '__DISPLAY_NAME__' => $displayName,
                            '__URL__' => App::siteUrl() . url('app/user/login')
                        );
                        $notify->notify('User', 'user_registered', $params);

                        //endregion

                        //region send mail
                        //TODO move to api
//                    $url = "<a target='_blank' href='" . App::siteUrl() . url('app/user/verified-account', array('id' => base64_encode($userId))) . "' >" . App::siteUrl() . url('app/user/verified-account', array('id' => base64_encode($userId))) . "</a>";
//                    if (isset($user_config->varValue['templates']) && isset($user_config->varValue['templates']['confirmationMailTemplate'])) {
//                        $mailTemplateId = $user_config->varValue['templates']['confirmationMailTemplate'];
//                        $html = App::RenderTemplate($mailTemplateId, array(
//                            '__URL__' => $url,
//                        ));
//                    } else {
//                        $mailTemplate = new ViewModel(array(
//                            'url' => $url,
//                        ));
//                        $mailTemplate->setTemplate('user/user/confirm-email-template');
//                        $html = $this->render($mailTemplate);
//                    }
                        $email = $notify->getEmail();
                        $email->to = $data['basic']['email'];
                        $email->from = Mail::getFrom();
                        $email->subject = t('Verify Email Address');
                        $email->entityType = 'User';
                        $email->queued = 0;

                        $params['__URL__'] = App::siteUrl() . url('app/user/verify-email', array('id' => base64_encode($userId)));
                        $notify->notify('User', 'user_registered', $params);
                    }
//                    send_mail(
//                        $data['basic']['email'],
//                        Mail::getFrom('mail_config'),
//                        t('Verified Link'),
//                        $html,
//                        \User\Module::ENTITY_TYPE_VERIFIED_ACCOUNT,
//                        0
//                    );
                    //endregion
                    $this->flashMessenger()->addSuccessMessage('Your account has been successfully created.');
                    $this->flashMessenger()->addSuccessMessage('An email containing the verification link has been sent to your email address, to verify your email click on that link');
                    $this->flashMessenger()->addSuccessMessage('Thanks for your choice');

                    if ($status == 1) {
                        $user = getSM('user_table')->login($user);
                        if (!is_object($user))
                            $this->flashMessenger()->addErrorMessage($user);

                        $session = App::getSession();
                        if ($session->offsetExists('register_redirect')) {
                            $redirect = $session->offsetGet('register_redirect');
                            $session->offsetUnset('register_redirect');
                            return $this->redirect()->toUrl(urldecode($redirect));
                        }

                        $this->redirect()->toRoute('app/user/user-profile', array('id' => $userId));
                    } else {
                        $this->viewModel->setTemplate('user/user/not-verified');
                        return $this->viewModel;
                    }
                } else
                    $this->formHasErrors();
            }
        }

        $this->viewModel->setVariables(
            array(
                'form' => $form,
                'general_details' => $general_details,
                'roleName' => $selected_role['roleName'],
                'fields' => ($fields && count($fields))
            ));
        $this->viewModel->setTemplate('user/user/register');
        return $this->viewModel;
    }

    public function verifyEmailAction()
    {
        $id = $this->params()->fromRoute('id', false);
        if (!$id)
            return $this->invalidRequest('app/front-page');


        $id = base64_decode($id);
        if (!$id)
            return $this->invalidRequest('app/front-page');

        $user = getSM('user_table')->get($id);
        if (!$user)
            return $this->invalidRequest('app/front-page');

        if ($user->emailStatus != 1)
            getSM('user_table')->update(array('emailStatus' => 1), array('id' => $id));

        $this->viewModel->setTemplate('user/user/verify-email');
        $this->viewModel->setVariables(array('emailStatus' => $user->emailStatus, 'accountStatus' => $user->accountStatus));
        return $this->viewModel;
    }

    public function newAction($model = null, $countryId = array(), $stateId = array(), $cityId = array(), $userFields = null)
    {
        if (!$model) {
            $form = new \User\Form\User(getSM()->get('db_adapter'));
            $form->setAction(url('admin/users/new'));
            $form->setAttribute('data-cancel', url('admin/users'));
        } else {
            $form = new \User\Form\User(getSM()->get('db_adapter'), false, $model['basic']['username'], $model['basic']['email'], true, $countryId, $stateId, $cityId, $userFields);
            $form->setAction(url('admin/users/edit', array('id' => $model['basic']['id'])));
            $form->setAttribute('data-cancel', url('admin/users/view', array('id' => $model['basic']['id'])));
            $form->get('buttons')->remove('submit-new');
            $form->setData($model);
        }

        if ($this->request->isPost()) {
            $post = $this->request->getPost();

            if (isset($post['buttons']['cancel'])) {
                return $this->listAction();
            }
            if (isset($post['buttons']['submit']) || isset($post['buttons']['submit-new'])) {

                $form->setData($this->request->getPost());
                if ($form->isValid()) {
                    $data = $form->getData();
                    $data['roles'] = $this->getRoleTable()->filterRoles($data['roles']);

                    if (isset($data['profile']['birthDate']) && !empty($data['profile']['birthDate'])) {
                        $date = $data['profile']['birthDate'];
                        $date = explode('/', $date);
                        if (count($date) == 3) {
                            $data['profile']['birthDate'] = Date::fromDatePicker($date);
                        } else
                            $data['profile']['birthDate'] = null;
                    }

                    if ($model) {
                        $data['basic']['id'] = $model['basic']['id'];
                        $data['profile']['id'] = $model['profile']['id'];
                    }

                    $uid = User::Save($data);
                    if ($model)
                        $this->flashMessenger()->addSuccessMessage('User profile updated');
                    if (!isset($post['buttons']['submit-new'])) {
                        return $this->viewAction($uid);
                    } else {
                        $form->setData(array());
                    }
                } else
                    $this->formHasErrors();
            }
        }

        $this->viewModel->setTemplate('user/user/new');
        $this->viewModel->setVariables(array('form' => $form, 'user' => $model));
        return $this->viewModel;
    }

    public function editAction()
    {
        $id = $this->params()->fromRoute('id', false);
        if (!$id)
            return $this->invalidRequest('app/user');

        if (!isCurrentUser($id) && !isAllowed(UserModule::ADMIN_USER_EDIT_ALL))
            return $this->accessDenied();

        if (!isCurrentUser($id)) {
            $currentMaxRoleId = $this->getRoleTable()->getMaxLevel(current_user()->id);
            $userMaxRoleId = $this->getRoleTable()->getMaxLevel($id);
            if ($userMaxRoleId > $currentMaxRoleId)
                return $this->accessDenied();
        }

        $user = $this->getUserTable()->getForEdit($id);
        unset($user['basic']['password']);

        //region Address
        $countryId = $stateId = $cityId = array();
        $countryId = $this->getCountryTable()->getArray();

        $profile = $this->params()->fromPost('profile', false);

        if (isset($user['profile']) && isset($user['profile']['birthDate'])) {
            $user['profile']['birthDate'] = Date::toDatePicker($user['profile']['birthDate']);
        }

        $selected_country = $profile && isset($profile['countryId']) ? $profile['countryId'] : false;
        if (!$selected_country && isset($user['profile']['countryId']) && !empty($user['profile']['countryId']))
            $selected_country = $user['profile']['countryId'];
//        $selected_country = $selected_country ? $selected_country : current(array_slice(array_keys($countryId), 1, 1));
        if ($selected_country) {
            $stateId = $this->getStateTable()->getArray($selected_country);
            $selected_state = $profile && isset($profile['stateId']) ? $profile['stateId'] : false;
            if (!$selected_state && isset($user['profile']['stateId']) && !empty($user['profile']['stateId']))
                $selected_state = $user['profile']['stateId'];
//            $selected_state = $selected_state ? $selected_state : current(array_slice(array_keys($stateId), 1, 1));
            if ($selected_state)
                $cityId = $this->getCityTable()->getArray($selected_state);
        }
        //endregion

        //region User Config
        /* @var $config Config */
        $config = getConfig('user_config');
        $all_fields = $this->getFieldsTable()->getArray('user_profile');

        $userFields = array();
        $roleType_fields = @$config->varValue['roleType_fields'];
        if ($roleType_fields) {
            foreach ($roleType_fields as $roleId => $fields) {
                if (in_array($roleId, $user['roles'])) {
                    foreach ($fields as $fieldId => $value) {
                        if ($value == '1') {
                            $userFields[$fieldId] = $all_fields[$fieldId];
                        }
                    }
                }
            }
        }

        $allowedAccessLevels = array('private', 'members', 'public');
        $accessLevels = array();
        $fields_access = @$config->varValue['fields_access'];

        if ($fields_access) {
            foreach ($userFields as $fieldId => $field) {
                $access = 'private'; //private is default if not set
                if (isset($fields_access[$fieldId]) && in_array($fields_access[$fieldId], $allowedAccessLevels))
                    $access = $fields_access[$fieldId];

                $accessLevels[$fieldId] = $access;
            }
            foreach (User::$STATIC_FIELDS as $fieldId => $field) {
                $access = 'private'; //private is default if not set
                if (isset($fields_access[$fieldId]) && in_array($fields_access[$fieldId], $allowedAccessLevels))
                    $access = $fields_access[$fieldId];

                $accessLevels[$fieldId] = $access;
            }
        }
        if (isset($user['basic']['data']['fields_access'])) {
            foreach ($user['basic']['data']['fields_access'] as $fid => $access) {
                $accessLevels[$fid] = $access;
            }
        }
        $user['basic']['data']['fields_access'] = $accessLevels;
        //endregion

        return $this->newAction($user, $countryId, $stateId, $cityId, $userFields);
    }

    public function editImageAction()
    {
        $id = $this->params()->fromRoute('id', false);
        if (!$id)
            return $this->invalidRequest('app/user');

        if (!isCurrentUser($id) && !isAllowed(UserModule::ADMIN_USER_EDIT_IMAGE_ALL))
            return $this->accessDenied();

        if (!isCurrentUser($id)) {
            $currentMaxRoleId = $this->getRoleTable()->getMaxLevel(current_user()->id);
            $userMaxRoleId = $this->getRoleTable()->getMaxLevel($id);
            if ($userMaxRoleId > $currentMaxRoleId)
                return $this->accessDenied();
        }

        $user = getSM()->get('user_table')->get($id);
        $form = prepareForm(new \User\Form\EditImage(), array('submit-new'));
        $form->setAction(url('admin/users/edit-image', array('id' => $id)));
        $form->setAttribute('data-cancel', url('admin/users/view', array('id' => $id)));
        $form->setData(array('image' => $user->image));

        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            if (isset($post['buttons']['cancel'])) {
                return $this->viewAction($id);
            }
            if (isset($post['buttons']['submit']) || isset($post['buttons']['submit-new'])) {
                $post = array_merge_recursive(
                    $this->request->getPost()->toArray(),
                    $this->request->getFiles()->toArray()
                );
                $form->setData($post);
                if ($form->isValid()) {
                    $data = $form->getData();
                    @unlink(ROOT . $user->image);
                    $image = File::MoveUploadedFile($data['image']['tmp_name'], PUBLIC_FILE . '/userprofile', $data['image']['name']);
                    $data = array('image' => $image);
                    $data['userId'] = $id;
                    $form->setData($data);
                    $user->image = $image;
                    $this->getServiceLocator()->get('user_profile_table')->update(array('image' => $image), array('userId' => $id));
                    return $this->viewAction($id);
                }
            }
        }

        $this->viewModel->setTemplate('user/user/edit-image');
        $this->viewModel->setVariables(array('form' => $form, 'user' => $user));
        return $this->viewModel;
    }

    public function deleteAction()
    {
        if ($this->request->isPost()) {
            $id = $this->params()->fromPost('id', false);
            if (!$id)
                return $this->invalidRequest();

            if (!isCurrentUser($id) && !isAllowed(UserModule::ADMIN_USER_DELETE_ALL))
                return $this->accessDenied();

            if (isCurrentUser($id) && !isAllowed(UserModule::ADMIN_USER_DELETE_OWN))
                return $this->accessDenied();


            if (!isCurrentUser($id)) {
                $currentMaxRoleId = $this->getRoleTable()->getMaxLevel(current_user()->id);
                if (is_array($id)) {
                    $userIdToDelete = array();
                    $errors = array();
                    foreach ($id as $singleId) {
                        $userMaxRoleId = $this->getRoleTable()->getMaxLevel($singleId);
                        if ($userMaxRoleId > $currentMaxRoleId)
                            $errors[] = sprintf(t('You are NOT authorized to remove user id:%s'), $singleId);
                        else
                            $userIdToDelete[] = $singleId;
                    }
                    $id = $userIdToDelete;
                } else {
                    $userMaxRoleId = $this->getRoleTable()->getMaxLevel($id);
                    if ($userMaxRoleId > $currentMaxRoleId)
                        return $this->accessDenied();
                }
            }

            User::Delete($id);
            return new JsonModel(array('status' => 1));

        }
        return new JsonModel(array('status' => 0, 'msg' => t('Unfortunately something went wrong during this operation !')));
    }

    public function viewAction($userId = null)
    {
        if (!$userId)
            $userId = $this->params()->fromRoute('id', false);
        $userId_query = $this->params()->fromQuery('id', false);

        //TODO check why is this required ??? and where its been used
        if ($userId_query) {
            $user = $this->getUserTable()->get($userId_query);
            $this->viewModel->setTemplate('user/user/view');
            $this->viewModel->setVariables(array(
                'user' => $user,
                'specialInformationShow' => TRUE,
                'path' => $this->getPathPrefix(),
                'type' => 'guest'
            ));
            return $this->viewModel;
        }

        if (!$userId && !current_user()->id)
            return $this->loginAction();

        if (!$userId && current_user()->id)
            $userId = current_user()->id;

        $status = $this->params()->fromQuery('status', false);
        if ($status) {
            switch ($status) {
                case 'access-denied':
                    $this->flashMessenger()->addErrorMessage(t('Access Denied !'));
                    $this->flashMessenger()->addErrorMessage(t('You don\'t have required permissions to access the requested page.'));
                    break;
            }
        }

        if ($userId) {
            Breadcrumb::AddMvcPage('Profile', 'app/user', array('id' => $userId));

            return User::getUserProfile($userId);
        }
        return $this->invalidRequest();
    }

    public function loginAction()
    {
        $redirect = $this->params()->fromQuery('redirect', false);

        if (current_user()->id) {
            if ($redirect)
                return $this->redirect()->toUrl(urldecode($redirect));
            return $this->redirect()->toRoute('app/user');
        }

        $status = $this->params()->fromQuery('status', false);
        if ($status) {
            switch ($status) {
                case 'access-denied':
                    $this->flashMessenger()->addErrorMessage(t('Access Denied !'));
                    $this->flashMessenger()->addErrorMessage(t('You don\'t have required permissions to access the requested page.'));
                    break;
            }
        }

        $floodStatus = Flood::IsFlooded();
        if ($floodStatus !== false)
            return $floodStatus;

        $user = new \User\Model\User();
        $form = new \User\Form\Login($redirect);
        $form->bind($user);

        $view = array('form' => $form);
        if ($redirect) {
            App::getSession()->offsetSet('register_redirect', $redirect);
        }
        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            if (isset($post->submit)) {
                $form->setData($this->request->getPost());
                if ($form->isValid()) {
//                    $user->rememberMe = $form->get('rememberMe')->getValue();
                    $user = $this->getUserTable()->login($user);

                    if (!is_object($user)) {
                        $view['error'] = $user;
                        if (Flood::$FloodCount >= 5)
                            return Flood::GetFloodedView();
                    } else {
                        $roles = array();
                        foreach ($user->roles as $role) {
                            $roles[] = $role['id'];
                        }

                        if (App::getSession()->offsetExists('register_redirect'))
                            App::getSession()->offsetUnset('register_redirect');

                        if ($redirect)
                            return $this->redirect()->toUrl(urldecode($redirect));
                        elseif (isAllowed(\Application\Module::ADMIN, $roles)) {
                            return $this->redirect()->toRoute('admin');
                        } else
                            return $this->redirect()->toRoute('app/user');
                    }
                }
            }
        }

        Flood::RenderMessage($this);
        $this->viewModel->setTemplate('user/user/login');
        $this->viewModel->setVariables($view);
        return $this->viewModel;
    }

    public function logoutAction()
    {
        $authService = $this->getServiceLocator()->get('user_auth_service');
        $authService->getStorage()->clear();
        getSM()->get('session_manager')->forgetMe();
        $front = url('app/front-page');
        return $this->redirect()->toUrl($front);
    }

    public function accessDeniedAction()
    {
        return $this->viewModel;
    }

    public function listAction()
    {
        // $this->params()->fromQuery()


        $visibleRoles = $this->getRoleTable()->getVisibleRoles();
        $visibleRolesArray = array();
        foreach ($visibleRoles as $row) {
            $visibleRolesArray[$row->id] = str_repeat('|--', $row->indent) . $row->roleName;
        }

        $currentMaxRoleId = getSM('role_table')->getMaxLevel(current_user()->id);

        $grid = new DataGrid('user_table');
        $grid->route = 'admin/users';

        $selectRoleId = new Select('tbl_roles');
        $selectRoleId->columns(array('id'))->where(array('level > ?' => $currentMaxRoleId));

        $selectUserId = new Select('tbl_users_roles');
        $selectUserId->columns(array('userId'))->where->in('roleId', $selectRoleId);

        $select = $grid->getSelect();
        $select
            ->join(array('ur' => 'tbl_users_roles'), 'tbl_users.id=ur.userId', array('roleId'), 'left')
            ->join(array('r' => 'tbl_roles'), 'ur.roleId=r.id', array('roleName' => new Expression('GROUP_CONCAT(r.roleName)')), 'left')
            ->group('tbl_users.username')
            ->order(array('tbl_users.id ASC'))
            ->where->addPredicate(new NotIn($grid->getTableGateway()->table . '.id', $selectUserId));


        $id = new Column('id', 'Id', array('headerAttr' => array('width' => '15px', 'align' => 'center')));
        $grid->setIdCell($id);
        $username = new Column('username', 'Username');
        $email = new Column('email', 'Email');
        $displayName = new Column('displayName', 'Display Name');
        $roleName = new Custom('roleName', 'Roles',
            function (Column $data) {
                $roles = explode(',', $data->dataRow->roleName);
                for ($i = 0; $i < count($roles); $i++) {
                    $roles[$i] = t($roles[$i]);
                }
                return implode(',', $roles);
            }
        );

        $roleId = new Column('roleId', 'User Roles');
        $roleId->selectFilterData = $visibleRolesArray;
        $roleId->setTableName('ur');

        $emailStatus = new Visualizer('emailStatus', 'Email Status',
            array(
                0 => 'glyphicon glyphicon-question-sign text-warning grid-icon',
                1 => 'glyphicon glyphicon-info-sign text-info grid-icon',
                2 => 'glyphicon glyphicon-ok text-success grid-icon'
            ),
            array(
                0 => t('Unknown'),
                1 => t('Validation Email Sent'),
                2 => t('Validated')
            ), array());

        $accountStatusValues = array();
        foreach (UserTable::$accountStatus as $key => $value) {
            $accountStatusValues[$key] = t($value);
        }
//        $accountStatus = new Visualizer('accountStatus', 'Account Status',
//            array(
//                0 => 'status-not-approved',
//                1 => 'status-approved',
//                2 => 'status-temporary-locked',
//                3 => 'status-locked',
//                4 => 'status-banned',
//                5 => 'status-deleted',
//            ),
//            $accountStatusValues);

        $statusArrayFilter = UserTable::$accountStatus;
        $statusArrayFilter[-1] = 'Inherit';
        $status = new \DataView\Lib\Select('accountStatus', 'Status',
            $accountStatusValues,
            array('0' => 'inactive', '1' => 'active'),
            array('headerAttr' => array('width' => '50px'))
        );

        $view = new Button('View', function (Button $col) {
            $col->route = 'admin/users/view';
            $col->routeParams = array('id' => $col->dataRow->id);
            $col->icon = 'glyphicon glyphicon-eye-open';
        }, array(
            'headerAttr' => array('width' => '34px'),
            'attr' => array('align' => 'center'),
            'contentAttr' => array('class' => array('ajax_page_load', 'btn', 'btn-default'))
        ));
        $reset = new Button('Password Reset', function (Button $col) {
            $col->route = 'admin/users';
            $col->contentAttr['data-url'] = url('admin/users/password-reset', array('id' => $col->dataRow->id));
            $col->icon = 'glyphicon glyphicon-refresh';
        }, array(
            'headerAttr' => array('width' => '34px'),
            'attr' => array('align' => 'center'),
            'contentAttr' => array(
                'class' => array('ajax_page_load', 'btn', 'btn-default'),
                'id' => 'password-reset',
            )
        ));
        $edit = new EditButton();
        $delete = new DeleteButton();

        $grid->addColumns(array($id, $username, $email, $displayName, $roleName, $emailStatus, $status, $reset, $view, $edit, $delete));
        $grid->setSelectFilters(array($roleId));
        $grid->addNewButton('Add New User');
        $grid->addDeleteSelectedButton();
        if (getSM()->has('transactions_api') && isAllowed(\Payment\Module::ADMIN_PAYMENT_TRANSACTIONS))
            $grid->addButton('PAYMENT_CHANGE_BALANCE', 'PAYMENT_CHANGE_BALANCE', false, 'admin/payment/transactions/new', 'ajax_page_load', null, 'direct-deposit-money', array(), array(), array(), 'glyphicon glyphicon-usd text-success');
        $this->viewModel->setTemplate('user/user/list');
        $this->viewModel->setVariables(array(
            'grid' => $grid->render(),
            'buttons' => $grid->getButtons(),
        ));
        return $this->viewModel;
    }

    public function configAction()
    {
        /* @var $config Config */
        $config = getConfig('user_config');
        if (!isset($config->varValue['limit_login']))
            $config->varValue['limit_login'] = 0;

        $roles = $this->getRoleTable()->getVisibleRoles();
        $nestedRoles = $this->getRoleTable()->makeIndentedArray($roles, 'roleName');
        $fields_list = null;
        if ($this->hasFieldsApi())
            $fields_list = $this->getFieldsTable()->getArray('user_profile');
//        $mailTemplate = getSM('template_table')->getArray();
        $form = new Form\Config($roles, $nestedRoles, $fields_list);
        $form = prepareConfigForm($form);
        $form->setData($config->varValue);

        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            if (isset($post['buttons']['submit'])) {
                $form->setData($this->request->getPost());

                if ($form->isValid()) {
                    $config->setVarValue($form->getData());
                    $this->getServiceLocator()->get('config_table')->save($config);
                    $config->varValue = unserialize($config->varValue);
                    db_log_info("User Configs changed");
                    $this->flashMessenger()->addInfoMessage('User configs saved successfully');
                } else {
                    $this->formHasErrors();
                }
            }
        }


        $this->viewModel->setVariables(array('form' => $form, 'fields_access' => @$config->varValue['fields_access']));
        $this->viewModel->setTemplate('user/user/config');
        return $this->viewModel;
    }

    public function changePasswordAction()
    {
        $id = $this->params()->fromRoute('id', false);
        if (!$id)
            return $this->invalidRequest('app/user');

        if (!isCurrentUser($id) && !isAllowed(UserModule::ADMIN_USER_EDIT_ALL))
            return $this->accessDenied();

        if (!isCurrentUser($id)) {
            $currentMaxRoleId = $this->getRoleTable()->getMaxLevel(current_user()->id);
            $userMaxRoleId = $this->getRoleTable()->getMaxLevel($id);
            if ($userMaxRoleId > $currentMaxRoleId)
                return $this->accessDenied();
        }

        $form = prepareForm(new \User\Form\ChangePassword(), array('submit-new', 'cancel'));
        $form->setAction(url('admin/users/change-password', array('id' => $id)));
        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            if (isset($post['buttons']['submit'])) {
                $form->setData($this->request->getPost());

                if ($form->isValid()) {
                    $data = $form->getData();
                    $old_password = getSM()->get('user_table')->checkPassword($id, $data['old_password']);
                    if (!$old_password) {
                        $form->get('old_password')->setMessages(array('The Current Password Is Invalid'));
                    } else {
                        getSM()->get('user_table')->changePassword($id, $data['new_password']);
                        $this->viewAction($id);
                    }
                }
            }
        }

        $this->viewModel->setTemplate('user/user/change-password');
        $this->viewModel->setVariables(array('form' => $form, 'userId' => $id));
        return $this->viewModel;
    }

    public function advanceConfigAction()
    {
        /* @var $config Config */
        $config = getConfig('user_config');
        $advance_config = getConfig('user_config_advance');
        $all_roles = $this->getRoleTable()->getVisibleRoles();
        $all_fields = null;
        if ($this->hasFieldsApi())
            $all_fields = $this->getFieldsTable()->getArray('user_profile');
//        $template = getSM('template_table')->getArray();

        $register_roles = @$config->varValue['register_roles'];
        $roles = array();
        if ($register_roles && is_array($register_roles) && count($register_roles)) {
            foreach ($register_roles as $roleId) {
                if (isset($all_roles[$roleId]))
                    $roles[$roleId] = $all_roles[$roleId];
            }
        }

        $registerFields = array();
        $roleType_fields = @$config->varValue['roleType_fields'];
        if ($roleType_fields) {
            foreach ($roleType_fields as $roleId => $fields) {
                if (isset($roles[$roleId])) {
                    foreach ($fields as $fieldId => $value) {
                        if ($value == '1') {
                            $roles[$roleId]->fields[$fieldId] = $all_fields[$fieldId];
                            $registerFields[$fieldId] = $all_fields[$fieldId];
                        }
                    }
                }
            }
        }
        $form = new Form\AdvanceConfig($roles, $all_roles);
        $form = prepareConfigForm($form);
        $form->setData($advance_config->varValue);

        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            if (isset($post['buttons']['submit'])) {
                $form->setData($this->request->getPost());

                if ($form->isValid()) {
                    $advance_config->setVarValue($form->getData());
                    $this->getConfigTable()->save($advance_config);
                    db_log_info("Advance User Configs changed");
                    $this->flashMessenger()->addInfoMessage('User configs saved successfully');
                }
            }
        }

        $this->viewModel->setVariables(array('form' => $form, 'regFields' => $registerFields));
        $this->viewModel->setTemplate('user/user/advance-config');
        return $this->viewModel;
    }

    public function editCustomProfileAction()
    {
        $uid = $this->params()->fromRoute('id', false);
        if (!$uid)
            return $this->invalidRequest('app/user');

        if (!isCurrentUser($uid) && !isAllowed(UserModule::ADMIN_USER_EDIT_ALL))
            return $this->accessDenied();

        if (!isCurrentUser($uid)) {
            $currentMaxRoleId = $this->getRoleTable()->getMaxLevel(current_user()->id);
            $userMaxRoleId = $this->getRoleTable()->getMaxLevel($uid);
            if ($userMaxRoleId > $currentMaxRoleId)
                return $this->accessDenied();
        }

        $user = $this->getUserTable()->getUser($uid);
        $roles = $this->getUserRoleTable()->getRolesArray($uid);

        //custom profile
        $user_config_advance = getSM()->get('config_table')->getByVarName('user_config');
        $fields = null;
        if (isset($user_config_advance->varValue['roleType_fields'])) {
            $roleType_fields = $user_config_advance->varValue['roleType_fields'];
            foreach ($roles as $role) {
                if (isset($roleType_fields[$role])) {
                    foreach ($roleType_fields[$role] as $id => $value) {
                        if ($value == '1')
                            $fields[] = $id;
                    }
                }
            }
        }
        /* @var $form \User\Form\Custom */
        $form = prepareConfigForm(new \User\Form\Custom());
        $form->setAction(url('admin/users/edit/custom-profile', array('id' => $uid)));

        $data = array();
        $hasFields = true;
        if ($fields && count($fields) && getSM()->has('fields_api')) {
            $this->getFieldsApi()->init('user_profile');
            $inputs = $this->getFieldsApi()->loadFieldsById($fields, $form, $form->get('profile2'));
            $form->get('profile2')->setInputFiltersConfig($inputs);
            $data = array('profile2' => $this->getFieldsApi()->getFieldData($uid, $fields));
            $form->setData($data);
        } else {
            $hasFields = false;
        }

        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            if (isset($post['buttons']['submit'])) {
                $form->setData($this->request->getPost());
                if ($form->isValid()) {
                    $postData = $form->getData();
                    $postData = $postData['profile2'];
                    $postData['id'] = $data['profile2']['id'];
                    $this->getFieldsApi()->save('user_profile', $uid, $postData, $fields);
                    $this->flashMessenger()->addSuccessMessage('Your custom details saved successfully.');
                } else
                    $this->formHasErrors();
            }
        }

        $this->viewModel->setVariables(
            array(
                'form' => $form,
                'user' => $user,
                'hasFields' => $hasFields
            )
        );
        $this->viewModel->setTemplate('user/user/edit-custom-profile');
        return $this->viewModel;
    }

    /**
     * @return \User\Model\RoleTable
     */
    private function getRoleTable()
    {
        return getSM()->get('role_table');
    }

    /**
     * @return \User\Model\UserTable
     */
    private function getUserTable()
    {
        return getSM('user_table');
    }

    /**
     * @return \User\Model\UserRoleTable
     */
    private function getUserRoleTable()
    {
        return getSM('user_role_table');
    }

    public function updateAction()
    {
        if ($this->request->isPost()) {
            $field = $this->params()->fromPost('field', false);
            $value = $this->params()->fromPost('value', false);
            $id = $this->params()->fromPost('id', 0);
            if ($id && $field && has_value($value)) {
                if ($field == 'accountStatus') {
                    $this->getUserTable()->update(array($field => $value), array('id' => $id));

                    $notify = getNotifyApi();
                    if ($notify) {

                        $user = $this->getUserTable()->get($id);
                        $mobile = $user->mobile;
                        $params = array(
                            '__USERNAME__' => $user->username,
                            '__USERCODE__' => $user->userId,
                            '__STATUS__' => t(UserTable::$accountStatus[$value])
                        );

                        if ($mobile && strlen($mobile) == 11) {
                            $sms = $notify->getSms();
                            $sms->to = $mobile;
                        }

                        switch ($value) {
                            case 0:
                                $event = 'account_not_approved';
                                break;
                            case 1:
                                $event = 'account_approved';
                                break;
                            default:
                                $event = 'status_changed';
                        }
                        $notify->notify('User', $event, $params);
                    }

                    //send sms
                    if ($value == 1 || $value == 0) {
//                        $configRealEstate = getSM('config_table')->getByVarName('real_estate_config')->varValue;
//                        $selectRoles = getSM('user_role_table')->getRolesByUserId($id);
//                        $flag = false;
//                        foreach ($selectRoles as $role) //baraye peida kardan role moshavere amlak
//                        {
//                            foreach ($configRealEstate['agentUserRole'] as $val) {
//                                if ($role['roleId'] == $val)
//                                    $flag = true;
//                                if ($flag)
//                                    break;
//                            }
//                            if ($flag)
//                                break;
//                        }
//                        if ($flag) {
//                            $select = getSM('user_table')->get($id);
//                            $config = getSM('config_table')->getByVarName('real_estate_config')->varValue;
//                            if ($select->mobile) {
//                                $msg = '';
//                                if ($value == 1) {
//                                    if (isset($config->varValue['text2-sms-template'])) {
//                                        $smsTemplateId = $config->varValue['text2-sms-template'];
//                                        $msg = App::RenderTemplate($smsTemplateId, array(
//                                            '__USERNAME__' => $select->username,
//                                            '__USERCODE__' => $select->id,
//                                        ));
//                                    } else {
//                                        $msg = t('__USERNAME__ Dear friend, Your account has been verified.');
//                                        $msg = str_replace('__USERNAME__', $select->username, $msg);
//                                    }
//                                } elseif ($value == 0) {
//                                    if (isset($config->varValue['text3-sms-template'])) {
//                                        $smsTemplateId = $config->varValue['text3-sms-template'];
//                                        $msg = App::RenderTemplate($smsTemplateId, array(
//                                            '__USERNAME__' => $select->username,
//                                            '__USERCODE__' => $select->id,
//                                        ));
//                                    } else {
//                                        $msg = t('__USERNAME__ Dear friend, Your account has not verified.');
//                                        $msg = str_replace('__USERNAME__', $select->username, $msg);
//                                    }
//                                }
//                                $resultSms = getSM('sms_api')->send_sms($select->mobile, $msg);
//                            }
//                        }
                    }
                    //end
                    return new JsonModel(array('status' => 1));
                }
            }
        }
        return new JsonModel(array('status' => 0, 'msg' => t('Invalid Request !')));
    }

    public function passwordRecoveryAction()
    {
        $notify = getNotifyApi();
        /* @var $config Config */
//        $config = getConfig('user_config');
        $allowSendSms = false;
        if ($notify)
            $allowSendSms = $notify->isAllowed('User', 'password_recovery', 'sms');
//        if (isset($config->varValue['allow_send_sms_pass']))
//            $allowSendSms = $config->varValue['allow_send_sms_pass'];
        $form = new \User\Form\PasswordRecovery($allowSendSms);

        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                $select = getSM('user_table')->getAll(array('email' => $data['email']))->current();
                if ($select) {
                    $passLoginUser = rand(100, 1000000);
                    $showPassUser = $passLoginUser;
                    $bcrypt = new Bcrypt();
                    $securePassUser = $bcrypt->create($passLoginUser);
                    getSM('user_table')->update(array('password' => $securePassUser), array('id' => $select->id));

                    $mobile = false;
                    if (isset($data['send-sms']) && $data['send-sms'] == '1') {
                        $selectProfile = getSM('user_profile_table')->getAll(array('userId' => $select->id))->current();
                        if (isset($selectProfile->mobile) && !empty($selectProfile->mobile)) {
                            $mobile = $selectProfile->mobile;
                        }
                    }

                    $email = false;
                    if (isset($select->email) && !empty($select->email)) {
                        $email = $select->email;
                    }

                    $notify = $this->notifyPasswordReset($showPassUser, $email, $mobile);

                    if ($email) {
                        if (in_array('email', $notify->sentTypes))
                            $this->flashMessenger()->addSuccessMessage('new password sent to your email address');
                        else
                            $this->flashMessenger()->addWarningMessage('password recovery by email has been disabled by admin');
                    } else
                        $this->flashMessenger()->addErrorMessage("Your account dose not have a email address to send the password");

                    if ($allowSendSms) {
                        if ($mobile) {
                            if (in_array('sms', $notify->sentTypes))
                                $this->flashMessenger()->addSuccessMessage('new password sent to your cellphone');
                            else
                                $this->flashMessenger()->addWarningMessage('password recovery by sms has been disabled by admin');
                        } else
                            $this->flashMessenger()->addErrorMessage('your account dose not have any mobile number to send the new password');
                    }
                } else
                    $this->flashMessenger()->addErrorMessage('There is no account associated with this email address');
            }
        }

        $this->viewModel->setVariables(array(
            'form' => $form,
        ));
        $this->viewModel->setTemplate('user/user/password-recovery');
        return $this->viewModel;
    }

    public function passwordResetAction()
    {
        $id = $this->params()->fromRoute('id', false);
        if (!$id)
            return new JsonModel(array('status' => 0, 'msg' => t('Invalid request. user id has not been provided')));

        if (!isAllowed(\User\Module::ADMIN_USER_PASSWORD_RESET))
            return new JsonModel(array('status' => 0, 'msg' => t('You are not authorized to changed any users password')));

        $select = getSM('user_table')->get($id);
        if (!$select)
            return new JsonModel(array('status' => 0, 'msg' => t('Invalid request. user with this id not found')));

        $passLoginUser = rand(100, 1000000);
        $showPassUser = $passLoginUser;
        $bcrypt = new Bcrypt();
        $securePassUser = $bcrypt->create($passLoginUser);
        getSM('user_table')->update(array('password' => $securePassUser), array('id' => $id));

        $this->notifyPasswordReset($showPassUser, $select->email, $select->mobile);
        return new JsonModel(array('status' => 1, 'msg' => t("This account's password has been reset and the account holder has been notified")));
    }

    /**
     * @param $password
     * @param $emailAddress
     * @param $mobile
     * @return \Notify\API\Notify
     */
    private function notifyPasswordReset($password, $emailAddress, $mobile)
    {
        $notify = getNotifyApi();

        $params = array();


        $siteName = App::siteUrl();
        $params['__URL__'] = $siteName . url('app/user/login');
        $params['__SITE__'] = $siteName;
        $params['__PASS__'] = $password;

        if ($emailAddress) {
            $email = $notify->getEmail();
            $email->to = $emailAddress;
            $email->from = Mail::getFrom();
            $email->subject = t('Password Recovery');
            $email->entityType = 'User';
            $email->queued = 0;
        }

        if ($mobile && strlen($mobile) == 11) {
            $sms = $notify->getSms();
            $sms->to = $mobile;
        }

        $notify->notify('User', 'password_recovery', $params);

        return $notify;
    }

    public function searchAction()
    {
        $data = array('users' => array(), 'total' => 0);
        if ($this->request->isPost()) {
            $term = $this->params()->fromPost('q', false);
            if ($term) {
                $page = $this->params()->fromPost('page', 1);
                $page_limit = $this->params()->fromPost('page_limit', 10);
                $params = $this->params()->fromPost('params', false);
                $result = $this->getUserTable()->search($term, $page, $page_limit, $params);
                if ($result) {
                    foreach ($result as $row) {
                        $name = '';
                        if (!empty($row->firstName))
                            $name .= $row->firstName;
                        if (!empty($row->lastName)) {
                            if (!empty($name))
                                $name .= ' ';
                            $name .= $row->lastName;
                        }

                        $data['users'][] = array(
                            'id' => $row->id,
                            'text' => trim($row->username),
                            'displayName' => trim($row->displayName),
                            'name' => trim($name),
                        );
                    }
                    $data['total'] = $result->getTotalItemCount();
                }
            }
        }

        return new JsonModel($data);
    }
}