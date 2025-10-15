<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 7/27/14
 * Time: 11:24 AM
 */

namespace EducationalCenter\Controller;


use Application\API\App;
use DataView\Lib\Button;
use DataView\Lib\Column;
use DataView\Lib\Custom;
use DataView\Lib\DataGrid;
use DataView\Lib\Visualizer;
use EducationalCenter\Form\AttendanceCancel;
use EducationalCenter\Form\Signup;
use EducationalCenter\Model\WorkshopAttendanceTable;
use EducationalCenter\Model\WorkshopClassTable;
use EducationalCenter\Module;
use Localization\API\Date;
use Mail\API\Mail;
use System\Controller\BaseAbstractActionController;
use System\Form\Buttons;
use Theme\API\Common;
use Zend\Db\Sql\Predicate\Expression;

class WorkshopAttendance extends BaseAbstractActionController
{
    private $workshop;
    private $class;

    public function indexAction($type = 'current-class')
    {
        if ($type == 'current-class')
            if (($result = $this->init()) !== true)
                return $result;

        $grid = new DataGrid('ec_workshop_attendance_table');
        if ($type == 'current-user')
            $grid->route = 'admin/educational-center/my-workshop-classes';
        elseif ($type == 'all')
            $grid->route = 'admin/educational-center/attendance';
        else {
            $grid->route = 'admin/educational-center/workshop/class/attendance';
            $grid->routeParams = array('workshop' => $this->workshop, 'class' => $this->class);
        }

        $columns = array();

        $id = new Column('id', 'Id', array(
            'headerAttr' => array('width' => '70px', 'align' => 'center'),
            'attr' => array('align' => 'center')
        ));
        $grid->setIdCell($id);
        $columns[] = $id;

        if ($type != 'current-user') {
            $user = new Button('User', function (Button $col) {
                $col->route = 'admin/educational-center/participant-panel';
                $col->routeParams = array('id' => $col->dataRow->userId);
                $col->text = getUserDisplayName($col->dataRow);
                $col->contentAttr['class']['ajax_page_load'] = 'ajax_page_load';
            });
            $columns[] = $user;
        }
        if ($type != 'current-class') {
            $workshop = new Button('Workshop',
                function (Button $col) {
                    $col->route = 'app/workshop';
                    $col->routeParams['workshop'] = $col->dataRow->workshopId;
                    $col->text = $col->dataRow->workshopTitle;
                },
                array(
                    'contentAttr' => array('target' => '_blank')
                )
            );
            $columns[] = $workshop;

            $class = new Button('Class',
                function (Button $col) {
                    $col->route = 'app/workshop/class';
                    $col->routeParams['workshop'] = $col->dataRow->workshopId;
                    $col->routeParams['class'] = $col->dataRow->classId;
                    $col->text = $col->dataRow->classTitle;
                },
                array(
                    'contentAttr' => array('target' => '_blank')
                )
            );
            $columns[] = $class;
        }

        $date = new \DataView\Lib\Date('registerDate', 'Register date', array());
        $columns[] = $date;

        $firstSession = new \DataView\Lib\Date('firstSession', 'First Session', array(), 0, 3);
        $firstSession->sortable = false;
        $columns[] = $firstSession;

        $paymentStatus = new Visualizer('paymentStatus', 'Payment Status',
            array(
                '0' => 'glyphicon glyphicon-question-sign text-warning grid-icon',
                '1' => 'glyphicon glyphicon-ok text-success grid-icon'
            ),
            array(
                '0' => t('Unknown'),
                '1' => t('Done')
            ),
            array(
                'headerAttr' => array('width' => '35px', 'align' => 'center'),
                'attr' => array('align' => 'center')
            )
        );
        $columns[] = $paymentStatus;

        $config = getConfig('educational-center')->varValue;

        $paymentId = new Custom('paymentId', 'Payment Id',
            function (Custom $col) use ($type, $config) {
                if ($col->dataRow->paymentId == '0' && $col->dataRow->status == '0' && $type == 'current-user') {
                    if (isset($config['classPaymentTimeout']))
                        $classPaymentTimeout = (int)$config['classPaymentTimeout'];
                    else
                        $classPaymentTimeout = WorkshopAttendanceTable::CLASS_PAYMENT_TIMEOUT;

                    if (($classPaymentTimeout * 60) + $col->dataRow->registerDate > time()) {
                        $paymentParams = array(
                            'amount' => $col->dataRow->price,
                            'comment' => 'Registration for workshop class [' . $col->dataRow->classTitle . ']',
                            'validate' => array(
                                'route' => 'app/workshops/finalize-payment',
                                'params' => array(
                                    'attendance' => $col->dataRow->id,
                                ),
                            )
                        );
                        $paymentParams = serialize($paymentParams);
                        $paymentParams = base64_encode($paymentParams);
                        $paymentUrl = url('app/payment', array(), array('query' => array('routeParams' => $paymentParams)));
                        return Common::Link(
                            "<span class='glyphicon glyphicon-shopping-cart'></span>",
                            $paymentUrl,
                            array('class' => array('btn', 'btn-default', 'btn-xs'), 'target' => '_blank', 'title' => t('Pay Now'))
                        );
                    } else {
                        $title = t('This registration has failed due to payment timeout');
                        return "<span class='glyphicon glyphicon-minus-sign text-danger grid-icon' title='{$title}'></span>";
                    }
                } else
                    return $col->dataRow->paymentId;
            },
            array(
                'headerAttr' => array('width' => '35px', 'align' => 'center'),
                'attr' => array('align' => 'center')
            )
        );
        $columns[] = $paymentId;

        $status = new Visualizer('status', 'Status',
            array(
                '0' => 'glyphicon glyphicon-exclamation-sign text-warning grid-icon',
                '1' => 'glyphicon glyphicon-ok text-success grid-icon',
                '2' => 'glyphicon glyphicon-remove text-danger grid-icon',
                '3' => 'glyphicon glyphicon-exclamation-sign text-danger grid-icon',
                '4' => 'glyphicon glyphicon-ban-circle text-danger grid-icon'
            ),
            array(
                '0' => t('Temporarily reserved, waiting for payment'),
                '1' => t('Reserved'),
                '2' => t('Failed Reservation, payment was not done in time'),
                '3' => t('Cancel Request'),
                '4' => t('Canceled'),
            ),
            array(
                'headerAttr' => array('width' => '35px', 'align' => 'center'),
                'attr' => array('align' => 'center')
            )
        );
        $columns[] = $status;

        $classCancelTimeout = 168;
        if (isset($config['classCancelTimeout']) && !empty($config['classCancelTimeout']))
            $classCancelTimeout = (int)$config['classCancelTimeout'];
        $now = time();
        $classCancelTimeout = strtotime('+ ' . $classCancelTimeout . ' hours', $now);
        $classCancelTimeoutInterval = Date::formatInterval($classCancelTimeout - $now);
        if ($type == 'current-user') {
            $cancel = new Custom('cancel', 'Cancel',
                function (Custom $col) use ($classCancelTimeout, $classCancelTimeoutInterval) {
                    if (($col->dataRow->firstSession == null || $col->dataRow->firstSession > $classCancelTimeout) && $col->dataRow->paymentId != '0' && $col->dataRow->status == '1') {
                        return Common::Link(
                            "<span class='glyphicon glyphicon-remove-sign text-danger'></span>",
                            url('admin/educational-center/my-registered-workshop-classes/cancel-request', array('id' => $col->dataRow->id)),
                            array('class' => array('btn', 'btn-default', 'btn-xs', 'ajax_page_load'), 'title' => t('Cancel'))
                        );
                    }
                },
                array(
                    'headerAttr' => array('width' => '35px', 'align' => 'center'),
                    'attr' => array('align' => 'center')
                )
            );
            $columns[] = $cancel;
        } else {
            if (isAllowed(Module::ATTENDANCE_CANCEL)) {
                $cancel = new Custom('cancel', 'Cancel',
                    function (Custom $col) {
                        if ($col->dataRow->status != '4') {
                            return Common::Link(
                                "<span class='glyphicon glyphicon-remove-sign text-danger'></span>",
                                url('admin/educational-center/workshop/class/attendance/cancel',
                                    array('id' => $col->dataRow->id, 'workshop' => $col->dataRow->workshopId, 'class' => $col->dataRow->classId)),
                                array('class' => array('btn', 'btn-default', 'btn-xs', 'ajax_page_load'), 'title' => t('Cancel'))
                            );
                        }
                    },
                    array(
                        'headerAttr' => array('width' => '35px', 'align' => 'center'),
                        'attr' => array('align' => 'center')
                    )
                );
                $columns[] = $cancel;
            }
        }

        $grid->addColumns($columns);

        $select = $grid->getSelect();
        $select
            ->columns(
                array('id', 'registerDate', 'classId', 'userId', 'paymentId', 'paymentStatus', 'status',
                    'firstSession' => new Expression("(SELECT MIN(`start`) FROM `tbl_ec_workshop_timetable` WHERE `classId` = `c`.`id` AND `status` = '0')"),
                ))
            ->join(array('c' => 'tbl_ec_workshop_class'), $grid->getTableGateway()->table . '.classId=c.id', array('classTitle' => 'title', 'workshopId', 'price'))
            ->join(array('u' => 'tbl_users'), $grid->getTableGateway()->table . '.userId=u.id', array('username', 'displayName', 'email'))
            ->join(array('up' => 'tbl_user_profile'), 'up.userId=u.id', array('firstName', 'lastName', 'mobile'), 'LEFT');

        if ($type != 'current-class') {
            $select
                ->join(array('w' => 'tbl_ec_workshop'), 'c.workshopId=w.id', array('workshopTitle' => 'title'));
        }
        if ($type == 'current-class')
            $select->where(array($grid->getTableGateway()->table . '.classId' => $this->class));
        elseif ($type == 'current-user')
            $select->where(array($grid->getTableGateway()->table . '.userId' => current_user()->id));

        $grid->defaultSort = $id;
        $grid->defaultSortDirection = 'DESC';

        $this->viewModel->setVariables(
            array(
                'grid' => $grid->render(),
                'buttons' => $grid->getButtons(),
                'type' => $type,
                'classCancelTimeoutInterval' => $classCancelTimeoutInterval
            ));
        $this->viewModel->setTemplate('educational-center/workshop-attendance/index');
        return $this->viewModel;
    }

