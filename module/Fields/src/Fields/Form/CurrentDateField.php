<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/27/12
 * Time: 9:50 AM
 */

namespace Fields\Form;

use Zend\Form\Fieldset;

class CurrentDateField extends BaseConfig
{
    public function __construct()
    {
        parent::__construct('currentDate_field');
        $this->setAttribute('id', 'currentDate_settings');
    }
}
