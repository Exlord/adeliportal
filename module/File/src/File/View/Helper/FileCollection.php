<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 5/12/13
 * Time: 11:07 AM
 */

namespace File\View\Helper;

use System\View\Helper\BaseHelper;

class FileCollection extends BaseHelper
{
    public function __invoke($el)
    {
        return $this->getView()->render('file/file/file-collection', array('el' => $el));
    }
}