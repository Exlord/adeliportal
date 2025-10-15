<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 8/23/14
 * Time: 9:45 AM
 */

namespace HealthCenter\Controller;


use Application\API\App;
use Category\Model\EntityRelationTable;
use HealthCenter\Form\Config;
use HealthCenter\Form\PatientProfile;
use HealthCenter\Form\TimeSearch;
use HealthCenter\Model\DoctorProfileTable;
use HealthCenter\Model\DoctorReservationTable;
use HealthCenter\Model\DoctorTable;
use HealthCenter\Model\DoctorTimeTable;
use Localization\API\Date;
use Mail\API\Mail;
use System\Controller\BaseAbstractActionController;
use Theme\API\Common;
use Zend\EventManager\EventManager;
use Zend\EventManager\StaticEventManager;
use Zend\View\Model\ViewModel;

class HealthCenter extends BaseAbstractActionController
{
    //region Public Methods

    /**
     * Admin index page
     * @return array|ViewModel
     */
    public function indexAction()
    {
        return $this->adminMenuPage();
    }

    /**
     * Admin config page
     * @return ViewModel
     */
    public function configAction()
    {
        $config = getConfig('health-center');
        $form = prepareConfigForm(new Config());
        $form->setData($config->varValue);

        if ($this->request->isPost()) {
            if ($this->isSubmit()) {
                $form->setData($this->request->getPost());
                if ($form->isValid()) {
                    $config->setVarValue($form->getData());
                    $this->getConfigTable()->save($config);
                    db_log_info("Health center Configs changed");
                    $this->flashMessenger()->addSuccessMessage('Health center configs saved successfully');
                }
            }
        }

        $this->viewModel->setVariables(array('form' => $form));
        $this->viewModel->setTemplate('health-center/config');
        return $this->viewModel;
    }

    /**
     * client first page - select to search by doctor or category
     * @return ViewModel
     */
    public function healthCenterAction()
    {
        $form = new TimeSearch();

        $dateFrom = strtotime('today');
        $dateTo = strtotime('+1 week');
        $doctor = null;
        $spec = null;
        $start = null;
        $end = null;

        if ($this->request->isPost() && $this->isSubmit()) {
            $post = $this->request->getPost()->toArray();
            $form->setData($post);
            if ($form->isValid()) {
                $formData = $form->getData();

                if (isset($formData['doctor']))
                    $doctor = $formData['doctor'];
                if (isset($formData['spec']))
                    $spec = $formData['spec'];
                if (isset($formData['dateFrom'])) {
                    if ($dateFromTemp = $this->_validateDate($formData['dateFrom']))
                        $dateFrom = Date::fromDatePicker($dateFromTemp);
                }
                if (isset($formData['dateTo'])) {
                    if ($dateToTemp = $this->_validateDate($formData['dateTo']))
                        $dateTo = Date::fromDatePicker($dateToTemp);
                }

                if (isset($formData['start']))
                    $start = (int)$formData['start'];

                if (isset($formData['end']))
                    $end = (int)$formData['end'];
            } else {
                $this->formHasErrors();
            }
        }
        $data = $this->getTimeTable()->search($dateFrom, $dateTo, $doctor, $spec);

        $this->viewModel->setVariables(array(
            'data' => $data,
            'form' => $form,
            'start' => $start,
            'end' => $end
        ));
        $this->viewModel->setTemplate('health-center/reservation/list');
        return $this->viewModel;
    }

    public function searchByAction()
    {
        $this->viewModel->setTemplate('health-center/reservation/search-type');
        return $this->viewModel;
    }

    /**
     * Client side - a list of doctors
     * @return ViewModel
     */
    public function doctorsAction()
    {
        $doctorUserRoles = array();
        $config = getConfig('health-center');
        if (isset($config->varValue['doctorUserRole'])) {
            $doctorUserRoles = $config->varValue['doctorUserRole'];
        }

        $spec = $this->params()->fromRoute('spec', false);

        $items = $this->getDoctorTable()->getDoctors($doctorUserRoles, $spec);

        if ($spec) {
            $spec = $this->getCategoryItemTable()->get($spec);
        }

        $this->viewModel->setTemplate('health-center/doctors');
        $this->viewModel->setVariables(array(
            'items' => $items,
            'spec' => $spec,
            'type' => $this->params()->fromRoute('type', false),
            'patient' => $this->params()->fromRoute('patient', false),
        ));
        return $this->viewModel;
    }

