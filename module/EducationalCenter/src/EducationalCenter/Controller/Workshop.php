<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 8/2/14
 * Time: 2:27 PM
 */

namespace EducationalCenter\Controller;


use Application\API\App;
use EducationalCenter\Model\WorkshopAttendance;
use EducationalCenter\Model\WorkshopAttendanceTable;
use EducationalCenter\Model\WorkshopClassTable;
use EducationalCenter\Model\WorkshopClass;
use EducationalCenter\Model\WorkshopTable;
use EducationalCenter\Model\WorkshopTimeTable;
use Mail\API\Mail;
use System\Controller\BaseAbstractActionController;
use Theme\API\Common;

class Workshop extends BaseAbstractActionController
{
    //region Public Methods
    public function workshopsAction()
    {
        $items = $this->getTable()->getAvailable($this->params()->fromQuery('page', 1));

        $this->viewModel->setVariables(array(
            'items' => $items
        ));
        $this->viewModel->setTemplate('educational-center/workshop/workshops');
        return $this->viewModel;
    }

    public function workshopAction()
    {
        $vv = array();
        $id = $this->params()->fromRoute('workshop', false);
        if (!$id)
            return $this->invalidRequest('app/workshops');

        $item = $this->getTable()->get($id);
        if ($item) {
            $vv['item'] = $item->current();
            $vv['classes'] = $this->render($this->classesAction($id));
        } else
            $vv['error'] = sprintf(t('Invalid Request, A workshop with id %s not found'), $id);

        $this->viewModel->setVariables($vv);
        $this->viewModel->setTemplate('educational-center/workshop/workshop');
        return $this->viewModel;
    }

    public function classesAction($workshop = null)
    {
        if (!$workshop)
            $workshop = $this->params()->fromRoute('workshop', false);

        if ($workshop) {
            $items = $this->getClassTable()->getAvailable($workshop, $this->params()->fromQuery('page', 1));
            $workshop = $this->getTable()->getItem($workshop);
        } else
            $items = $this->getClassTable()->getAllClasses($this->params()->fromQuery('page', 1));


        $this->viewModel->setVariables(array(
            'items' => $items,
            'workshop' => $workshop
        ));
        $this->viewModel->setTemplate('educational-center/workshop/classes');
        return $this->viewModel;
    }

    public function classAction()
    {
        $workshop = $this->params()->fromRoute('workshop', false);
        $class = $this->params()->fromRoute('class', false);
        if (!$workshop)
            return $this->invalidRequest('app/workshops');

        if (!$class)
            return $this->invalidRequest('app/workshop', array('workshop' => $workshop));

        $vv = array('workshop' => $workshop, 'class' => $class);
        $item = $this->getClassTable()->get($class);
        if ($item) {
            $vv['item'] = $item->current();
            $vv['timetable'] = $this->getTimeTable()->getAll(array('classId' => $class, 'status' => 0));
            $vv['users'] = $this->getAttendanceTable()->getAttendances($class);
        } else
            $vv['error'] = sprintf(t('Invalid Request, A class with id %s not found'), $class);

        $this->viewModel->setVariables($vv);
        $this->viewModel->setTemplate('educational-center/workshop/class');
        return $this->viewModel;
    }

    public function agreementAction()
    {
        $workshop = $this->params()->fromRoute('workshop', 0);
        $class = $this->params()->fromRoute('class', 0);

        $page = 0;
        $config = getConfig('educational-center')->varValue;
        if (isset($config['workshopClassRules']))
            $page = $config['workshopClassRules'];

        if ($page)
            $page = getSM('page_table')->get($page);

        $this->viewModel->setVariables(array(
            'page' => $page,
            'workshop' => $workshop,
            'class' => $class
        ));
        $this->viewModel->setTemplate('educational-center/workshop/agreement');
        return $this->viewModel;
    }

