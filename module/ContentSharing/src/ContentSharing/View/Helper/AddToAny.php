<?php
namespace ContentSharing\View\Helper;


class AddToAny extends \Zend\View\Helper\AbstractHelper
{
    public function __invoke()
    {
        $html ='';
        $config = getSM('config_table')->getByVarName('content_sharing_config')->varValue;
        $visibleStatus = 0; // default hide
        if (isset($config['visibleStatus']) && $config['visibleStatus'])
            $visibleStatus = $config['visibleStatus'];
        if ($visibleStatus)
            $html = $this->view->render('content-sharing/content-sharing/add-to-any', array());
        return $html;
    }

}