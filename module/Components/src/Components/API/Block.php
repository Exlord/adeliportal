<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 4/13/13
 * Time: 3:21 PM
 */

namespace Components\API;

use System\API\BaseAPI;
use Zend\Db\Adapter\Adapter;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Input;

class Block extends BaseAPI
{
    const LOAD_BLOCK_CONFIGS = 'loading_block_configs';
    private $blockTypes = null;

    public function LoadBlockConfigs($form, $type)
    {
        $this->getEventManager()->trigger(self::LOAD_BLOCK_CONFIGS, $this, array('form' => $form, 'type' => $type));
    }

    /**
     * Get blocks info from config
     * @param $type
     * @return bool
     */
    public static function getBlockInfo($type)
    {
        $components = getSM('block_api')->LoadBlockTypes();
        if (isset($components[$type]))
            return $components[$type];
        return false;
    }

    /**
     * @param $data : all data array
     * @return mixed
     */
    public static function create($data)
    {
        $modelBlock = new \Components\Model\Block();
        if (isset($data['id']))
            $modelBlock->id = $data['id'];
        if (isset($data['title']))
            $modelBlock->title = $data['title'];
        if (isset($data['description']))
            $modelBlock->description = $data['description'];
        if (isset($data['type']))
            $modelBlock->type = $data['type'];
        if (isset($data['position']))
            $modelBlock->position = $data['position'];
        if (isset($data['visibility']))
            $modelBlock->visibility = $data['visibility'];
        if (isset($data['pages']))
            $modelBlock->pages = $data['pages'];
        if (isset($data['enabled']))
            $modelBlock->enabled = $data['enabled'];
        if (isset($data['data']))
            $modelBlock->data = $data['data'];
        if (isset($data['locked']))
            $modelBlock->locked = $data['locked'];
        return getSM('block_table')->save($modelBlock);
    }

    public function LoadBlockTypes()
    {
        if ($this->blockTypes == null) {
            $config = getSM('Config');
            $this->blockTypes = $config['components'];
            $this->getEventManager()->trigger('Components.Block.Types.Load', $this);
        }
        return $this->blockTypes;
    }

    public function AddBlockType($name, $label, $helper, $description = '')
    {
        $this->blockTypes[$name] = array(
            'label' => $label,
            'description' => $description,
            'helper' => $helper,
        );
    }
}