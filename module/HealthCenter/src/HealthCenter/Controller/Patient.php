<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 8/30/14
 * Time: 10:56 AM
 */

namespace HealthCenter\Controller;


use HealthCenter\Form\PatientProfile;
use HealthCenter\Model\DoctorRefTable;
use HealthCenter\Model\DoctorReservationTable;
use System\Controller\BaseAbstractActionController;
use System\Form\Buttons;
use User\Model\UserTable;

class Patient extends BaseAbstractActionController
{
    //region Public Methods
    public function editProfileAction()
    {
        $doctor = $this->params()->fromRoute('doctor', false);
        $time = $this->params()->fromRoute('time', false);

        $userId = current_user()->id;

        $fieldsApi = null;
        $recordsData = null;
        if ($this->hasFieldsApi()) {
            $fieldsApi = $this->getFieldsApi();
            $fieldsApi->init('medical_records');
            $recordsData = $fieldsApi->getFieldData($userId);
        }

        $form = new PatientProfile();
        $form->setAction($this->request->getRequestUri());

        if ($fieldsApi) {
            $inputFilters = $fieldsApi->loadFieldsByType('medical_records', $form);
            $form->setInputFiltersConfig($inputFilters);
        }

        $form->add(new Buttons('PatientProfile', array(Buttons::SAVE, Buttons::CSRF, Buttons::SPAM)));
        prepareForm($form,array(),array('submit'=>'AP_SAVE_CONTINUE'));

        if ($recordsData) {
            $form->setData($recordsData);

            if ($doctor && $time) {
                $form->get('buttons')->add(array(
                    'type' => 'Zend\Form\Element\Button',
                    'name' => 'skip',
                    'attributes' => array(
                        'type' => 'button',
                        'value' => 'My info is complete and up to date',
                        'class' => 'btn btn-default pull-right flip',
                        'id' => 'skip',
                        'data-href' => url('app/health-center/reserve', array('doctor' => $doctor, 'time' => $time), array('query' => array('skip-records' => true)))
                    ),
                    'options' => array(
                        'label' => 'My info is complete and up to date',
                        'glyphicon' => 'ok text-success',
                    )
                ));
            }
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
                        $fieldsApi->save('medical_records', $userId, $data);
                        $this->flashMessenger()->addSuccessMessage('Your medical records updated successfully');
                    } else
                        $this->flashMessenger()->addErrorMessage('Medical records requires Fields module to work');

                    if ($doctor !== false && $time)
                        return $this->forward()->dispatch('HealthCenter\Controller\HealthCenter',
                            array('action' => 'reserve', 'doctor' => $doctor, 'time' => $time, 'skip-records' => true));
                } else {
                    $this->formHasErrors();
                }
            }
        }

        $this->viewModel->setVariables(array('form' => $form));
        $this->viewModel->setTemplate('health-center/patient/edit-profile');
        return $this->viewModel;
    }

    public function profileAction()
    {
        $currentUserId = current_user()->id;
        $userId = $this->params()->fromRoute('patient', false);
        if (!$userId)
            $userId = $currentUserId;

        $viewVars = array();

        if ($userId == $currentUserId) {
            $viewVars['lastSession'] = $this->getReservationTable()->getMyLastSession();
            $viewVars['nextSession'] = $this->getReservationTable()->getMyNextSession();
        }

        $patient_profile = array();
        $fieldsApi = null;
        if ($this->hasFieldsApi()) {
            $fieldsApi = $this->getFieldsApi();
            $fieldsApi->init('medical_records');
            $fields = $this->getFieldsTable()->getByEntityType('medical_records')->toArray();
            $data = $fieldsApi->getFieldData($userId);
            $patient_profile = $fieldsApi->generate($fields, $data);
        }

        $viewVars['userId'] = $userId;
        $viewVars['patient_profile'] = $patient_profile;
        $viewVars['isMyDoctor'] = $this->getDoctorRefTable()->isMyDoctor(current_user()->id, $userId);

        $this->viewModel->setVariables($viewVars);
        $this->viewModel->setTemplate('health-center/patient/profile');
        return $this->viewModel;
    }

    public function panelAction()
    {
        return $this->profileAction();
    }

    public function myReservationsAction()
    {
        $currentUserId = current_user()->id;
        $userId = $this->params()->fromRoute('id', false);
        if (!$userId)
            $userId = $currentUserId;
    }
    //endregion

    //region Private Methods
    /**
     * @return UserTable
     */
    private function getUserTable()
    {
        return getSM('user_table');
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
    //endregion
} 