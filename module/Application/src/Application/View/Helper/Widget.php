<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 10/20/13
 * Time: 2:46 PM
 */

namespace Application\View\Helper;


use System\View\Helper\BaseHelper;

abstract class Widget extends BaseHelper
{
    const WIDTH_25 = '25%';
    const WIDTH_50 = '50%';
    const WIDTH_75 = '75%';
    const WIDTH_100 = '100%';

    const STYLE_1 = 'widget-style-1';
//    const STYLE_2 = 'widget-style-2';
//    const STYLE_3 = 'widget-style-3';
    const STYLE_4 = 'widget-style-4';
//    const STYLE_5 = 'widget-style-5';
//    const STYLE_6 = 'widget-style-6';
//    const STYLE_7 = 'widget-style-7';
    const STYLE_8 = 'widget-style-8';
//    const STYLE_9 = 'widget-style-9';
    const STYLE_10 = 'widget-style-10';

    protected $styles = array(1, 4, 8, 10);

    protected $sizeClasses = array(
        self::WIDTH_25 => 'widget-25',
        self::WIDTH_50 => 'widget-50',
        self::WIDTH_75 => 'widget-75',
        self::WIDTH_100 => 'widget-100',
    );

    protected $width = self::WIDTH_100;
    protected $style;
    /**
     * How load before this widget needs to be reloaded with ajax . only if $static=true
     * @var int millisecond defaults to 5 minutes
     */
    protected $timeout = 300000;
//    protected $timeout = 300000;
    protected $id = null;

    public $cacheKey = null;

    final public function __invoke($width = null, $style = null)
    {
        if ($this->isAllowed()) {
            if ($width != null)
                $this->width = $width;
            if ($style != null)
                $this->style = $style;

            $class = $this->sizeClasses[$this->width];
            $style = isset($this->style) ? $this->style : 'widget-style-' . $this->styles[rand(0, count($this->styles) - 1)];
            $class .= ' ' . $style;
            if (!$content = $this->getCachedView()) {
                $content = $this->render();
            }
            return $this->view->render('application/admin/widget', array(
                'class' => $class,
                'content' => $content,
                'widgetId' => $this->id,
                'fqn' => get_called_class(),
                'timeout' => $this->timeout
            ));
        }
    }

    final public function getCachedView()
    {
        $cacheKey = $this->cacheKey;
        if ($cacheKey) {
            if ($view = getCacheItem($cacheKey)) {
                return $view;
            }
        }
        return false;
    }

    public function render()
    {
        return "<div class='loading'>&nbsp;</div>";
    }

    /**
     * Override this method in child classes to provide custom permission
     * @return bool
     */
    public function isAllowed()
    {
        return true;
    }
}