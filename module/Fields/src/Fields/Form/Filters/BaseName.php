<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/20/13
 * Time: 9:22 AM
 */

namespace Fields\Form\Filters;

class BaseName extends BaseFilter
{
    protected $label = 'BaseName';
    protected $attributes = array(
        'id' => 'filter_BaseName',
        'name' => 'Zend\Filter\BaseName'
    );

    public function __construct()
    {
        parent::__construct();
        $this->setDescription('allows you to filter a string which contains the path to a file and it will return the base name of this file.');
    }
} 