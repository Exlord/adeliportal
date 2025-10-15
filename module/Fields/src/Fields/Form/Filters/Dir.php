<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/20/13
 * Time: 9:22 AM
 */

namespace Fields\Form\Filters;

class Dir extends BaseFilter
{
    protected $label = 'Dir';
    protected $attributes = array(
        'id' => 'filter_Dir',
        'name' => 'Zend\Filter\Dir'
    );

    public function __construct()
    {
        parent::__construct();
        $this->setDescription('Given a string containing a path to a file, this function will return the name of the directory.');
    }
} 