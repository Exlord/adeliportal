<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 9/9/14
 * Time: 3:28 PM
 */

namespace HealthCenter\Controller;


use Application\API\App;
use DataView\Lib\Column;
use DataView\Lib\Custom;
use DataView\Lib\DataGrid;
use DataView\Lib\Date;
use DataView\Lib\DeleteButton;
use HealthCenter\Form\ReserveCancel;
use HealthCenter\Model\DoctorProfileTable;
use HealthCenter\Model\DoctorReservationTable;
use HealthCenter\Module;
use Mail\API\Mail;
use System\Controller\BaseAbstractActionController;
use Theme\API\Common;
use Zend\View\Model\JsonModel;

class Reservations extends BaseAbstractActionController
{
    private $visitor = null;

    //region Public Methods
    public function indexAction()
    {
        $this->visitor = $this->params()->fromRoute('visitor', $this->visitor);
        $currentUserId = current_user()->id;
        $doctorId = $this->params()->fromRoute('doctorId', false);
        $config = getConfig('health-center')->varValue;

        $cancelTimeout = 0;
        if (isset($config['cancelTimeout'])) {
            $cancelTimeout = (int)$config['cancelTimeout'];
            if ($cancelTimeout)
                $cancelTimeout = strtotime('+' . $cancelTimeout . ' hours');
        }

        $columns = array();

        $grid = new DataGrid('hc_doctor_reservation');
        $grid->attributes['class'][] = 'table-nonfluid';
        if ($this->visitor == 'patient')
            $grid->route = 'admin/health-center/patient-panel/my-reservations';
        elseif ($this->visitor == 'doctor')
            $grid->route = 'admin/health-center/doctor-panel/my-reservations';
        elseif ($doctorId) {
            $grid->route = 'admin/health-center/doctors/reservations';
            $grid->routeParams['doctorId'] = $doctorId;
        } else
            $grid->route = 'admin/health-center/reservations';

        $id = new Column('id', 'Id', array('headerAttr' => array('width' => '50px', 'align' => 'center')));
        $grid->setIdCell($id);
        $columns[] = $id;

        if ($this->visitor != 'patient') {
            $user = new Custom('userId', 'Patient', function (Custom $col) {
                return Common::Link(
                    getUserDisplayName($col->dataRow),
                    url('app/user/user-profile', array('id' => $col->dataRow->userId)),
                    array('target' => '_blank')
                );
            }, array(), true);
//            $user->hasTextFilter = true;
            $columns[] = $user;
        }

        if ($this->visitor != 'doctor' || $doctorId) {
            $doctor = new Custom('doctorId', 'Doctor', function (Custom $col) {
                $params = array();
                foreach ($col->dataRow as $name => $value) {
                    if ($name[0] == 'd' && $name[1] == '_')
                        $params[str_replace('d_', '', $name)] = $value;
                }
                return Common::Link(
                    getUserDisplayName($params),
                    url('app/user/user-profile', array('id' => $col->dataRow->doctorId)),
                    array('target' => '_blank')
                );
            }, array(), true);
//            $doctor->hasTextFilter = true;
            $columns[] = $doctor;
        }

        $date = new Date('date', 'Date');
        $columns[] = $date;

        $start = new Date('start', 'Start Time', array(), -1, 3);
        $columns[] = $start;

        $end = new Date('end', 'End Time', array(), -1, 3);
        $columns[] = $end;

        $status = new Custom('status', 'Status',
            function (Custom $col) use ($currentUserId, $cancelTimeout) {
                $dataRow = $col->dataRow;
                $content = '';
                $now = time();
                switch ($dataRow['status']) {
                    case '0':
                        $content = "<span class='glyphicon glyphicon-question-sign text-muted grid-icon' title='" . t('Not Finalized') . "'></span>";
                        break;
                    case '1':
                        $content = "<span class='glyphicon glyphicon-info-sign text-primary grid-icon' title='" . t('Reserved') . '/' . t('Not Visited') . "'></span>";
                        if (
                            //current user is this patients doctor
                            $col->dataRow['doctorId'] == $currentUserId &&
                            //current user is not the patient
                            $col->dataRow['userId'] != $currentUserId &&
                            //current user is allowed to visit patients
                            isAllowed(Module::DOCTOR_VISIT_PATIENT)
                        ) {
                            $content .= '&nbsp;' . Common::Link(
                                    t('HC_DOCTOR_VISIT'),
                                    url('admin/health-center/doctor-panel/visit',
                                        array('patient' => $dataRow['userId'], 'resId' => $dataRow['id'])),
                                    array(
                                        'class' => 'ajax_page_load',
                                        'title' => t('HC_DOCTOR_VISIT'),
                                    )
                                );
                        }

                        //if session has not started yet
                        if ($col->dataRow['start'] > $now) {

                            $canCancel = true;
                            if ($cancelTimeout && $col->dataRow['start'] < $cancelTimeout)
                                $canCancel = false;

                            //if the current user is this sessions doctor
                            if ($col->dataRow['doctorId'] == $currentUserId) {
                                if ($canCancel) {
                                    $content .= '&nbsp;' . Common::Link(
                                            "<span class='glyphicon glyphicon-remove-sign text-danger'></span>",
                                            url('admin/health-center/doctor-panel/my-reservations/cancel',
                                                array('resId' => $dataRow['id'])),
                                            array(
                                                'class' => 'ajax_page_load btn btn-default btn-xs',
                                                'title' => t('Cancel'),
                                            )
                                        );
                                }
                                //if current user is this sessions patient
                            } elseif ($col->dataRow['userId'] == $currentUserId) {
                                if ($canCancel) {
                                    $content .= '&nbsp;' . Common::Link(
                                            "<span class='glyphicon glyphicon-remove-circle text-danger'></span>",
                                            url('admin/health-center/patient-panel/my-reservations/cancel-request',
                                                array('resId' => $dataRow['id'])),
                                            array(
                                                'class' => 'ajax_page_load btn btn-default btn-xs',
                                                'title' => t('Cancel Request'),
                                            )
                                        );
                                }
                                $content .= '&nbsp;' . Common::Link(
                                        "<span class='glyphicon glyphicon-calendar text-primary'></span>",
                                        url('app/health-center/doctor',
                                            array('id' => $dataRow['doctorId']), array('query' => array('transfer' => $dataRow['id']))),
                                        array(
                                            'class' => 'btn btn-default btn-xs',
                                            'title' => t('Transfer to another time'),
                                            'target' => '_blank'
                                        )
                                    );
                            } else {
                                if ($canCancel) {
                                    $content .= '&nbsp;' . Common::Link(
                                            "<span class='glyphicon glyphicon-remove-sign text-danger'></span>",
                                            url('admin/health-center/reservations/cancel',
                                                array('resId' => $dataRow['id'])),
                                            array(
                                                'class' => 'ajax_page_load btn btn-default btn-xs',
                                                'title' => t('Cancel'),
                                            )
                                        );
                                }
                            }
                        }
                        break;
                    case '2':
                        $content = "<span class='glyphicon glyphicon-exclamation-sign text-danger grid-icon' title='" . t('Failed Reservation') . "'></span>";
                        break;
                    case '3':
                        $content = "<span class='glyphicon glyphicon-remove-circle text-warning grid-icon' title='" . t('Cancel Request') . "'></span>";
                        if (
                            //allowed to cancel
                            (isAllowed(Module::RESERVATION_CANCEL) &&
                                //current user is this patients doctor
                                $col->dataRow['doctorId'] == $currentUserId) ||
                            //allowed to cancel all
                            isAllowed(Module::RESERVATION_CANCEL_ALL)
                        ) {
                            $content .= '&nbsp;' . Common::Link(
                                    "<span class='glyphicon glyphicon-remove-sign text-danger'></span>",
                                    url('admin/health-center/reservations/cancel',
                                        array('resId' => $dataRow['id'])),
                                    array(
                                        'class' => 'ajax_page_load btn btn-default btn-xs',
                                        'title' => t('Cancel'),
                                    )
                                );
                        }
                        break;
                    case '4':
                        $content = "<span class='glyphicon glyphicon-ban-circle text-warning grid-icon' title='" . t('Canceled') . "'></span>";
                        break;
                    case '5':
                        $content = "<span class='glyphicon glyphicon-ok text-success grid-icon' title='" . t('Visited') . "'></span>";
                        if (
                            //current user is this patients doctor
                            $col->dataRow['doctorId'] == $currentUserId &&
                            //current user is not the patient
                            $col->dataRow['userId'] != $currentUserId &&
                            //current user is allowed to visit patients
                            isAllowed(Module::DOCTOR_VISIT_PATIENT)
                        ) {
                            $content .= '&nbsp;' . Common::Link(
                                    t('HC_DOCTOR_VISIT'),
                                    url('admin/health-center/doctor-panel/patient',
                                        array('patient' => $dataRow['userId'])),
                                    array(
                                        'class' => 'ajax_page_load',
                                        'title' => t('HC_DOCTOR_VISIT'),
                                    )
                                );
                        }
                        break;
                }
                return $content;
            },
            array('headerAttr' => array('width' => '15px'), 'attr' => array('class' => array('nowrap')))
        );
        $columns[] = $status;

        if ($this->visitor == 'patient') {
            $rating = new Custom('doctorId', '', function (Custom $col) {
                if ($col->dataRow->status == '5') {
                    $r = getVHM('rating');
                    return $r($col->dataRow->doctorId, 'hc_doctor', $col->dataRow->id, 0);
                }
            }, array('attr' => array('class' => array('nowrap'))));
            $columns[] = $rating;
        }

        if (isAllowed(Module::RESERVATION_DELETE) && $this->visitor != 'doctor' && $this->visitor != 'patient') {
            $delete = new DeleteButton();
            $columns[] = $delete;
        }

        $grid->defaultSort = $start;
        $grid->defaultSortDirection = 'DESC';

        $select = $grid->getSelect();
        $select
            ->columns(array('id', 'userId', 'timeId', 'status', 'doctorId'))
            ->join(array('u' => 'tbl_users'), 'tbl_hc_doctor_reservation.userId=u.id', array('username', 'displayName', 'email'), 'LEFT')
            ->join(array('up' => 'tbl_user_profile'), 'up.userId=u.id', array('firstName', 'lastName', 'mobile'), 'LEFT')
            ->join(array('u2' => 'tbl_users'), 'tbl_hc_doctor_reservation.doctorId=u2.id', array('d_username' => 'username', 'd_displayName' => 'displayName', 'd_email' => 'email'), 'LEFT')
            ->join(array('up2' => 'tbl_user_profile'), 'up2.userId=u2.id', array('d_firstName' => 'firstName', 'd_lastName' => 'lastName', 'd_mobile' => 'mobile'), 'LEFT')
            ->join(array('t' => 'tbl_hc_doctor_timetable'), 'tbl_hc_doctor_reservation.timeId=t.id', array('start', 'end', 'date'))
            ->where(array());

        if ($this->visitor == 'patient')
            $select->where(array('tbl_hc_doctor_reservation.userId' => $currentUserId,));
        elseif ($this->visitor == 'doctor')
            $select->where(array('tbl_hc_doctor_reservation.doctorId' => $currentUserId,));
        elseif ($doctorId)
            $select->where(array('tbl_hc_doctor_reservation.doctorId' => $doctorId,));

        $grid->addColumns($columns);

        if ($this->visitor == 'doctor')
            $doctorId = $currentUserId;
        $doctor = null;
        if ($doctorId)
            $doctor = getSM('user_table')->getUser($doctorId, array('table' => array('username', 'displayName'), 'profile' => array('firstName', 'lastName')));

        $this->viewModel->setTemplate('health-center/reservation/index');
        $this->viewModel->setVariables(array(
            'grid' => $grid->render(),
            'buttons' => $grid->getButtons(),
            'doctor' => $doctor,
            'visitor' => $this->visitor
        ));
        return $this->viewModel;
    }

