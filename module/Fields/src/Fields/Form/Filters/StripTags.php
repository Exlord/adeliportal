<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/20/13
 * Time: 9:22 AM
 */

namespace Fields\Form\Filters;

class StripTags extends BaseFilter
{
    protected $label = 'StripTags';
    protected $attributes = array(
        'id' => 'filter_StripTags',
        'name' => 'Zend\Filter\StripTags'
    );

    public function __construct()
    {
        parent::__construct();
        $this->setDescription('This filter can strip XML and HTML tags from given content.');

        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'allowTags',
            'options' => array(
                'label' => 'Allow Tags',
                'description' => 'This option sets the tags which are accepted. All other tags will be stripped from the given content.'
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'allowAttribs',
            'options' => array(
                'label' => 'Allow Attributes',
                'description' => 'This option sets the attributes which are accepted. All other attributes are stripped from the given content.'
            )
        ));
    }
} 