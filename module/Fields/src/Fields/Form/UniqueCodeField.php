<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/27/12
 * Time: 9:50 AM
 */

namespace Fields\Form;

use Zend\Form\Fieldset;

class UniqueCodeField extends BaseConfig
{
    public function __construct()
    {
        parent::__construct('uniqueCode_field');
        $this->setAttribute('id', 'uniqueCode_settings');

    }
}
