<?php

namespace NewsLetter\API;

use SiteMap\Model\UrlSet;
use System\API\BaseAPI;
use Zend\Db\Adapter\Adapter;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Input;

class NewsLetter extends BaseAPI
{
    const GET_INFO_NEWSLETTER = 'NewsLetterGetInfo';

    public function getInfo($data)
    {
        $this->getEventManager()->trigger(self::GET_INFO_NEWSLETTER, $this,
            array(
                'data' => &$data,
            )
        );
    }

}