    public function myWorkshopClassesAction()
    {
        return $this->indexAction('current-user');
    }

    public function attendanceAction()
    {
        return $this->indexAction('all');
    }

    public function cancelRequestAction()
    {
        $attendance = $this->params()->fromRoute('id', false);
        if (!$attendance) {
            $this->flashMessenger()->addErrorMessage(t('Invalid Request !'));
            $this->flashMessenger()->addErrorMessage(t('No identification found in the request !'));
            return $this->myWorkshopClassesAction();
        }

        $attendance = $this->getAttendanceTable()->getAttendanceForCancel($attendance);
        if (!$attendance) {
            $this->flashMessenger()->addErrorMessage(t('Invalid Request !'));
            return $this->myWorkshopClassesAction();
        }

        //IS THIS USER REGISTERED IN THIS CLASS
        if ($attendance->userId != current_user()->id) {
            $this->flashMessenger()->addErrorMessage(t('Invalid Request !'));
            $this->flashMessenger()->addErrorMessage(t('Users can only cancel their own registrations.'));
            return $this->myWorkshopClassesAction();
        }

        $classCancelTimeout = 168;//1 week/7 days
        $config = getConfig('educational-center')->varValue;
        if (isset($config['classCancelTimeout']) && !empty($config['classCancelTimeout']))
            $classCancelTimeout = (int)$config['classCancelTimeout'];

        $now = time();
        $classCancelTimeout = strtotime('+ ' . $classCancelTimeout . ' hours', $now);
        if ($attendance->firstSession == null || $attendance->firstSession > $classCancelTimeout) {
            $classCancelTimeout = Date::formatInterval($classCancelTimeout - $now);
            $this->flashMessenger()->addErrorMessage(sprintf(t("The registration can only be canceled %s before start date."), $classCancelTimeout));
            return $this->myWorkshopClassesAction();
        }

        //can be canceled / has paymentId / status=1 and has not yet started
        if ($attendance->paymentId != '0' && $attendance->status == '1') {
            $classTitle = $attendance->classTitle . ' » ' . $attendance->workshopTitle;
            $classTitle = "<strong>{$classTitle}</strong>";

            $form = new AttendanceCancel();
            $form->setAttribute('data-cancel', url('admin/educational-center/my-registered-workshop-classes'));
            $form->setAction(url('admin/educational-center/my-registered-workshop-classes/cancel-request', array('id' => $attendance->id)));

            if ($this->request->isPost()) {

                $form->setData($this->request->getPost());
                $form->isValid();
                $formData = $form->getData();
                $cancelReason = $this->makeQuote($formData['cancelReason'], $attendance);

                $this->getAttendanceTable()->cancel($attendance->id, 3, $cancelReason);
                $this->flashMessenger()->addSuccessMessage(t('Your class cancellation request submitted successfully'));
                $this->flashMessenger()->addSuccessMessage(t('A representative from the administration will contact you soon'));

                if ($notifyApi = getNotifyApi()) {

                    $classTitle = $attendance->classTitle . ' » ' . $attendance->workshopTitle;
                    $classTitle = "<strong>{$classTitle}</strong>";

                    //region User
                    if (isset($attendance->email) && has_value($attendance->email)) {
                        $email = $notifyApi->getEmail();
                        $email->to = array($attendance->email => getUserDisplayName($attendance));
                        $email->from = Mail::getFrom();
                        $email->subject = t('Class Cancellation Request');
                        $email->queued = 0;
                    }

                    $internal = $notifyApi->getInternal()->uId = $attendance->userId;

                    $notifyApi->notify('EducationalCenter', 'workshop_class_cancel_request',
                        array(
                            '__WORKSHOP_CLASS_NAME__' => $classTitle,
                            '__WORKSHOP_CLASS_URL__' => Common::Link($classTitle,
                                App::siteUrl() . url('app/workshop/class',
                                    array('workshop' => $attendance->workshopId, 'class' => $attendance->classId),
                                    array('target' => '_blank'))),
                            '__WORKSHOP_CLASS_CANCEL_REASON__' => $cancelReason
                        )
                    );
                    //endregion

                    //region ADMIN
                    $email = $notifyApi->getEmail();
                    $email->to = $notifyApi->getSystemRecipient()->to();
                    $email->from = Mail::getFrom();
                    $email->subject = t('Class Cancellation Request');
                    $email->queued = 0;

                    $internal = $notifyApi->getInternal()->uId = $notifyApi->getSystemRecipient()->id;

                    $notifyApi->notify('EducationalCenter', 'workshop_class_cancel_request_admin',
                        array(
                            '__WORKSHOP_CLASS_NAME__' => $classTitle,
                            '__WORKSHOP_CLASS_URL__' => Common::Link($classTitle, App::siteUrl() . url('app/workshop/class', array('workshop' => $attendance->workshopId, 'class' => $attendance->classId))),
                            '__WORKSHOP_ATTENDANCE__' => Common::Link(
                                $attendance->id,
                                App::siteUrl() . url('admin/educational-center/workshop/class/attendance',
                                    array(
                                        'workshop' => $attendance->workshopId,
                                        'class' => $attendance->classId,
                                        'attendance' => $attendance->id
                                    )
                                )
                            ),
                            '__USER_URL__' => Common::Link(
                                getUserDisplayName($attendance),
                                App::siteUrl() . url('app/user/user-profile', array('id' => $attendance->userId)),
                                array('target' => '_blank')
                            ),
                            '__WORKSHOP_CLASS_CANCEL_REASON__' => $cancelReason,
                        )
                    );
                    //endregion
                }

                return $this->myWorkshopClassesAction();
            } else {

                if ($attendance->status == '3')
                    $form->setData(array(
                        'cancelReason' => t('Attendance request')
                    ));

                $this->viewModel->setTemplate('educational-center/workshop-attendance/cancel-request');
                $this->viewModel->setVariables(array(
                    'attendance' => $attendance,
                    'classTitle' => $classTitle,
                    'form' => $form
                ));
                return $this->viewModel;
            }
        } else {
            $this->flashMessenger()->addErrorMessage(t('Invalid Request !'));
            $this->flashMessenger()->addErrorMessage(t('This registration cannot be canceled.'));
            return $this->myWorkshopClassesAction();
        }
    }

