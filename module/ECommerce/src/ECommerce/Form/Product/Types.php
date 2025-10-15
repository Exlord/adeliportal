<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/14/14
 * Time: 10:40 AM
 */

namespace ECommerce\Form\Product;


use ECommerce\API\Product;
use ECommerce\Form\Product\Types\Code;
use ECommerce\Form\Product\Types\DownloadableFile;
use ECommerce\Form\Product\Types\PhysicalItem;
use Zend\Form\Fieldset;

class Types extends Fieldset
{
    public function __construct()
    {
        $this->setLabel('Product Type');
        parent::__construct('types');

        $this->add(array(
            'name' => 'type',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Type',
                'value_options' => Product::$types
            ),
            'attributes' => array()
        ));

        $this->add(new PhysicalItem());
        $this->add(new DownloadableFile());
        $this->add(new Code());

        getSM('product_api')->loadExtraTypesConfigs($this);
    }
} 