    public function registerAction()
    {
        $workshop = $this->params()->fromRoute('workshop', false);
        $class = $this->params()->fromRoute('class', false);
        if (!$workshop)
            return $this->invalidRequest('app/workshops');

        if (!$class)
            return $this->invalidRequest('app/workshop', array('workshop' => $workshop));

        //is logged in
        if (!current_user()->id) {
            $this->flashMessenger()->addInfoMessage('To register to any class ,first you need to be a member in the site.');
            $this->flashMessenger()->addInfoMessage('Login or Signup to register for this class.');
            return $this->redirect()->toRoute('app/user/login', array(), array('query' => array('redirect' => urlencode($this->request->getRequestUri()))));
        }

        $firstSession = $this->getTimeTable()->getFirstSession($class);
        $config = getConfig('educational-center')->varValue;
        //is this class started
        if ($firstSession && $firstSession < time()) {
            $this->flashMessenger()->addInfoMessage('This class is already started, registration is closed for this class');
            return $this->redirect()->toRoute('app/workshop/class', array('workshop' => $workshop, 'class' => $class));
        }

        //is there still time to register ?
        $classRegisterTimeout = WorkshopClassTable::CLASS_REGISTER_TIMEOUT;
        if (isset($config['classRegisterTimeout']))
            $classRegisterTimeout = (int)$config['classRegisterTimeout'];
        if ($firstSession && ($firstSession - ($classRegisterTimeout * 60 * 60) < time())) {
            $this->flashMessenger()->addInfoMessage('The allowed time for registering for this class is over');
            return $this->redirect()->toRoute('app/workshop/class', array('workshop' => $workshop, 'class' => $class));
        }

        //is this user already registered
        if ($this->getAttendanceTable()->isRegistered($class, current_user()->id)) {
            $this->flashMessenger()->addInfoMessage('You already have registered in this class.');
            return $this->redirect()->toRoute('app/workshop/class', array('workshop' => $workshop, 'class' => $class));
        }


        //check if user profile is complete
        //TODO get this from config
//        $requiredUserProfileFields = array(
//            'user' => array('email' => 'Email'),
//            'profile' => array(
//                'firstName' => 'First Name',
//                'lastName' => 'Last Name',
//                'phone' => 'Phone',
//                'mobile' => 'Mobile',
//                'birthDate' => 'Birth Date',
//            ),
//            'customProfile' => array(
//                'education' => 'Education',
//                'job' => 'Job'
//            )
//        );
//
//        $missingUserProfileItems = array();
//        $user = (array)getSM('user_table')->getUser(current_user()->id);
//        $userProfile = (array)getSM('user_profile_table')->getByUserId(current_user()->id);
//        $hasFieldsApi = $this->hasFieldsApi();
//        $userCustomProfile = false;
//        if ($hasFieldsApi) {
//            /* @var $fields_api \Fields\API\Fields */
//            $fields_api = $this->getFieldsApi();
//            $fields_table = $this->getFieldsApi()->init('user_profile');
//            $userCustomProfile = $fields_api->getFieldData(current_user()->id);
//        }
//        foreach ($requiredUserProfileFields as $section => $fields) {
//            $data = null;
//            switch ($section) {
//                case 'user':
//                    $data = $user;
//                    break;
//                case 'profile':
//                    $data = $userProfile;
//                    break;
//                case 'customProfile':
//                    $data = $userCustomProfile;
//                    break;
//            }
//            foreach ($fields as $name => $text) {
//                if ($data)
//                    if (!has_value(@$data[$name]))
//                        $missingUserProfileItems[$section][] = t($text);
//            }
//        }
//
//        if (count($missingUserProfileItems)) {
//            $this->viewModel->setVariables(array('items' => $missingUserProfileItems));
//            $this->viewModel->setTemplate('educational-center/workshop/missing-profile');
//            return $this->viewModel;
//        }

        //first time reserver and has not filled the form
        if ($this->params()->fromRoute('skip-form') != true && $this->params()->fromQuery('skip-form') != 1) {
            return $this->forward()->dispatch('EducationalCenter\Controller\WorkshopAttendance',
                array('action' => 'edit-signup-form', 'workshop' => $workshop, 'class' => $class));
        }

        //is there on empty space to register
        $classItem = $this->getClassTable()->getItem($class);
        $capacity = $classItem->capacity;
        $used = $this->getAttendanceTable()->getRegisteredCount($class);
        if ($capacity - $used == 0) {
            $this->flashMessenger()->addErrorMessage(t("The Selected class's Capacity is full."));
            return $this->redirect()->toRoute('app/workshop/class', array('workshop' => $workshop, 'class' => $class));
        }

        //we are good to go , save the request
        $attendance = new WorkshopAttendance();
        $attendance->userId = current_user()->id;
        $attendance->classId = $class;
        $attendance->registerDate = time();
        $requestId = $this->getAttendanceTable()->save($attendance);
        if ($requestId) {

            if (isset($config['classPaymentTimeout']))
                $timeForPayment = (int)$config['classPaymentTimeout'];
            else
                $timeForPayment = WorkshopAttendanceTable::CLASS_PAYMENT_TIMEOUT;

            //heeeeeeeeeeeeha we are victorious
            $classTitle = $classItem->title;

            $this->flashMessenger()->addSuccessMessage(sprintf(t('Your request with id %s is submitted successfully.'), $requestId));
            $this->flashMessenger()->addSuccessMessage(sprintf(t('you have initialized your registration for the %s'), $classTitle));
//            $this->flashMessenger()->addWarningMessage(sprintf(t('This registration is temporary and if payment is not completed in less than %s hours it will be canceled'), $timeForPayment));

            if ($notifyApi = getNotifyApi()) {
                if (isset($user['email']) && has_value($user['email'])) {
                    $email = $notifyApi->getEmail();
                    $email->to = array($user['email'] => getUserDisplayName($user));
                    $email->from = Mail::getFrom();
                    $email->subject = t('initial workshop class registration');
                    $email->entityType = 'EducationalCenterWorkshop';
                    $email->queued = 0;
                }

                $classTitle = "<strong>{$classTitle}</strong>";
                $notifyApi->notify('EducationalCenter', 'workshop_user_reg_temp', array(
                    '__WORKSHOP_CLASS_REQUEST_ID__' => $requestId,
                    '__WORKSHOP_CLASS_NAME__' => $classTitle,
                    '__WORKSHOP_CLASS_URL__' => Common::Link($classTitle, App::siteUrl() . url('app/workshop/class', array('workshop' => $classItem->workshopId, 'class' => $classItem->id))),
                    '__WORKSHOP_TIME_FOR_PAYMENT__' => $timeForPayment,
                ));
            }

            //lets pay the piper
            $paymentParams = array(
                'amount' => $classItem->price,
                'comment' => 'Registration for workshop class [' . $classItem->title . ']',
                'validate' => array(
                    'route' => 'app/workshops/finalize-payment',
                    'params' => array(
                        'attendance' => $requestId,
                    ),
                )
            );
            $paymentParams = serialize($paymentParams);
            $paymentParams = base64_encode($paymentParams);
            return $this->redirect()->toRoute('app/payment', array(), array('query' => array('routeParams' => $paymentParams)));
        } else {
            //wooooooooooooops something went wrong ???? there is no insert id
            $this->flashMessenger()->addErrorMessage(t('Something went wrong while creating your request, please try again later.'));
            return $this->redirect()->toRoute('app/workshop/class', array('workshop' => $workshop, 'class' => $class));
        }

        die('We should never get this far, it is dangerous ...');
    }

