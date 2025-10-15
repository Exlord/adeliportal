<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/20/13
 * Time: 9:22 AM
 */

namespace Fields\Form\Filters;

class HtmlEntities extends BaseFilter
{
    protected $label = 'HtmlEntities';
    protected $attributes = array(
        'id' => 'filter_HtmlEntities',
        'name' => 'Zend\Filter\HtmlEntities'
    );

    public function __construct()
    {
        parent::__construct();
        $this->setDescription('Returns the string $value, converting characters to their corresponding HTML entity equivalents where they exist.');

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'quotestyle',
            'options' => array(
                'label' => 'Quotestyle',
                'description' => 'Equivalent to the PHP htmlentities native function parameter quote_style. This allows you to define what will be done with ‘single’ and “double” quotes.The following constants are accepted: ' . ENT_COMPAT . ', ' . ENT_QUOTES . ' ' . ENT_NOQUOTES . ' with the default being ' . ENT_COMPAT . '.',
                'value_options' => array(
                    ENT_COMPAT => ENT_COMPAT,
                    ENT_QUOTES => ENT_QUOTES,
                    ENT_NOQUOTES => ENT_NOQUOTES
                )
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'charset',
            'options' => array(
                'label' => 'Charset',
                'description' => 'Equivalent to the PHP htmlentities native function parameter charset. This defines the character set to be used in filtering. Unlike the PHP native function the default is ‘UTF-8’'
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'doublequote',
            'options' => array(
                'label' => 'Double Quote',
                'description' => 'Equivalent to the PHP htmlentities native function parameter double_encode. If set to false existing html entities will not be encoded. The default is to convert everything (true).'
            )
        ));
    }
} 