<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 3/10/14
 * Time: 10:39 AM
 */

namespace System\Mvc\Router\Http;


use Zend\Mvc\Router\Http\Segment;

class Optional extends Segment
{
    public function assemble(array $params = array(), array $options = array())
    {
        $patch = parent::assemble($params, $options);
        if ($patch == '/')
            return '';
        return $patch;
    }
} 