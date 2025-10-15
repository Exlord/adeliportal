<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 10/20/13
 * Time: 2:46 PM
 */

namespace Application\View\Helper;


use System\View\Helper\BaseHelper;

class Head extends BaseHelper
{
    public function __invoke($viewport = true)
    {
        $favIconUrl = false;
        $description = 'پورتال سازماني شرکت ايده پرداز';
        $keywords = 'پورتال سازماني,پرتال,طراحي سايت,ipt,ايده پرداز تبريز,طراحي پورتال اينترنتي,ipt24,IPT Portal';
        $browserTitle = 'IPT System';
        $author = 'IPT تيم طراحي پرتال سازماني';

        $config = getSM('config_table')->getByVarName('system_config')->varValue;
        if (isset($config['favIconUrl']) && $config['favIconUrl'])
            $favIconUrl = $config['favIconUrl'];
        if (isset($config['description']) && $config['description'])
            $description = $config['description'];
        if (isset($config['keywords']) && $config['keywords'])
            $keywords .= $config['keywords'];
        if (isset($config['browserTitle']) && !empty($config['browserTitle']))
            $browserTitle = $config['browserTitle'];

        $this->view->headTitle()->setPrefix(t($browserTitle));

        if ($favIconUrl) {
            $this->view->headLink(array(
                    'rel' => 'icon',
                    'href' => $favIconUrl,
                    'type' => 'image/gif',
                ),
                'PREPEND');
        }
        $this->view->headMeta()
            ->setCharset('UTF-8');
        if ($viewport)
            $this->view->headMeta()->appendName('viewport', 'width=device-width, initial-scale=1.0');
        $this->view->headMeta()
            ->appendName('description', $description)
            ->appendName('keywords', $keywords)
            ->appendName('author', $author)
            ->appendName('Robots', 'index,follow')
            ->appendName('Revisit-After', '1 days')
            ->appendName('Design', 'http://www.IPT24.com');
    }
}