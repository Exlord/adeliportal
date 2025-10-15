<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Company: AzarIPT
 * Date: 5/6/12
 * Time: 10:35 AM
 */
namespace System\View\Helper;

use Zend\View\Renderer\RendererInterface as Renderer;

class General extends \Zend\View\Helper\AbstractHelper
{
    const ERROR = 'error';
    const MESSAGE = 'msg';
    const SUCCESS = 'success';
    const WARNING = 'warning';

    public function general()
    {
        return $this;
    }

    public function ParseOutputResultMsg($result)
    {
        $output = '<ul class="msg_stack">';
        foreach ($result as $msg) {
            if (isset($msg['Exception']))
                $output .= '<li class="error">' . $msg['Exception'] . '</li>';
            if (isset($msg['done']))
                $output .= '<li class="done">' . $msg['done'] . '</li>';
            if (isset($msg['notice']))
                $output .= '<li class="notice">' . $msg['notice'] . '</li>';
        }
        $output .= '</ul>';
        return $output;
    }

    private function _showError($msg)
    {
        return "<div class='message error'><div class='message-content'>{$msg}</div></div>";
    }

    private function _highLightMSG($msg)
    {
        return "<div class='message info'><div class='message-content'>{$msg}</div></div>";
    }

    private function _successMSG($msg)
    {
        return "<div class='message success'><div class='message-content'>{$msg}</div></div>";
    }

    private function _warningMSG($msg)
    {
        return "<div class='message warning'><div class='message-content'>{$msg}</div></div>";
    }

    public function _msg($text, $type = self::MESSAGE, $wrapper = 'div')
    {
        if (isset($text) && !empty($text)) {
            $out_put = '';
            if ($wrapper == 'div')
                $out_put = "<div>";

            switch ($type) {
                case self::MESSAGE:
                case 'info':
                    $out_put .= $this->_highLightMSG($text);
                    break;
                case self::ERROR:
                    $out_put .= $this->_showError($text);
                    break;
                case self::SUCCESS:
                    $out_put .= $this->_successMSG($text);
                    break;
                case self::WARNING:
                    $out_put .= $this->_warningMSG($text);
                    break;
            }


            if ($wrapper == 'div')
                $out_put .= "</div>";

            return $out_put;
        }
        return '';
    }

    public function MSG($text)
    {
        return $this->_msg($text);
    }

    public function ERROR($text)
    {
        return $this->_msg($text, self::ERROR);
    }

    public function SUCCESS($text)
    {
        return $this->_msg($text, self::SUCCESS);
    }

    public function WARNING($text)
    {
        return $this->_msg($text, self::WARNING);
    }


    public function ToFarsiNumber($int)
    {
        $num0 = "۰";
        $num1 = "۱";
        $num2 = "۲";
        $num3 = "۳";
        $num4 = "۴";
        $num5 = "۵";
        $num6 = "۶";
        $num7 = "۷";
        $num8 = "۸";
        $num9 = "۹";

        $stringtemp = "";
        $len = strlen($int);
        for ($sub = 0; $sub < $len; $sub++) {
            if (substr($int, $sub, 1) == "0") $stringtemp .= $num0;
            elseif (substr($int, $sub, 1) == "1") $stringtemp .= $num1; elseif (substr($int, $sub, 1) == "2") $stringtemp .= $num2; elseif (substr($int, $sub, 1) == "3") $stringtemp .= $num3; elseif (substr($int, $sub, 1) == "4") $stringtemp .= $num4; elseif (substr($int, $sub, 1) == "5") $stringtemp .= $num5; elseif (substr($int, $sub, 1) == "6") $stringtemp .= $num6; elseif (substr($int, $sub, 1) == "7") $stringtemp .= $num7; elseif (substr($int, $sub, 1) == "8") $stringtemp .= $num8; elseif (substr($int, $sub, 1) == "9") $stringtemp .= $num9; else $stringtemp .= substr($int, $sub, 1);

        }

        return $stringtemp;
    }

    public function short_text($text, $size = 100)
    {
        $text = strip_tags($text);
        return mb_substr($text, 0, $size, 'UTF-8');
    }


    public function noOutPut()
    {
        return $this->MSG(t("No result Found !"));
    }
}
