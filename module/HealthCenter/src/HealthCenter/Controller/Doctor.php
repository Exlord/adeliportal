<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 8/23/14
 * Time: 9:45 AM
 */

namespace HealthCenter\Controller;


use Application\API\App;
use DataView\Lib\Button;
use DataView\Lib\Column;
use DataView\Lib\Custom;
use DataView\Lib\DataGrid;
use DataView\Lib\Date;
use HealthCenter\Model\DoctorProfileTable;
use HealthCenter\Model\DoctorRefTable;
use HealthCenter\Model\DoctorReservationTable;
use HealthCenter\Model\DoctorTable;
use Mail\API\Mail;
use System\Controller\BaseAbstractActionController;
use Theme\API\Common;
use User\Model\UserTable;
use Zend\Db\Sql\Predicate\Expression;

class Doctor extends BaseAbstractActionController
{
    //region Public Methods
    public function doctorsAction()
    {
        $grid = new DataGrid('user_table');
        $grid->route = 'admin/health-center/doctors';

        $id = new Column('id', 'Id', array('headerAttr' => array('width' => '15px', 'align' => 'center')));
        $grid->setIdCell($id);

        $username = new Button('User', function (Button $col) {
            $col->route = 'app/user/user-profile';
            $col->routeParams = array('id' => $col->dataRow->id);
            $col->text = getUserDisplayName($col->dataRow);
//            $col->icon = 'glyphicon glyphicon-eye-open';
        }, array(
//            'headerAttr' => array('width' => '34px'),
//            'attr' => array('align' => 'center'),
            'contentAttr' => array('target' => '_blank')
        ));
        $email = new Column('email', 'Email');
//        $displayName = new Column('displayName', 'Display Name');

        $roleName = new Custom('roleName', 'Roles',
            function (Column $data) {
                $roles = explode(',', $data->dataRow->roleName);
                for ($i = 0; $i < count($roles); $i++) {
                    $roles[$i] = t($roles[$i]);
                }
                return implode(',', $roles);
            }
        );

        $reservations = new Button('', function (Button $col) {
            $col->route = 'admin/health-center/doctors/reservations';
            $col->routeParams = array('doctorId' => $col->dataRow->id);
//            $col->icon = 'glyphicon glyphicon-eye-open';
            $col->text = t('Reservations');
        }, array(
//            'headerAttr' => array('width' => '34px'),
//            'attr' => array('align' => 'center'),
//            'contentAttr' => array('class' => array('btn', 'btn-default'), 'target' => '_blank')
            'contentAttr' => array('class' => array('ajax_page_load'))
        ));

        $patients = new Button('', function (Button $col) {
            $col->route = 'admin/health-center/doctors/patients';
            $col->routeParams = array('id' => $col->dataRow->id);
//            $col->icon = 'glyphicon glyphicon-eye-open';
            $col->text = t('Patients');
        }, array(
//            'headerAttr' => array('width' => '34px'),
//            'attr' => array('align' => 'center'),
//            'contentAttr' => array('class' => array('btn', 'btn-default'), 'target' => '_blank')
            'contentAttr' => array('class' => array('ajax_page_load'))
        ));

        $timetable = new Button('Time Table', function (Button $col) {
            $col->route = 'admin/health-center/doctors/timetable';
            $col->routeParams = array('doctor' => $col->dataRow->id);
            $col->icon = 'glyphicon glyphicon-time text-primary';
        }, array(
            'headerAttr' => array('width' => '34px'),
            'attr' => array('align' => 'center'),
            'contentAttr' => array('class' => array('btn', 'btn-default', 'ajax_page_load'))
        ));

        $profile = new Button('Edit Profile', function (Button $col) {
            $col->route = 'admin/health-center/doctors/edit-profile';
            $col->routeParams = array('id' => $col->dataRow->id);
            $col->icon = 'glyphicon glyphicon-user';
        }, array(
            'headerAttr' => array('width' => '34px'),
            'attr' => array('align' => 'center'),
            'contentAttr' => array('class' => array('btn btn-default ajax_page_load'),)
        ));

        $grid->addColumns(array($id, $username, $email, $roleName, $reservations, $patients, $profile, $timetable));
        $grid->setSelectFilters(array());

        $doctorUserRoles = array();
        $config = getConfig('health-center');
        if (isset($config->varValue['doctorUserRole'])) {
            $doctorUserRoles = $config->varValue['doctorUserRole'];
        }

        $select = $grid->getSelect();
        $select
            ->join(array('ur' => 'tbl_users_roles'), 'tbl_users.id=ur.userId', array('roleId'), 'left')
            ->join(array('r' => 'tbl_roles'), 'ur.roleId=r.id', array('roleName' => new Expression('GROUP_CONCAT(r.roleName)')), 'left')
            ->group('tbl_users.username')
            ->where(array('ur.roleId' => $doctorUserRoles));

        $grid->defaultSort = $id;
        $grid->defaultSortDirection = 'ASC';

        $this->viewModel->setTemplate('health-center/doctor/index');
        $this->viewModel->setVariables(array(
            'grid' => $grid->render(),
            'buttons' => $grid->getButtons(),
        ));
        return $this->viewModel;
    }

