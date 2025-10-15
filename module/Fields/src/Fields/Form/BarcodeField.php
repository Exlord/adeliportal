<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/27/12
 * Time: 9:50 AM
 */

namespace Fields\Form;

use Zend\Form\Fieldset;

class BarcodeField extends BaseConfig
{
    public function __construct()
    {
        parent::__construct('barcode_field');
        $this->setAttribute('id', 'barcode_settings');
    }
}
