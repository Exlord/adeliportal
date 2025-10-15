<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 10/30/13
 * Time: 10:32 AM
 */

namespace Thumbnail\API;


use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;

class Thumbnail
{
    /**
     * @var Imagine
     */
    public $imagine;
    public static $ResizeGif = false;

    public function __construct()
    {
        ini_set('memory_limit', '512M');
        //if (extension_loaded('imagick'))
        $this->imagine = new Imagine();

    }

    private function getThumbName($file, $width, $height)
    {
        $thumb_path = $file;
        $thumb_path = str_replace('/files/', '/files/thumbs/', $thumb_path);
        $thumb_name = getFileName($thumb_path);
        $thumb_path = str_replace($thumb_name, '', $thumb_path);
        if (!is_dir(PUBLIC_PATH . $thumb_path))
            mkdir(PUBLIC_PATH . $thumb_path, 0755, true);
        $thumb_ext = getFileExt($thumb_name);
        $thumb_name = str_replace('.' . $thumb_ext, '', $thumb_name);
        $thumb_path = $thumb_path . $thumb_name . '_' . $width . '_' . $height . '.' . $thumb_ext;
        return $thumb_path;
    }

    /**
     * Resize the image to this exact dimensions
     * @param $file
     * @param $width
     * @param $height
     * @param string $filter
     * @return mixed|string
     * @throws \Exception
     */
    public function resize($file, $width, $height, $saveOptions = array(), $filter = ImageInterface::FILTER_UNDEFINED)
    {
        $ext = getFileExt(PUBLIC_PATH . $file);
        if (strtolower($ext) == 'swf') //no resizing for gif and swf for now
            return $file;

        if (strtolower($ext) == 'gif' && !self::$ResizeGif) //no resizing for gif and swf for now
            return $file;

        if (!in_array(strtolower($ext), array('gif', 'jpeg', 'png', 'jpg', 'bmp')))
            return $file;

        $thumb_name = $file;

        try {//check if the original file exist
            if (file_exists(PUBLIC_PATH . $file)) {
                $thumb_name = $this->getThumbName($file, $width, $height);
                //check if the thumbnail file has not been created before
                if (!file_exists(PUBLIC_PATH . $thumb_name)) {
                    $image = $this->imagine->open(PUBLIC_PATH . $file);
                    $image->resize(New Box($width, $height), $filter, $saveOptions)
                        ->save(PUBLIC_PATH . $thumb_name);
                    $image = null;
                }
            }
        } catch (\Exception $e) {
            db_log_error(___exception_trace($e));
        }
        return $thumb_name;
    }

    /**
     * Resizing with ratio intact
     * @param $file
     * @param $width
     * @param $height
     * @param string $mode
     * @param string $filter
     * @return mixed|string
     * @throws \Exception
     */
    public function thumbnail($file, $width, $height, $saveOptions = array(), $mode = ImageInterface::THUMBNAIL_INSET, $filter = ImageInterface::FILTER_UNDEFINED)
    {
//        var_dump("<pre>".$file."</pre>");
        $ext = getFileExt(PUBLIC_PATH . $file);
        if (in_array(strtolower($ext), array('gif', 'swf'))) //no resizing for gif and swf for now
            return $file;

        if (!in_array(strtolower($ext), array('gif', 'jpeg', 'png', 'jpg', 'bmp')))
            return $file;

        $thumb_name = $file;
        //check if the original file exist
        try {
            if (file_exists(PUBLIC_PATH . $file)) {
                $thumb_name = $this->getThumbName($file, $width, $height);
                //check if the thumbnail file has not been created before
                if (!file_exists(PUBLIC_PATH . $thumb_name)) {
                    $image = $this->imagine->open(PUBLIC_PATH . $file);
                    $image->thumbnail(New Box($width, $height), $mode, $filter)
                        ->save(PUBLIC_PATH . $thumb_name, $saveOptions);
                    $image = null;
                }
            }
        } catch (\Exception $e) {
            db_log_error(___exception_trace($e));
        }
        return $thumb_name;
    }


    /**
     * @param $text : create $text to png file
     * @return string : return url
     */
    public function createImage($text)
    {
        $file_name = base64_encode($text);
        $file_root_path = '/clients/' . ACTIVE_SITE . '/files/createImage/' . $file_name . '.png';
        $thumb_path = PUBLIC_FILE . '/createImage';
        $file_path = $thumb_path . '/' . $file_name . '.png';
        if (!is_dir($thumb_path))
            mkdir($thumb_path, 0755, true);
        if (!file_exists($file_path)) {
            $length = strlen($text) * 10;
            $image = imagecreatetruecolor($length, 30);
            $white = imagecolorallocate($image, 255, 255, 255);
            imagefilledrectangle($image, 0, 0, 399, 99, $white);
            $color = imagecolorallocate($image, 0, 0, 0);
            $fontUrl = ROOT . '/module/Application/public/fonts/tahoma.ttf';
            imagettftext($image, 11, 0, 5, 20, $color, $fontUrl, $text);
            imagepng($image, $file_path);
            return $file_root_path;
        } else
            return $file_root_path;
    }
} 