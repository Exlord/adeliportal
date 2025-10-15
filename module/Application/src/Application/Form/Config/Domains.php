<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 4/20/14
 * Time: 12:59 PM
 */

namespace Application\Form\Config;


use Zend\Form\Fieldset;

class Domains extends Fieldset
{
    public function __construct($domains)
    {
        parent::__construct('domains');
        $this->setLabel('Domains');

        foreach ($domains as $id => $name) {
            $this->add(new Domain($name));
        }
    }
} 