    public function editProfileAction()
    {
        $id = $this->params()->fromRoute('id', false);
        if (!$id) {
            $this->flashMessenger()->addErrorMessage('Invalid Request !');
            return $this->doctorsAction();
        }

        $data['specializations'] = getSM('entity_relation_table')->getItemsIdArray($id, 'doctor_specializations');
        $data = array_merge($data, (array)$this->getDoctorProfileTable()->get($id));

        $form = new \HealthCenter\Form\Doctor();
        $form->setAction(url('admin/health-center/doctors/edit-profile', array('id' => $id)));
        $form = prepareConfigForm($form, array('submit-new'));
        $form->setData($data);

        $user = getSM('user_table')->get($id);

        if ($this->request->isPost()) {
            $post = $this->request->getPost()->toArray();
            if ($this->isSubmit()) {
                $form->setData($post);
                if ($form->isValid()) {
                    $data = $form->getData();
                    unset($data['buttons']);
                    getSM('entity_relation_table')->removeByEntityId($id, 'doctor_specializations');

                    if (isset($data['specializations'])) {
                        getSM('entity_relation_table')->saveAll($id, 'doctor_specializations', $data['specializations']);
                        unset($data['specializations']);
                    }
                    $data['doctorId'] = $id;
                    $this->getDoctorProfileTable()->save($data);
                    $this->flashMessenger()->addSuccessMessage('Doctor/Counselor profile saved successfully');
                } else
                    $this->formHasErrors();

            } elseif ($this->isCancel()) {
                return $this->doctorsAction();
            }
        }

        $this->viewModel->setTemplate('health-center/doctor/edit-profile');
        $this->viewModel->setVariables(array('form' => $form, 'user' => $user));
        return $this->viewModel;
    }

    public function panelAction()
    {
        $id = current_user()->id;
        $today = strtotime('today');

        $todayReserves = $this->getReservationTable()->getReserves($today);

        $this->viewModel->setTemplate('health-center/doctor/panel');
        $this->viewModel->setVariables(array(
            'data' => $this->render($this->forward()->dispatch('HealthCenter\Controller\HealthCenter',
                    array('action' => 'time-line', 'id' => $id, 'day' => $today))),
            'todayReserves' => $todayReserves
        ));
        return $this->viewModel;
    }

    public function visitAction()
    {
        $patient = $this->params()->fromRoute('patient', 0);
        $resId = $this->params()->fromRoute('resId', 0);

        $this->getReservationTable()->update(array('status' => 5), array('id' => $resId));
        $this->getDoctorRefTable()->setDoctor(current_user()->id, $patient);
        return $this->forward()->dispatch('HealthCenter\Controller\Patient', array('action' => 'profile', 'patient' => $patient));
    }

    public function patientsAction()
    {
        $isSelf = false;
        $doctorId = $this->params()->fromRoute('id', false);
        if (!$doctorId) {
            $doctorId = current_user()->id;
            $isSelf = true;
        }

        $columns = array();

        $grid = new DataGrid('hc_doctor_ref_table');
        $grid->attributes['class'][] = 'table-nonfluid';
        $grid->route = 'admin/health-center/doctors/patients';
        $grid->routeParams['id'] = $doctorId;

//        $id = new Column('id', 'Id', array('headerAttr' => array('width' => '15px', 'align' => 'center')));
//        $grid->setIdCell($id);
//        $columns[] = $id;

        $user = new Custom('userId', 'Patient', function (Custom $col) {
            return Common::Link(
                getUserDisplayName($col->dataRow),
                url('admin/health-center/doctor-panel/patient', array('patient' => $col->dataRow->patientId)),
                array('class' => 'ajax_page_load')
            );
        });
        $columns[] = $user;

//        $count = new Column('count', 'Visit Count');
//        $columns[] = $count;

        $refer = new Custom('refId', '', function (Custom $col) {
            if ($col->dataRow->refId) {
                $doctorParams = array();
                foreach ($col->dataRow as $name => $value) {
                    if ($name[0] == 'd' && $name[1] == '_') {
                        $doctorParams[str_replace('d_', '', $name)] = $value;
                    }
                }
                return t('Referred to you by') . ' : ' . Common::Link(
                        getUserDisplayName($doctorParams),
                        url('app/user/user-profile', array('id' => $col->dataRow->doctorId)),
                        array('target' => '_blank')
                    );
            }
        });
        $columns[] = $refer;

//        $grid->defaultSort = $start;
//        $grid->defaultSortDirection = 'DESC';

        $select = $grid->getSelect();
        $expr = new Expression('r.userId=tbl_hc_doctor_ref.patientId AND r.status in (1,5)');
        $select
            ->columns(array('patientId', 'refId', 'doctorId'))
//            ->join(array('r' => 'tbl_hc_doctor_reservation'), $expr, array('id', 'userId', 'timeId', 'status', 'count' => new Expression('COUNT(r.userId)')), 'LEFT')
            ->join(array('u' => 'tbl_users'), 'tbl_hc_doctor_ref.patientId=u.id', array('username', 'displayName', 'email'), 'LEFT')
            ->join(array('up' => 'tbl_user_profile'), 'up.userId=u.id', array('firstName', 'lastName', 'mobile'), 'LEFT')
            ->join(array('u2' => 'tbl_users'), $grid->getTableGateway()->table . '.doctorId=u2.id', array('d_username' => 'username', 'd_displayName' => 'displayName', 'd_email' => 'email'), 'LEFT')
            ->join(array('up2' => 'tbl_user_profile'), 'up2.userId=u2.id', array('d_firstName' => 'firstName', 'd_lastName' => 'lastName', 'd_mobile' => 'mobile'), 'LEFT')
            ->where(array(
                'tbl_hc_doctor_ref.doctorId' => $doctorId,
            ))//            ->group('r.userId')
        ;


        $grid->addColumns($columns);

        $this->viewModel->setTemplate('health-center/doctor/patients');
        $this->viewModel->setVariables(array(
            'grid' => $grid->render(),
            'buttons' => $grid->getButtons(),
            'doctor' => getSM('user_table')->getUser($doctorId,
                    array('table' => array('username', 'displayName'), 'profile' => array('firstName', 'lastName')))
        ));
        return $this->viewModel;
    }

