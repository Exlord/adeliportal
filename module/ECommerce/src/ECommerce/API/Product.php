<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/12/14
 * Time: 1:26 PM
 */

namespace ECommerce\API;


use System\API\BaseAPI;

class Product extends BaseAPI
{
    const EVENT_LOAD_EXTRA_TYPES = 'load_extra_types';
    const EVENT_LOAD_EXTRA_TYPES_CONFIGS = 'load_extra_types_configs';

    const TYPE_PHYSICAL = 'physical_item';
    const TYPE_FILE = 'downloadable_file';
    const TYPE_CODE = 'text_code';

    /**
     * @var array Product types
     */
    public static $types = array(
        self::TYPE_PHYSICAL => 'Physical Item',
        self::TYPE_FILE => 'Downloadable File',
        self::TYPE_CODE => 'Text Code'
    );

    /**
     * if any module wants to add its item to the product type's ,it should handle this event
     */
    public function loadExtraTypes()
    {
        $this->getEventManager()->trigger(self::EVENT_LOAD_EXTRA_TYPES, $this);
    }

    public function loadExtraTypesConfigs($container)
    {
        $this->getEventManager()->trigger(self::EVENT_LOAD_EXTRA_TYPES_CONFIGS, $this, array('container' => &$container));
    }
} 