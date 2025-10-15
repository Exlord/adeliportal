<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/9/13
 * Time: 12:34 PM
 */

namespace Application\Form;


use System\Form\BaseForm;
use System\Form\Buttons;

class DbBackup extends BaseForm
{

    public function __construct()
    {
        $this->setAction(url('admin/backup/db/new'));
        $this->setAttribute('data-cancel', url('admin/backup/db'));
        parent::__construct('db_backup');
        $this->setAttribute('class', 'ajax_submit');
    }

    protected function addElements()
    {
        $this->add(array(
            'name' => 'comment',
            'type' => 'Zend\Form\Element\Textarea',
            'options' => array(
                'label' => 'Description',
            ),
            'attributes' => array(
                'cols' => 50,
                'rows' => 3
            )
        ));

        $this->add(new Buttons('db_backup'));
    }

    protected function addInputFilters()
    {
        // TODO: Implement addInputFilters() method.
    }
}