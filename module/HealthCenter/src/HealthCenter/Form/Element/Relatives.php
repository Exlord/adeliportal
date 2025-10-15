<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 8/30/14
 * Time: 11:11 AM
 */

namespace HealthCenter\Form\Element;


use Zend\Form\Fieldset;
use Zend\InputFilter\InputProviderInterface;

class Relatives extends Fieldset implements InputProviderInterface
{
    public function __construct()
    {
        parent::__construct('relative');
        $this->attributes['class'] = 'collection-item col-md-4 col-sm-6 well wel-sm';

        $this->add(array(
            'type' => 'Select',
            'name' => 'relation',
            'options' => array(
                'label' => 'Relation',
                'value_options' => array(
                    'Spouse' => 'Spouse',
                    'Mother' => 'Mother',
                    'Father' => 'Father',
                    'Sister' => 'Sister',
                    'Brother' => 'Brother',
                    'Child' => 'Child'
                )
            ),
            'attributes' => array()
        ));

        $this->add(array(
            'type' => 'Text',
            'name' => 'age',
            'options' => array(
                'label' => 'Age'
            ),
            'attributes' => array()
        ));

        $this->add(array(
            'type' => 'Text',
            'name' => 'education',
            'options' => array(
                'label' => 'Education'
            ),
            'attributes' => array()
        ));

        $this->add(array(
            'type' => 'Text',
            'name' => 'job',
            'options' => array(
                'label' => 'Job'
            ),
            'attributes' => array()
        ));

        $this->add(array(
            'name' => 'drop_collection_item',
            'options' => array(
                'label' => '',
                'description' => '',
            ),
            'attributes' => array(
                'type' => 'button',
                'value' => t('Delete This Item'),
                'title' => t('Delete This Item'),
                'class' => 'btn btn-default drop_collection_item',
            ),
        ));
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInput()}.
     *
     * @return array
     */
    public function getInputSpecification()
    {
        return array();
    }
}