    public function finalizePaymentAction()
    {
        $params = $this->params()->fromRoute('params', false);
        $paymentId = $this->params()->fromRoute('paymentId', false);

        if (!$params || !$paymentId) {
            $this->invalidRequest('app/workshops');
        }

        $params = unserialize(base64_decode($params));

        if (!isset($params['attendance']))
            $this->invalidRequest('app/workshops');

        $attendance = (array)$this->getAttendanceTable()->getAttendance($params['attendance']);
        $classTitle = $attendance['title'] . ' » ' . $attendance['workshopTitle'];

        $payment_message = App::getSession()->offsetGet('payment_message');
        App::getSession()->offsetUnset('payment_message');
        foreach ($payment_message as $msg) {
            $this->flashMessenger()->addSuccessMessage($msg);
        }

        $this->getAttendanceTable()->update(array('paymentId' => $paymentId, 'paymentStatus' => 1, 'status' => 1), array('id' => $params['attendance']));
        $this->flashMessenger()->addSuccessMessage(sprintf(t('You have successfully finalized your registration for %s'), $classTitle));

        $classTitle = "<strong>{$classTitle}</strong>";
        $classUrl = Common::Link(
            $classTitle,
            App::siteUrl() . url('app/workshop/class',
                array('workshop' => $attendance['workshopId'], 'class' => $attendance['classId'])));
        if ($notifyApi = getNotifyApi()) {
            //region Notify Attendance
            if (isset($attendance['email']) && has_value($attendance['email'])) {
                $email = $notifyApi->getEmail();
                $email->to = array($attendance['email'] => getUserDisplayName($attendance));
                $email->from = Mail::getFrom();
                $email->subject = t('Finalized workshop class registration');
                $email->entityType = 'EducationalCenterWorkshop';
                $email->queued = 0;
            }

            if (isset($attendance['mobile']) && has_value($attendance['mobile'])) {
                $sms = $notifyApi->getSms();
                $sms->to = $attendance['mobile'];
            }

            $notifyApi->notify('EducationalCenter', 'workshop_user_reg_fin', array(
                '__WORKSHOP_CLASS_NAME__' => $classTitle,
                '__WORKSHOP_CLASS_URL__' => $classUrl,
            ));
            //endregion
        }

        //add point to user
        if (getSM()->has('points_api'))
            getSM('points_api')->addPoint('EducationalCenter', 'Register.Done', $attendance['userId'], t('class registration'));
        //update users activity log
        if (getSM()->has('customer_records_table'))
            getSM('customer_records_table')->add($attendance['userId'], sprintf(t('registered for %s'), $classUrl));

        return $this->classesAction();
    }

    //endregion

    //region Private Methods
    /**
     * @return WorkshopTable
     */
    private function getTable()
    {
        return getSM('ec_workshop_table');
    }

    /**
     * @return WorkshopClassTable
     */
    private function getClassTable()
    {
        return getSM('ec_workshop_class_table');
    }

    /**
     * @return WorkshopTimeTable
     */
    private function getTimeTable()
    {
        return getSM('ec_workshop_timetable');
    }

    /**
     * @return WorkshopAttendanceTable
     */
    private function getAttendanceTable()
    {
        return getSM('ec_workshop_attendance_table');
    }
    //endregion
} 