    public function cancelRequestAction()
    {
        $this->visitor = 'patient';
        //reserve id
        $resId = $this->params()->fromRoute('resId', false);
        if (!$resId) {
            $this->flashMessenger()->addErrorMessage(t('Invalid Request !'));
            $this->flashMessenger()->addErrorMessage(t('No identification found in the request !'));
            return $this->indexAction();
        }

        $reserver = $this->getDoctorReservationTable()->getReserver($resId);
        if (!$reserver) {
            $this->flashMessenger()->addErrorMessage(t('Invalid Request !'));
            return $this->indexAction();
        }

        //IS THIS USER reserved this time
        if ($reserver['userId'] != current_user()->id) {
            $this->flashMessenger()->addErrorMessage(t('Invalid Request !'));
            $this->flashMessenger()->addErrorMessage(t('Users can only cancel their own reservations.'));
            return $this->indexAction();
        }

        $config = getConfig('health-center')->varValue;

        $cancelTimeout = time();
        if (isset($config['cancelTimeout'])) {
            $cancelTimeout = (int)$config['cancelTimeout'];
            if ($cancelTimeout)
                $cancelTimeout = strtotime('+' . $cancelTimeout . ' hours');
        }

        //can be canceled / has paymentId / status=1 and has not yet started
        if ($reserver['start'] > $cancelTimeout && $reserver['paymentId'] != '0' && $reserver['status'] == '1') {

            $doctorParams = array();
            foreach ($reserver as $name => $value) {
                if ($name[0] == 'd' && $name[1] == '_') {
                    $doctorParams[str_replace('d_', '', $name)] = $value;
                }
            }

            $doctorTitle = getUserDisplayName($doctorParams);
            $doctorTitle = "<strong>{$doctorTitle}</strong>";

            $form = new ReserveCancel();
            $form->setAttribute('data-cancel', url('admin/health-center/patient-panel/my-reservations'));
            $form->setAction(url('admin/health-center/patient-panel/my-reservations/cancel-request', array('resId' => $resId)));

            if ($this->request->isPost()) {

                $form->setData($this->request->getPost());
                $form->isValid();
                $formData = $form->getData();
                $cancelReason = $this->makeQuote($formData['cancelReason'], $reserver);

                $this->getDoctorReservationTable()->cancel($reserver['id'], 3, $cancelReason);
                $this->flashMessenger()->addSuccessMessage(t('Your reservation cancellation request submitted successfully'));
                $this->flashMessenger()->addSuccessMessage(t('A representative from the administration will contact you soon'));

                if ($notifyApi = getNotifyApi()) {

                    //region User
                    if (isset($reserver['email']) && has_value($reserver['email'])) {
                        $email = $notifyApi->getEmail();
                        $email->to = array($reserver['email'] => getUserDisplayName($reserver));
                        $email->from = Mail::getFrom();
                        $email->subject = t('Reserve Cancellation Request');
                        $email->queued = 0;
                    }

                    $internal = $notifyApi->getInternal()->uId = $reserver['userId'];

                    $notifyApi->notify('HealthCenter', 'reserve_cancel_request',
                        array(
                            '__DOCTOR_NAME__' => $doctorTitle,
                            '__DOCTOR_URL__' => Common::Link($doctorTitle,
                                App::siteUrl() . url('app/user/user-profile',
                                    array('id' => $reserver['id']),
                                    array('target' => '_blank'))),
                            '__RESERVE_CANCEL_REASON__' => $cancelReason
                        )
                    );
                    //endregion

                    //region ADMIN
                    $email = $notifyApi->getEmail();
                    $email->to = $notifyApi->getSystemRecipient()->to();
                    $email->from = Mail::getFrom();
                    $email->subject = t('Reserve Cancellation Request');
                    $email->queued = 0;

                    $internal = $notifyApi->getInternal()->uId = $notifyApi->getSystemRecipient()->id;

                    $notifyApi->notify('HealthCenter', 'reserve_cancel_request_admin',
                        array(
                            '__DOCTOR_NAME__' => $doctorTitle,
                            '__DOCTOR_URL__' => Common::Link($doctorTitle, App::siteUrl() . url('app/user/user-profile', array('id' => $reserver['doctorId']))),
                            '__RESERVATION__' => Common::Link(
                                $reserver['id'],
                                App::siteUrl() . url('admin/health-center/reservations', array(), array('query' => array('grid_filter_id' => $reserver['id']))),
                                array('target' => '_blank')
                            ),
                            '__USER_URL__' => Common::Link(
                                getUserDisplayName($reserver),
                                App::siteUrl() . url('app/user/user-profile', array('id' => $reserver['userId'])),
                                array('target' => '_blank')
                            ),
                            '__RESERVE_CANCEL_REASON__' => $cancelReason,
                        )
                    );
                    //endregion
                }

                return $this->indexAction();
            } else {

//                if ($reserver['status'] == '3')
                $form->setData(array(
                    'cancelReason' => t('Reserver request')
                ));

                $this->viewModel->setTemplate('health-center/reservation/cancel-request');
                $this->viewModel->setVariables(array(
                    'reserver' => $reserver,
                    'doctorTitle' => $doctorTitle,
                    'form' => $form
                ));
                return $this->viewModel;
            }
        } else {
            $this->flashMessenger()->addErrorMessage(t('Invalid Request !'));
            $this->flashMessenger()->addErrorMessage(t('This reservation cannot be canceled.'));
            return $this->indexAction();
        }
    }

