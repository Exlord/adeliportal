<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 6/11/14
 * Time: 9:41 AM
 */

namespace System\Filter\Word;


use Zend\Filter\Word\SeparatorToSeparator;

class SpaceToUnderscore extends SeparatorToSeparator
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(' ', '_');
    }
} 