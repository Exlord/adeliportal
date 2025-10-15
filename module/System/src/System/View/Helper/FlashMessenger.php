<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/9/13
 * Time: 1:51 PM
 */
namespace System\View\Helper;


use Zend\Escaper\Escaper;
use Zend\View\Helper\EscapeHtml;

class FlashMessenger extends \Zend\View\Helper\FlashMessenger
{

    /**
     * Templates for the open/close/separators for message tags
     *
     * @var string
     */
    protected $messageCloseString = '</li></ul></div></div>';
    protected $messageOpenFormat = '<div%s><div class="message-content"><ul><li>';
    protected $messageSeparatorString = '</li><li>';

    public function render()
    {
        $markup = '';

//        $markup .= parent::renderCurrent('success', array('alert', 'alert-success'));
//        $markup .= parent::renderCurrent('info', array('alert', 'alert-info'));
//        $markup .= parent::renderCurrent('warning', array('alert', 'alert-warning'));
//        $markup .= parent::renderCurrent('error', array('alert', 'alert-danger'));
//        $markup .= parent::renderCurrent('default', array('alert', 'alert-default'));
//
//        $this->getPluginFlashMessenger()->clearCurrentMessagesFromContainer();

        $markup .= parent::render('success', array('alert', 'alert-success'));
        $markup .= parent::render('info', array('alert', 'alert-info'));
        $markup .= parent::render('warning', array('alert', 'alert-warning'));
        $markup .= parent::render('error', array('alert', 'alert-danger'));
        $markup .= parent::render('default', array('alert', 'alert-default'));

        return  $markup;
    }
}