<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/20/13
 * Time: 9:22 AM
 */

namespace Fields\Form\Filters;

class Int extends BaseFilter
{
    protected $label = 'Int';
    protected $attributes = array(
        'id' => 'filter_Int',
        'name' => 'Zend\Filter\Int'
    );

    public function __construct()
    {
        parent::__construct();
        $this->setDescription(' allows you to transform a scalar value which contains into an integer.');
    }
} 