    public function cancelAction()
    {
        if (($result = $this->init()) !== true)
            return $result;

        $id = $this->params()->fromRoute('id', false);

        if (!$id) {
            $this->flashMessenger()->addErrorMessage(t('Invalid Request !'));
            return $this->indexAction();
        }

        $attendance = $this->getAttendanceTable()->getAttendanceForCancel($id);
        if (!$attendance) {
            $this->flashMessenger()->addErrorMessage(t('Invalid Request !'));
            return $this->indexAction();
        }

        $classTitle = $attendance->classTitle . ' » ' . $attendance->workshopTitle;
        $classTitle = "<strong>{$classTitle}</strong>";

        $form = new AttendanceCancel(true);
        $form->setAttribute('data-cancel', url('admin/educational-center/workshop/class/attendance', array('workshop' => $attendance->workshopId, 'class' => $attendance->classId)));
        $form->setAction(url('admin/educational-center/workshop/class/attendance/cancel', array('workshop' => $attendance->workshopId, 'class' => $attendance->classId, 'id' => $attendance->id)));
        $form->setData(array(
            'refund' => $attendance->price
        ));
        if ($this->request->isPost()) {

            $form->setData($this->request->getPost());
            $form->isValid();
            $formData = $form->getData();
            $cancelReason = $this->makeQuote($formData['cancelReason'], current_user());
            $refund = $formData['refund'];

            $this->getAttendanceTable()->cancel($id, 4, $cancelReason);
            $this->flashMessenger()->addSuccessMessage(t('Class cancellation finalized'));
            /* @var $currentFormat callable */
            $currentFormat = $this->vhm()->get('currency_format');
            $this->flashMessenger()->addSuccessMessage(sprintf(t('This registration was cost %s, amount of %s should be refunded'), strip_tags($currentFormat($attendance->price)), strip_tags($currentFormat($refund))));

            $classUrl = Common::Link($classTitle,
                App::siteUrl() . url('app/workshop/class',
                    array('workshop' => $attendance->workshopId, 'class' => $attendance->classId),
                    array('target' => '_blank')));
            if ($notifyApi = getNotifyApi()) {

                //region User
                if (isset($attendance->email) && has_value($attendance->email)) {
                    $email = $notifyApi->getEmail();
                    $email->to = array($attendance->email => getUserDisplayName($attendance));
                    $email->from = Mail::getFrom();
                    $email->subject = t('Class Cancellation');
                    $email->queued = 0;
                }

                if (isset($attendance->mobile) && has_value($attendance->mobile)) {
                    $sms = $notifyApi->getSms();
                    $sms->to = $attendance->mobile;
                }

                $internal = $notifyApi->getInternal()->uId = $attendance->userId;

                $notifyApi->notify('EducationalCenter', 'workshop_class_reg_canceled',
                    array(
                        '__WORKSHOP_CLASS_NAME__' => $classTitle,
                        '__WORKSHOP_CLASS_URL__' => $classUrl,
                        '__WORKSHOP_CLASS_CANCEL_REASON__' => $cancelReason,
                        '__WORKSHOP_CLASS_CANCEL_REFUND_AMOUNT__' => $currentFormat($refund)
                    )
                );
                //endregion
            }

            //trigger reserve done event
            if (getSM()->has('points_api'))
                getSM('points_api')->addPoint('EducationalCenter', 'Register.Cancel', $attendance->userId, t('class registration cancellation'));
            //update users activity log
            if (getSM()->has('customer_records_table'))
                getSM('customer_records_table')->add($attendance->userId, sprintf(t('canceled a registration for %s'), $classUrl));

            return $this->forward()->dispatch('EducationalCenter\Controller\WorkshopAttendance', array(
                'action' => 'index',
                'workshop' => $attendance->workshopId,
                'class' => $attendance->classId
            ));
        } else {

            $this->viewModel->setTemplate('educational-center/workshop-attendance/cancel');
            $this->viewModel->setVariables(array(
                'attendance' => $attendance,
                'classTitle' => $classTitle,
                'form' => $form
            ));
            return $this->viewModel;
        }
    }

