<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/20/13
 * Time: 9:22 AM
 */

namespace Fields\Form\Filters;

class StripNewLines extends BaseFilter
{
    protected $label = 'StripNewLines';
    protected $attributes = array(
        'id' => 'filter_StripNewLines',
        'name' => 'Zend\Filter\StripNewLines'
    );

    public function __construct()
    {
        parent::__construct();
        $this->setDescription('This filter modifies a given string and removes all new line characters within that string.');
    }
} 