    /**
     * Client side - a list of specialization categories
     * @return ViewModel
     */
    public function specializationAction()
    {
        $doctorUserRoles = array();
        $config = getConfig('health-center');
        if (isset($config->varValue['doctorUserRole'])) {
            $doctorUserRoles = $config->varValue['doctorUserRole'];
        }

        $items = $this->getDoctorTable()->getSpecializations($doctorUserRoles);

        $this->viewModel->setTemplate('health-center/reservation/specialization');
        $this->viewModel->setVariables(array(
            'items' => $items
        ));
        return $this->viewModel;
    }

    /**
     * Client side - doctor profile to select a time for reservation
     * @return ViewModel
     */
    public function doctorAction()
    {
        $transfer = $this->params()->fromQuery('transfer', false);
        $transferFrom = null;
        $session = App::getSession('health_center');
        if ($transfer) {
            $session->setExpirationSeconds(300);
            $session->offsetSet('transfer', $transfer);
            $transferFrom = $this->getReservationTable()->getReserver($transfer);
        } else
            $session->offsetUnset('transfer');

        $id = $this->params()->fromRoute('id', false);
        if (!$id)
            return $this->doctorsAction();

        $day = $this->params()->fromRoute('day', false);
        if (!$day) {
            $result = $this->getTimeTable()->findFirstUnreserved($id);
            if ($result) {
                $result = $result->current();
                if ($result)
                    $day = $result->date;
                else
                    $day = $timestamp = strtotime('today');
            }
        }

        $data = $this->render($this->forward()->dispatch('HealthCenter\Controller\HealthCenter',
            array('action' => 'time-line', 'day' => $day, 'id' => $id)));

        $this->viewModel->setTemplate('health-center/reservation/doctor');
        $this->viewModel->setVariables(array(
            'id' => $id,
            'data' => $data,
            'day' => $day,
            'specs' => $this->getEntityRelationTable()->getItems($id, 'doctor_specializations'),
            'transferFrom' => $transferFrom
        ));
        return $this->viewModel;
    }

    /**
     * TimeLine for doctors panel
     * @return ViewModel
     */
    public function timeLineAction()
    {
        $id = $this->params()->fromRoute('id', false);
        $day = $this->params()->fromRoute('day', false);

        $data = $this->getTimeTable()->getDates($id, $day);

        $view = new ViewModel();
        $view->setTemplate('health-center/doctor-time-line');
        $view->setTerminal(true);
        $view->setVariables(array(
            'doctor' => $id,
            'data' => $data,
            'day' => $day,
        ));

        return $view;
    }

    /**
     * User Agreement and reservation rules
     * @return ViewModel
     */
    public function agreementAction()
    {
        $doctor = $this->params()->fromRoute('doctor', 0);
        $time = $this->params()->fromRoute('time', 0);

        $page = 0;
        $config = getConfig('health-center')->varValue;
        if (isset($config['visitRules']))
            $page = $config['visitRules'];

        if ($page)
            $page = getSM('page_table')->get($page);

        $this->viewModel->setVariables(array(
            'page' => $page,
            'doctor' => $doctor,
            'time' => $time
        ));
        $this->viewModel->setTemplate('health-center/reservation/agreement');
        return $this->viewModel;
    }

