<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 10/20/13
 * Time: 2:46 PM
 */

namespace Application\View\Helper;


use System\View\Helper\BaseHelper;

class Test extends BaseHelper
{
    public $class = array();

    public function __invoke()
    {
        $this->view->headLink()->appendStylesheet($this->basePath() . '/css/menu.css');
    }
}