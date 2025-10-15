<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace OnlineOrders\Form;

use System\Form\BaseForm;
use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\Filter;

class PerDomains extends BaseForm
{


    public function __construct()
    {

        parent::__construct('perDomains_form');
        $this->setAttributes(array(
            'class', 'normal-form',
            'action'=>url('admin/online-orders/per-domains')
        ));
    }

    protected function addElements()
    {

        $this->add(array(
            'name' => 'ID',
            'type' => 'Zend\Form\Element\Hidden',
        ));

        $this->add(array(
            'name' => 'domainName',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Domain Name'
            ),
        ));


        $this->add(array(
            'name' => 'domainPrice',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Price'
            ),
        ));


        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'domainStatus',
            'options' => array(
                'label' => 'Do you want to be displayed on the site?',
            ),
            'attributes' => array(
                'checked' => 'checked'
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'domainSell',
            'options' => array(
                'label' => 'Sold out?',
            ),
        ));


        $this->add(array(
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 'csrf_domains_form'
        ));

        $this->add(array(
            'type' => 'submit',
            'name' => 'submit-create',
            'attributes' => array(
                'value' => 'Save',
                'class' => 'button',
            )
        ));
        $this->add(array(
            'type' => 'submit',
            'name' => 'submit-edit',
            'attributes' => array(
                'value' => 'Edit',
                'class' => 'button',
            )
        ));
        $this->add(array(
            'type' => 'submit',
            'name' => 'submit-delete',
            'attributes' => array(
                'value' => 'Delete',
                'class' => 'button',
            )
        ));


    }


    protected function addInputFilters()
    {

    }

}
