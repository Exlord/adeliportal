<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 2/20/14
 * Time: 9:14 AM
 */

namespace System\Captcha;


use Application\API\App;
use Zend\Captcha\Image;
use Zend\Form\Element\Captcha;

class CaptchaFactory
{
    private $config = null;
    private $allowedTypes = array(
        'math', 'image'
    );
    private $type = 'math';
    private $name;

    /**
     * @param string $name
     * @return Captcha
     */
    public static function create($name = 'captcha')
    {
        $ins = new static($name);
        return $ins->get();
    }

    /**
     * @param $name
     */
    public function __construct($name)
    {
        $this->name = $name;

        if (is_null($this->config))
            $this->config = getConfig('system_captcha')->varValue;

        if (isset($this->config['type'])) {
            $type = $this->config['type'];
            if (in_array($type, $this->allowedTypes))
                $this->type = $type;
        }
    }

    /**
     * @return Captcha
     */
    public function get()
    {
        $adapter = null;
        switch ($this->type) {
            case 'math':
                $adapter = new Math($this->config);
                break;
            case 'image':

                $imageConfig = array();
                if (isset($this->config['image']))
                    $imageConfig = $this->config['image'];

                if (isset($imageConfig['numbersOnly']) && $imageConfig['numbersOnly'] == '1')
                    $image = new NumberImage();
                else
                    $image = new Image();

                $image->setFont(ROOT . '/module/Application/public/fonts/arial.ttf');
                $image->setImgDir(PUBLIC_FILE . '/captcha');
                $image->setImgUrl(App::siteUrl() . '/clients/' . ACTIVE_SITE . '/files/captcha');

                if (isset($imageConfig['DotNoiseLevel']) && has_value($imageConfig['DotNoiseLevel']))
                    $image->setDotNoiseLevel((int)$imageConfig['DotNoiseLevel']);

                if (isset($imageConfig['Wordlen']) && has_value($imageConfig['Wordlen']))
                    $image->setWordlen((int)$imageConfig['Wordlen']);

                if (isset($imageConfig['FontSize']) && has_value($imageConfig['FontSize']))
                    $image->setFontSize((int)$imageConfig['FontSize']);

                if (isset($imageConfig['Width']) && has_value($imageConfig['Width']))
                    $image->setWidth((int)$imageConfig['Width']);

                if (isset($imageConfig['Height']) && has_value($imageConfig['Height']))
                    $image->setHeight((int)$imageConfig['Height']);

                if (isset($imageConfig['lineNoiseLevel']) && has_value($imageConfig['lineNoiseLevel']))
                    $image->setLineNoiseLevel((int)$imageConfig['lineNoiseLevel']);

                $adapter = $image;
                break;
        }

        $captcha = new Captcha($this->name);
        $captcha->setCaptcha($adapter);
        $captcha->setAttribute('class', 'captcha');
        $captcha->setAttribute('placeholder', t('Captcha'));

        return $captcha;
    }
} 