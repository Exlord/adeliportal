<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/20/13
 * Time: 9:22 AM
 */

namespace Fields\Form\Filters;

class PregReplace extends BaseFilter
{
    protected $label = 'PregReplace';
    protected $attributes = array(
        'id' => 'filter_PregReplace',
        'name' => 'Zend\Filter\PregReplace'
    );

    public function __construct()
    {
        parent::__construct();
        $this->setDescription('performs a search using regular expressions and replaces all found elements.');

        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'pattern',
            'options' => array(
                'label' => 'Pattern',
                'description' => 'The pattern which will be searched for.'
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'replacement',
            'options' => array(
                'label' => 'Replacement',
                'description' => 'The string which is used as replacement for the matches.'
            )
        ));
    }
} 