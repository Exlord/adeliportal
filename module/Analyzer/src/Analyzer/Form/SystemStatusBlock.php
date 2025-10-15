<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 2/24/14
 * Time: 9:30 AM
 */

namespace Analyzer\Form;


use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\InputFilter\InputProviderInterface;

class SystemStatusBlock extends Fieldset{
    public function __construct(){
        parent::__construct('system_status_block');
        $this->setOptions(array(
            'description' => 'select in what period of times you want to see the number of visitors'
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'total',
            'options' => array(
                'label' => 'Total',
                'description' => 'display total visitors count from the beginning of time'
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'report_periods',
            'options' => array(
                'label' => 'Report Periods',
                'count' => 0,
                'should_create_template' => true,
                'allow_add' => true,
                'allow_remove' => true,
                'target_element' => array(
                    'type' => 'Analyzer\Form\ReportPeriod'
                )
            ),
            'attributes' => array(
                'class' => 'collection-container'
            ),
        ));

        $this->add(array(
            'name' => 'add_more_select_option',
            'options' => array(
                'label' => '',
                'description' => '',
            ),
            'attributes' => array(
                'type' => 'button',
                'title' => t('Add More Select Options'),
                'value' => t('Add More'),
                'class' => 'button add_button add_collection_item',
            ),
        ));
    }
}