    /**
     * Reserve a doctor appointment
     * @return mixed|\Zend\Http\Response|ViewModel
     */
    public function reserveAction()
    {
        //doctorId and timeId for reservation
        $doctor = $this->params()->fromRoute('doctor', false);
        $time = $this->params()->fromRoute('time', false);

        //doctor is not set ! it could be 0
        if ($doctor === false)
            return $this->invalidRequest('app/health-center');

        //time is not set !
        if (!$time)
            return $this->invalidRequest('app/health-center');

        //user is not logged in redirect to login with message and stuff
        if (!current_user()->id) {
            $this->flashMessenger()->addInfoMessage('To reserve time ,you need to be a member in the site.');
//            $this->flashMessenger()->addInfoMessage('Login or register to reserve a time.');
            return $this->redirect()->toRoute('app/user/login', array(), array('query' => array('redirect' => urlencode($this->request->getRequestUri()))));
        }

        //is this time available
        $now = time();
        //check if this timeId exist , if id is not passed to this url manually , we won't have a problem
        $timeTemp = explode(',', $time);
        $time = array();
        foreach ($timeTemp as $t) {
            $t = trim($t);
            if (has_value($t))
                $time[] = $t;
        }

        $timeDatas = $this->getTimeTable()->get($time);
        if (!$timeDatas) {
            //this should not happen
            $this->somethingIsNotRight();
            return $this->forward()->dispatch('HealthCenter\Controller\HealthCenter', array('action' => 'health-center'));
        } else {
            $__timeDatas = array();
            foreach ($timeDatas as $row) {
                $__timeDatas[] = $row;
            }

            $timeDatas = $__timeDatas;
            $__timeDatas = null;
        }

        //check if this time is already exist in the reservation table
        $unavailables = array();
        $availableStatus = array('0', '1', '3', '5');
        foreach ($timeDatas as $timeData) {
            //normal time should have status=0
            if ($timeData->status != '0') {
                //time is canceled
                $unavailables[$timeData->id] = sprintf(t('The time with id %s has been canceled'), $timeData->id);
            } elseif (in_array($timeData->resStatus, $availableStatus)) {
                //time is temp reserved(0) or full reserved(1) or is requesting to cancel(3) or is visited(5)
                $unavailables[$timeData->id] = sprintf(t('The time with id %s has been reserved'), $timeData->id);
            } elseif ($timeData->start < $now) {
                // this time is already started so ...
                //this time is in progress
                if ($timeData->end > $now)
                    $unavailables[$timeData->id] = sprintf(t('The time with id %s has already started'), $timeData->id);
                else
                    $unavailables[$timeData->id] = sprintf(t('The time with id %s has finished'), $timeData->id);
            }
        }


        ///this is is not available for reservation, a appropriate message has already been set to flash messenger
        if ($count = count($unavailables)) {
            $countAll = count($timeDatas);
            if ($countAll == 1 || $countAll == $count) {
                foreach ($unavailables as $key => $value)
                    $this->flashMessenger()->addErrorMessage($value);

                return $this->redirect()->toRoute('app/health-center');
            } else {
                $this->viewModel->setVariables(array(
                    'doctor' => $doctor,
                    'time' => $time,
                    'unavailables' => $unavailables
                ));
                $this->viewModel->setTemplate('health-center/reservation/unavailables');
                return $this->viewModel;
            }
        }

        //patient profile
        if ($this->params()->fromRoute('skip-records') != true && $this->params()->fromQuery('skip-records') != 1) {
            return $this->forward()->dispatch('HealthCenter\Controller\Patient',
                array('action' => 'edit-profile', 'doctor' => $doctor, 'time' => implode(',', $time)));
        }

        //ok , we are good, everything checks out
        //is there a transfer request present ?
        $session = App::getSession('health_center');
        $transfer = $session->offsetGet('transfer');
        $transferFrom = null;
        $transferable = true;
        if ($transfer) {
            $transferFrom = $this->getReservationTable()->getReserver($transfer);
            //this is not a valid reservation
            if ($transferFrom['status'] != '1') {
                $transferable = false;
                $this->flashMessenger()->addErrorMessage('The selected reservation for transform is not a valid reservation');
            } //this reservation is already started and cannot be transferred
            elseif ($transferFrom['start'] < time()) {
                $transferable = false;
                $this->flashMessenger()->addErrorMessage('The selected reservation is already started and cannot be transferred');
            } //users can only transfer their own reservations
            elseif ($transferFrom['userId'] != current_user()->id) {
                $transferable = false;
                $this->flashMessenger()->addErrorMessage('Users can only transfer their own reservations');
            }
        }

        $reserveId = null;
        if ($transferFrom) {
            if ($transferable) {
                $this->getReservationTable()->update(array('timeId' => $time), array('id' => $transfer));
            } else {
                //the reservation selected for transfer is not transferable
                return $this->redirect()->toRoute('app/health-center/doctor', array('id' => $doctor));
            }
        } else {
            //save the temporary reservation request
            $reserveId = array();
            $doctors = array();
            foreach ($timeDatas as $t) {
                $doctors[$t->id] = $t->doctorId;
                $reserveId[] = $this->getReservationTable()->save(array(
                    'timeId' => $t->id,
                    'doctorId' => $t->doctorId,
                    'userId' => current_user()->id,
                    'date' => $now,
                    'status' => 0
                ));
            }
        }


        //temp reservation has been successful, its a normal reservation
        if ($reserveId && count($reserveId)) {

            $config = getConfig('health-center')->varValue;

            //reservationPaymentTimeout
            if (isset($config['reservationPaymentTimeout']) && !empty($config['reservationPaymentTimeout']))
                $timeForPayment = (int)$config['reservationPaymentTimeout'];
            else
                $timeForPayment = 60;//1 hour

            $this->flashMessenger()->addSuccessMessage(sprintf(t('Your reservation request with id %s is submitted successfully.'), implode(',', $reserveId)));
//            $this->flashMessenger()->addWarningMessage(sprintf(t('This reservation is temporary and if payment is not completed in less than %s hours it will be canceled'), $timeForPayment));

            //notify the user about successful temp reservation
            if ($notifyApi = getNotifyApi()) {

                $doctorUsers = array();
                foreach ($doctors as $dId) {
                    if (!isset($doctorUsers[$dId])) {
                        $doctorUsers[$dId] = getSM('user_table')->getUser($dId, array(
                            'table' => array('username', 'displayName'),
                            'profile' => array('firstName', 'lastName')
                        ));
                    }
                }

                if (isset($user['email']) && has_value($user['email'])) {
                    $email = $notifyApi->getEmail();
                    $email->to = array($user['email'] => getUserDisplayName($user));
                    $email->from = Mail::getFrom();
                    $email->subject = t('initial reservation');
                    $email->entityType = 'HealthCenterReservation';
                    $email->queued = 0;
                }

                $doctorUrls = array();
                foreach ($doctorUsers as $dId => $doctor) {
                    $doctorTitle = getUserDisplayName($doctor);
                    $doctorTitle = "<strong>{$doctorTitle}</strong>";
                    $doctorUrls[] = Common::Link($doctorTitle, App::siteUrl() . url('app/user/user-profile', array('id' => $dId,)));
                }
                $doctorUrls = implode(',', $doctorUrls);

                $notifyApi->notify('HealthCenter', 'reservation_initialized', array(
                    '__RESERVE_REQUEST_ID__' => implode(',', $reserveId),
                    '__DOCTOR_URL__' => $doctorUrls,
                    '__RESERVE_TIME_FOR_PAYMENT__' => $timeForPayment,
                ));
            }

            //global session cost, from global config
            $cost = $config['sessionCost'];

            $doctorCosts = array();
            foreach ($doctors as $dId) {
                if (!isset($doctorCosts[$dId])) {
                    //get doctor specific profile
                    $doctorProfile = $this->getDoctorProfileTable()->get($dId);
                    //per doctor session cost, is this doctor has its own price ?
                    if (isset($doctorProfile['sessionCost']) && $doctorProfile['sessionCost'])
                        $__cost = $doctorProfile['sessionCost'];
                    else
                        $__cost = $cost;
                    $doctorCosts[$dId] = $__cost;
                }
            }

            //there is more than 1 time for reserve , so multiply the cost by the time count
            $finalCost = 0;
            foreach ($doctors as $dId) {
                $finalCost += $doctorCosts[$dId];
            }

            //lets pay the piper, heeeeeeeeeeeeeeha
            $paymentParams = array(
                'amount' => $finalCost,
                'comment' => 'doctor time reservation',
                'validate' => array(
                    'route' => 'app/health-center/finalize-payment',
                    'params' => array(
                        'reservation' => implode(',', $reserveId),
                    ),
                )
            );
            $paymentParams = serialize($paymentParams);
            $paymentParams = base64_encode($paymentParams);
            return $this->redirect()->toRoute('app/payment', array(),
                array('query' => array('routeParams' => $paymentParams)));
        } elseif ($transferFrom) {
            //this is a transfer
            $this->flashMessenger()->addSuccessMessage('Your reservation successfully transferred');
            //notify the user about successful temp reservation
            if ($notifyApi = getNotifyApi()) {

                $doctorUser = getSM('user_table')->getUser($doctor, array(
                    'table' => array('username', 'displayName', 'email'),
                    'profile' => array('firstName', 'lastName', 'mobile')
                ));

                //region NOTIFY USER
                $notifyApi->getInternal()->uId = current_user()->id;

                if (isset($user['email']) && has_value($user['email'])) {
                    $email = $notifyApi->getEmail();
                    $email->to = array($user['email'] => getUserDisplayName($user));
                    $email->from = Mail::getFrom();
                    $email->subject = t('reservation transferred');
                    $email->entityType = 'HealthCenterReservation';
                    $email->queued = 0;
                }

                $doctorTitle = getUserDisplayName($doctorUser);
                $doctorTitle = "<strong>{$doctorTitle}</strong>";
                $notifyApi->notify('HealthCenter', 'reservation_transferred', array(
                    '__DOCTOR_NAME__' => $doctorTitle,
                    '__DOCTOR_URL__' => Common::Link($doctorTitle, App::siteUrl() . url('app/user/user-profile', array('id' => $doctor,))),
                    '__DOCTOR_TRANSFER_TIME__' => dateFormat($transferFrom['start'], 0, 3),
                    '__DOCTOR_RESERVE_TIME__' => dateFormat($timeData->start, 0, 3)
                ));
                //endregion

                //region NOTIFY DOCTOR
                $notifyApi->getInternal()->uId = $doctor;

                if (isset($doctorUser['email']) && has_value($doctorUser['email'])) {
                    $email = $notifyApi->getEmail();
                    $email->to = array($doctorUser['email'] => getUserDisplayName($doctorUser));
                    $email->from = Mail::getFrom();
                    $email->subject = t('reservation transferred');
                    $email->entityType = 'HealthCenterReservation';
                    $email->queued = 0;
                }

                $userTitle = getUserDisplayName($user);
                $userTitle = "<strong>{$userTitle}</strong>";
                $notifyApi->notify('HealthCenter', 'reservation_transferred_to_doctor', array(
                    '__USER_NAME__' => $userTitle,
                    '__USER_URL__' => Common::Link($userTitle, App::siteUrl() . url('app/user/user-profile', array('id' => $user['id'],))),
                    '__DOCTOR_TRANSFER_TIME__' => dateFormat($transferFrom['start'], 0, 3),
                    '__DOCTOR_RESERVE_TIME__' => dateFormat($timeData->start, 0, 3)
                ));
                //endregion

                return $this->redirect()->toRoute('app/health-center/doctor', array('id' => $doctor));
            }
        } else {
            //wooooooooooooops something went wrong ???? there is no insert id
            $this->somethingWentWrong();

            return $this->redirect()->toRoute('app/health-center');
        }

        die('We should never get this far, it is dangerous ...');
    }

