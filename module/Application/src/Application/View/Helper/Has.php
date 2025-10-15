<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 2/6/14
 * Time: 12:38 PM
 */

namespace Application\View\Helper;


use System\View\Helper\BaseHelper;

class Has extends BaseHelper
{
    public function __invoke($helper)
    {
        return (boolean)$this->getServiceLocator()->has($helper);
    }
} 