    public function editSignupFormAction()
    {
        $workshop = $this->params()->fromRoute('workshop', false);
        $class = $this->params()->fromRoute('class', false);

        $userId = current_user()->id;

        $fieldsApi = null;
        $recordsData = null;
        if ($this->hasFieldsApi()) {
            $fieldsApi = $this->getFieldsApi();
            $fieldsApi->init('workshop_signup_form');
            $recordsData = $fieldsApi->getFieldData($userId);
        }

        $form = new Signup();
        $form->setAction($this->request->getRequestUri());

        if ($fieldsApi) {
            $inputFilters = $fieldsApi->loadFieldsByType('workshop_signup_form', $form);
            $form->setInputFiltersConfig($inputFilters);
        }

        $form->add(new Buttons('workshop_signup_form', array(Buttons::SAVE, Buttons::CSRF, Buttons::SPAM)));
        prepareForm($form, array(), array('submit' => 'AP_SAVE_CONTINUE'));

        if ($recordsData) {
            $form->setData($recordsData);
        }

        if ($this->request->isPost()) {
            if ($this->isSubmit()) {
                $form->setData($this->request->getPost());
                if ($form->isValid()) {
                    $data = $form->getData();
                    unset($data['buttons']);

                    //editing the old data
                    if ($recordsData)
                        $data['id'] = $recordsData['id'];

                    if ($fieldsApi) {
                        $fieldsApi->save('workshop_signup_form', $userId, $data);
                        $this->flashMessenger()->addSuccessMessage('Workshop signup form updated successfully');
                    } else
                        $this->flashMessenger()->addErrorMessage('Workshop signup form requires Fields module to work');

                    if ($workshop && $class)
                        return $this->forward()->dispatch('EducationalCenter\Controller\Workshop',
                            array('action' => 'register', 'workshop' => $workshop, 'class' => $class, 'skip-form' => true));
                    else
                        return $this->participantPanelAction();
                } else {
                    $this->formHasErrors();
                }
            }
        }

        $this->viewModel->setVariables(array('form' => $form));
        $this->viewModel->setTemplate('educational-center/workshop-attendance/edit-signup-form');
        return $this->viewModel;
    }

