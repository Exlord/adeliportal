<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ajami
 * Date: 10/20/13
 * Time: 2:49 PM
 */

namespace Note\View\Helper;

use \Application\View\Helper\Widget as BaseWidget;

class Widget extends BaseWidget
{
    protected $width = self::WIDTH_25;
    protected $style = self::STYLE_10;
    protected $id = 'note-widget';

   // public $cacheKey = 'gallery_widget';

    public function render()
    {
        $data = getSM('note_table')->getCounts();
        return $this->view->render('note/admin/widget', array('data' => $data));
    }
}