    public function cancelAction()
    {
        $resId = $this->params()->fromRoute('resId', false);

        if (!$resId) {
            $this->flashMessenger()->addErrorMessage(t('Invalid Request !'));
            return $this->indexAction();
        }

        $reserver = $this->getDoctorReservationTable()->getReserver($resId);
        if (!$reserver) {
            $this->flashMessenger()->addErrorMessage(t('Invalid Request !'));
            return $this->indexAction();
        }

        $config = getConfig('health-center')->varValue;
        //global session cost, from global config
        $cost = $config['sessionCost'];
        //get doctor specific profile
        $doctorProfile = $this->getDoctorProfileTable()->get($reserver['doctorId']);
        //per doctor session cost, is this doctor has its own price ?
        if (isset($doctorProfile['sessionCost']))
            $cost = $doctorProfile['sessionCost'];

        $isDoctor = ($reserver['doctorId'] == current_user()->id);

        $action = url('admin/health-center/reservations/cancel', array('resId' => $resId));
        $cancelUrl = url('admin/health-center/reservations', array('resId' => $resId));
        if ($isDoctor) {
            $action = url('admin/health-center/doctor-panel/my-reservations/cancel', array('resId' => $resId));
            $cancelUrl = url('admin/health-center/doctor-panel/my-reservations', array('resId' => $resId));
        }

        $form = new ReserveCancel(true);
        $form->setAttribute('data-cancel', $cancelUrl);
        $form->setAction($action);
        $form->setData(array(
            'refund' => $cost
        ));

        $doctorParams = array();
        foreach ($reserver as $name => $value) {
            if ($name[0] == 'd' && $name[1] == '_') {
                $doctorParams[str_replace('d_', '', $name)] = $value;
            }
        }

        $doctorTitle = getUserDisplayName($doctorParams);
        $doctorTitle = "<strong>{$doctorTitle}</strong>";

        if ($this->request->isPost()) {

            $form->setData($this->request->getPost());
            $form->isValid();
            $formData = $form->getData();
            $cancelReason = $this->makeQuote($formData['cancelReason'], current_user());
            $refund = $formData['refund'];

            $this->getDoctorReservationTable()->cancel($resId, 4, $cancelReason);
            $this->flashMessenger()->addSuccessMessage('Reserve cancellation finalized');
            $this->flashMessenger()->addSuccessMessage(sprintf(
                t('This reservation was cost %s, amount of %s should be refunded'),
                strip_tags(currencyFormat($cost)), strip_tags(currencyFormat($refund))));

            if ($notifyApi = getNotifyApi()) {

                //region User
                if (isset($reserver['email']) && has_value($reserver['email'])) {
                    $email = $notifyApi->getEmail();
                    $email->to = array($reserver['email'] => getUserDisplayName($reserver));
                    $email->from = Mail::getFrom();
                    $email->subject = t('Reservation Cancellation');
                    $email->queued = 0;
                }

                if (isset($reserver['mobile']) && has_value($reserver['mobile'])) {
                    $sms = $notifyApi->getSms();
                    $sms->to = $reserver['mobile'];
                }

                $internal = $notifyApi->getInternal()->uId = $reserver['userId'];

                $event = 'reserve_cancel_user';
                //if the user has requested the cancel
                if ($reserver['status'] == '3')
                    $event = 'reserve_cancel_request_response';

                $notifyApi->notify('HealthCenter', $event,
                    array(
                        '__DOCTOR__' => Common::Link($doctorTitle,
                            App::siteUrl() . url('app/user/user-profile',
                                array('id' => $reserver['doctorId']),
                                array('target' => '_blank'))),
                        '__RESERVE_CANCEL_REASON__' => $cancelReason,
                        '__RESERVE_CANCEL_REFUND_AMOUNT__' => currencyFormat($refund)
                    )
                );
                //endregion


                if (!$isDoctor) {
                    //region DOCTOR
                    if (isset($doctorParams['email']) && has_value($doctorParams['email'])) {
                        $email = $notifyApi->getEmail();
                        $email->to = array($doctorParams['email'] => $doctorTitle);
                        $email->from = Mail::getFrom();
                        $email->subject = t('Reserve Cancellation');
                        $email->queued = 0;
                    }

                    if (isset($doctorParams['mobile']) && has_value($doctorParams['mobile'])) {
                        $sms = $notifyApi->getSms();
                        $sms->to = $doctorParams['mobile'];
                    }

                    $internal = $notifyApi->getInternal()->uId = $reserver['doctorId'];

                    $notifyApi->notify('HealthCenter', 'reserve_cancel_doctor',
                        array(
                            '__RESERVE_DATE__' => dateFormat($reserver['start'], 0, 3),
                            '__RESERVE_CANCEL_REASON__' => $cancelReason,
                            '__RESERVE_CANCEL_REFUND_AMOUNT__' => currencyFormat($refund)
                        )
                    );
                    //endregion
                } else {
                    //ADMIN
                    $systemRecipient = $notifyApi->getSystemRecipient();
                    if (isset($systemRecipient->email) && has_value($systemRecipient->email)) {
                        $email = $notifyApi->getEmail();
                        $email->to = array($systemRecipient->email => $systemRecipient->name);
                        $email->from = Mail::getFrom();
                        $email->subject = t('Reserve Cancellation');
                        $email->queued = 0;
                    }

                    if (isset($systemRecipient->mobile) && has_value($systemRecipient->mobile)) {
                        $sms = $notifyApi->getSms();
                        $sms->to = $systemRecipient->mobile;
                    }

                    $notifyApi->getInternal()->uId = $systemRecipient->id;

                    $notifyApi->notify('HealthCenter', 'reserve_cancel_admin',
                        array(
                            '__USER__' => Common::Link($doctorTitle,
                                App::siteUrl() . url('app/user/user-profile',
                                    array('id' => $reserver['userId']),
                                    array('target' => '_blank'))),
                            '__DOCTOR__' => Common::Link($doctorTitle,
                                App::siteUrl() . url('app/user/user-profile',
                                    array('id' => $reserver['doctorId']),
                                    array('target' => '_blank'))),
                            '__RESERVATION__' => dateFormat($reserver['start'], 0, 3),
                            '__RESERVE_CANCEL_REASON__' => $cancelReason,
                            '__RESERVE_CANCEL_REFUND_AMOUNT__' => currencyFormat($refund)
                        )
                    );

                    $this->flashMessenger()->addInfoMessage(sprintf(t('%s notified about cancellation'), $systemRecipient->name));
                }

            }

            //if the user has requested the cancel
            if ($reserver['status'] == '3') {
                if (getSM()->has('points_api'))
                    getSM('points_api')->addPoint('HealthCenter', 'Reserve.Cancel', $reserver['userId'], t('appointment reservation cancellation'));
                //update activity log
                $doctorUrl = Common::Link($doctorTitle,
                    App::siteUrl() . url('app/user/user-profile',
                        array('id' => $reserver['doctorId']),
                        array('target' => '_blank')));

                if (getSM()->has('customer_records_table'))
                    getSM('customer_records_table')->add($reserver['userId'], sprintf(t('canceled a reservation with %s'), $doctorUrl));
            }

            return $this->indexAction();
        } else {

            $this->viewModel->setTemplate('health-center/reservation/cancel');
            $this->viewModel->setVariables(array(
                'reserver' => $reserver,
                'doctorTitle' => $doctorTitle,
                'form' => $form
            ));
            return $this->viewModel;
        }
    }

    public function deleteAction()
    {
        if ($this->request->isPost()) {
            $id = $this->params()->fromPost('id', 0);
            if ($id) {
                $this->getDoctorReservationTable()->remove($id);
                return new JsonModel(array('status' => 1));
            }
        }
        return $this->unknownAjaxError();
    }
    //endregion

    //region Private Methods
    /**
     * @return DoctorReservationTable
     */
    private function getDoctorReservationTable()
    {
        return getSM('hc_doctor_reservation');
    }

    private function makeQuote($note, $writer)
    {
        if (has_value($note)) {
            $writer = getUserDisplayName($writer);
            return "<blockquote class='bg-info'>
                      <p>{$note}</p>
                      <footer>{$writer}</footer>
                    </blockquote>";
        }
        return '';
    }

    /**
     * @return DoctorProfileTable
     */
    private function getDoctorProfileTable()
    {
        return getSM('hc_doctor_profile_table');
    }
    //endregion
} 