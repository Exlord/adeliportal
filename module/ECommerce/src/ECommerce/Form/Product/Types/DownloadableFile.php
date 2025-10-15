<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/14/14
 * Time: 10:52 AM
 */

namespace ECommerce\Form\Product\Types;


use ECommerce\API\Product;
use Zend\Form\Fieldset;

class DownloadableFile extends Fieldset
{
    public function __construct()
    {
        parent::__construct(Product::TYPE_FILE);
        $this->setLabel('Downloadable File');
        $this->setAttribute('class', 'no-border no-bg hidden');
        $this->setAttribute('id', 'product_type_'.Product::TYPE_FILE);
    }
} 