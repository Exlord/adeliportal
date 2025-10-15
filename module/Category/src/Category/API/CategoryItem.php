<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 4/13/13
 * Time: 3:21 PM
 */

namespace Category\API;

use SiteMap\Model\UrlSet;
use System\API\BaseAPI;
use Zend\Db\Adapter\Adapter;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Input;

class CategoryItem extends BaseAPI
{
    const URL_GENERATING = 'CategoryItemUrlGenerating';

    public function UrlGenerate($machineName, $data)
    {
        $this->getEventManager()->trigger(self::URL_GENERATING, $this,
            array(
                'machineName' => $machineName,
                'data' => &$data
            )
        );
    }
}