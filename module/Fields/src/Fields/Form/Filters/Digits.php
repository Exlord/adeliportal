<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/20/13
 * Time: 9:22 AM
 */

namespace Fields\Form\Filters;

class Digits extends BaseFilter
{
    protected $label = 'Digits';
    protected $attributes = array(
        'id' => 'filter_Digits',
        'name' => 'Zend\Filter\Digits'
    );

    public function __construct()
    {
        parent::__construct();
        $this->setDescription('Returns the string $value, removing all but digits.');
    }
} 