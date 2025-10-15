<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 10/13/13
 * Time: 10:28 AM
 */

namespace DataView\Lib;

class Checkbox extends Input
{
    protected $inputType = 'checkbox';

    /**
     * @param string $field The name of the column in database
     * @param string $title
     * @param array $options
     */
    public function __construct($field, $title, $options = array())
    {
        if (!isset($options['headerAttr']['width']))
            $options['headerAttr']['width'] = '25px';
        $options['attr']['align'] = 'center';
        parent::__construct($field, $title, $options);
        $this->hasTextFilter = false;
    }
}