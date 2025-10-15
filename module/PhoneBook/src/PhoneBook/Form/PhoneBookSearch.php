<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace PhoneBook\Form;

use System\Form\BaseForm;
use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\Filter;

class PhoneBookSearch extends BaseForm
{
    public function __construct()
    {
        parent::__construct('phone-book-search-form');
        $this->setAttribute('class', 'normal-form');
    }

    protected function addElements()
    {


        $this->add(array(
            'name' => 'ID',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Home Id : '
            ),
        ));

        $this->add(array(
            'name' => 'nameAndFamily',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'name And Family : '
            ),
        ));


        $this->add(array(
            'name' => 'email',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'email : '
            ),
        ));


        $this->add(array(
            'name' => 'phone',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'phone : '
            ),
        ));


        $this->add(array(
            'type' => 'submit',
            'name' => 'submit-search',
            'attributes' => array(
                'value' => 'Search',
                'class' => 'button',
            )
        ));


    }


    protected function addInputFilters()
    {

    }

}