    public function referAction()
    {
        $patient = $this->params()->fromRoute('patient', false);
        if (!$patient) {
            $this->invalidRequest();
            return $this->forward()->dispatch('HealthCenter\Controller\Reservations',
                array('action' => 'index', 'visitor' => 'doctor'));
        }

        $isMyDoctor = $this->getDoctorRefTable()->isMyDoctor(current_user()->id, $patient);
        if (!$isMyDoctor) {
            $this->flashMessenger()->addErrorMessage('you are not this patients doctor, so you cannot refer him/her to another doctor');
            return $this->forward()->dispatch('HealthCenter\Controller\Reservations',
                array('action' => 'index', 'visitor' => 'doctor'));
        }

        $doctor = $this->params()->fromRoute('doctor', false);
        if (!$doctor)
            return $this->forward()->dispatch('HealthCenter\Controller\HealthCenter',
                array('action' => 'doctors', 'patient' => $patient, 'type' => 'select-for-refer'));
        else {
            $this->getDoctorRefTable()->setDoctor($doctor, $patient, current_user()->id);
            $this->flashMessenger()->addSuccessMessage('patient successfully referred to the new doctor');

            if ($notifyApi = getNotifyApi()) {

                $doctor = getSM('user_table')->getUser(current_user()->id, UserTable::$profileFields);
                $patient = getSM('user_table')->getUser($patient, UserTable::$profileFields);

                $doctorTitle = getUserDisplayName($doctor);

                if (isset($doctor['email']) && has_value($doctor['email'])) {
                    $email = $notifyApi->getEmail();
                    $email->to = array($doctor['email'] => $doctorTitle);
                    $email->from = Mail::getFrom();
                    $email->subject = t('Patient Referral');
                    $email->queued = 0;
                }

                $notifyApi->getInternal()->uId = $doctor['id'];

                $doctorTitle = "<stong>{$doctorTitle}</stong>";

                $userTitle = getUserDisplayName($patient);
                $userTitle = "<stong>{$userTitle}</stong>";

                $notifyApi->notify('HealthCenter', 'refer',
                    array(
                        '__DOCTOR__' => Common::Link($doctorTitle,
                                App::siteUrl() . url('app/user/user-profile',
                                    array('id' => $doctor['id']),
                                    array('target' => '_blank'))),
                        '__USER__' => Common::Link($userTitle,
                                App::siteUrl() . url('admin/health-center/doctor-panel/patient',
                                    array('patient' => $patient['id']),
                                    array('class' => 'ajax_page_load'))),
                    )
                );
                //endregion

            }
            return $this->patientsAction();
        }
    }
    //endregion

    //region Private Methods
    /**
     * @return DoctorProfileTable
     */
    private function getDoctorProfileTable()
    {
        return getSM('hc_doctor_profile_table');
    }

    /**
     * @return DoctorReservationTable
     */
    private function getReservationTable()
    {
        return getSM('hc_doctor_reservation');
    }

    /**
     * @return DoctorRefTable
     */
    private function getDoctorRefTable()
    {
        return getSM('hc_doctor_ref_table');
    }

    /**
     * @return DoctorTable
     */
    private function getDoctorTable()
    {
        return getSM('hc_doctor_table');
    }
    //endregion
} 