    public function participantPanelAction()
    {
        $currentUserId = current_user()->id;
        $userId = $this->params()->fromRoute('id', false);
        if (!$userId)
            $userId = $currentUserId;

        $viewVars = array();

        $signup_form = array();
        $fieldsApi = null;
        if ($this->hasFieldsApi()) {
            $fieldsApi = $this->getFieldsApi();
            $fieldsApi->init('workshop_signup_form');
            $fields = $this->getFieldsTable()->getByEntityType('workshop_signup_form')->toArray();
            $data = $fieldsApi->getFieldData($userId);
            $signup_form = $fieldsApi->generate($fields, $data);
        }

        $viewVars['userId'] = $userId;
        $viewVars['workshop_signup_form'] = $signup_form;

        $this->viewModel->setVariables($viewVars);
        $this->viewModel->setTemplate('educational-center/workshop-attendance/panel');
        return $this->viewModel;
    }


    //region Private Methods
    private function init()
    {
        $this->workshop = $this->params()->fromRoute('workshop', false);
        if (!$this->workshop) {
            return $this->invalidRequest('admin/educational-center/workshop');
        }

        $this->class = $this->params()->fromRoute('class', false);
        if (!$this->class) {
            return $this->invalidRequest('admin/educational-center/workshop/class', array('workshop' => $this->workshop));
        }

        return true;
    }

    /**
     * @return WorkshopAttendanceTable
     */
    private function getAttendanceTable()
    {
        return getSM('ec_workshop_attendance_table');
    }

    /**
     * @return WorkshopClassTable
     */
    private function getClassTable()
    {
        return getSM('ec_workshop_class_table');
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
    //endregion
}