    /**
     * finalized payment and reservation - return from bank
     * @return ViewModel
     */
    public function finalizePaymentAction()
    {
        $params = $this->params()->fromRoute('params', false);
        $paymentId = $this->params()->fromRoute('paymentId', false);

        //the received parameters from payment module is not correct
        if (!$params || !$paymentId) {
            $this->invalidRequest('app/health-center');
        }

        $params = unserialize(base64_decode($params));

        if (!isset($params['reservation']))
            $this->invalidRequest('app/health-center');

        $reserveId = explode(',', $params['reservation']);
        $reserver = (array)$this->getReservationTable()->getReserver($reserveId, true)->toArray();

        //update server status to finalized(1) and set payment id
        $this->getReservationTable()->update(
            array('paymentId' => $paymentId, 'paymentStatus' => 1, 'status' => 1),
            array('id' => $reserveId));

        $payment_message = App::getSession()->offsetGet('payment_message');
        App::getSession()->offsetUnset('payment_message');
        if ($payment_message) {
            foreach ($payment_message as $msg) {
                $this->flashMessenger()->addSuccessMessage($msg);
            }
        }

        $this->flashMessenger()->addSuccessMessage(sprintf(t('You have successfully finalized your reservation')));

        $doctors = array();
        $doctorDisplayNames = array();
        $reservedTimes = array();
        foreach ($reserver as $row) {
            $doctorParams = array();
            foreach ($row as $name => $value) {
                if ($name[0] == 'd' && $name[1] == '_') {
                    $doctorParams[str_replace('d_', '', $name)] = $value;
                }
            }

            $doctorDisplayName = getUserDisplayName($doctorParams);
            $doctorDisplayNames[] = $doctorDisplayName;
            $doctorUrl = Common::Link(
                $doctorDisplayName,
                App::siteUrl() . url('app/user/user-profile', array('id' => $row['doctorId']))
            );

            $doctors[] = $doctorUrl;

            $reservedTimes[] = dateFormat($row['start'], 0, 3);
        }

        $doctors = implode(',', $doctors);
        $doctorDisplayNames = implode(',', $doctorDisplayNames);
        $reservedTimes = implode(',', $reservedTimes);


        $reserver = array_shift($reserver);

        //notify user about successful reservation
        if ($notifyApi = getNotifyApi()) {

            //region Notify Attendance
            if (isset($reserver['email']) && has_value($reserver['email'])) {
                $email = $notifyApi->getEmail();
                $email->to = array($reserver['email'] => getUserDisplayName($reserver));
                $email->from = Mail::getFrom();
                $email->subject = t('Finalized Reservation');
                $email->entityType = 'HealthCenterReservation';
                $email->queued = 0;
            }

            if (isset($reserver['mobile']) && has_value($reserver['mobile'])) {
                $sms = $notifyApi->getSms();
                $sms->to = $reserver['mobile'];
            }

            $notifyApi->notify('HealthCenter', 'reservation_finalized', array(
                '__DOCTOR_NAME__' => $doctorDisplayNames,
                '__DOCTOR_RESERVE_TIME__' => $reservedTimes,
                '__DOCTOR_URL__' => $doctors,
            ));
            //endregion
        }

        //add point to user
        if (getSM()->has('points_api'))
            getSM('points_api')->addPoint('HealthCenter', 'Reserve.Done', $reserver['userId'],
                t('appointment reservation'), count($reserveId));
        //update activity log
        if (getSM()->has('customer_records_table'))
            getSM('customer_records_table')->add($reserver['userId'],
                sprintf(t('reserved a appointment with %s'), $doctors));


        return $this->healthCenterAction();
    }
    //endregion

    //region Private Methods
    /**
     * @return DoctorTable
     */
    private function getDoctorTable()
    {
        return getSM('hc_doctor_table');
    }

    /**
     * @return DoctorTimeTable
     */
    private function getTimeTable()
    {
        return getSM('hc_doctor_time_table');
    }

    /**
     * @return EntityRelationTable
     */
    private function getEntityRelationTable()
    {
        return getSM('entity_relation_table');
    }

    /**
     * @return DoctorReservationTable
     */
    private function getReservationTable()
    {
        return getSM('hc_doctor_reservation');
    }

    /**
     * @return DoctorProfileTable
     */
    private function getDoctorProfileTable()
    {
        return getSM('hc_doctor_profile_table');
    }

    private function _validateDate($date)
    {
        $date = explode('/', $date);
        if (count($date) == 3)
            return $date;
        else
            return false;
    }
    //endregion
} 