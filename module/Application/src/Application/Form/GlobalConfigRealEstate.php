<?php
namespace Application\Form;

use Zend\Form\Fieldset;

class GlobalConfigRealEstate extends Fieldset
{
    public function __construct()
    {
        parent::__construct('realEstate');
        $this->setLabel('Real Estate');

        $this->add(array(
            'name' => 'new',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Count Of New',
                'description' => 'Count Of New',
            ),
            'attributes' => array(

            ),
        ));

        $this->add(array(
            'name' => 'image',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Count Of Image',
                'description' => 'Count Of Image',
            ),
            'attributes' => array(

            ),
        ));

        $this->add(array(
            'name' => 'info',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Type For Info',
                'description' => 'Type For Info',
            ),
            'attributes' => array(

            ),
        ));

        $this->add(array(
            'name' => 'search',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Type For Search',
                'description' => 'Type For Search',
            ),
            'attributes' => array(

            ),
        ));



    }
}
