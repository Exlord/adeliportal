<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 4/13/13
 * Time: 3:21 PM
 */

namespace File\API;

use File\Form\FileCollection;
use File\Form\FileField;
use System\API\BaseAPI;
use Zend\Db\Adapter\Adapter;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Input;
use Zend\Validator\File\MimeType;
use Zend\InputFilter\FileInput;
use Zend\Validator;
use Zend\Filter;

class File extends BaseAPI
{
    const MIME_IMAGE_PNG = 'image/png';
    const MIME_IMAGE_JPG = 'image/jpg';
    const MIME_IMAGE_JPEG = 'image/jpeg';
    const MIME_IMAGE_GIF = 'image/gif';
    const MIME_IMAGE_TIFF = 'image/tiff';

    public $IMAGE_MIMES = array(
        self::MIME_IMAGE_JPG,
        self::MIME_IMAGE_PNG,
        self::MIME_IMAGE_GIF,
        self::MIME_IMAGE_JPEG,
        self::MIME_IMAGE_TIFF
    );

    /**
     * @var FileCollection
     */
    public $collectionItem;

    /**
     * @var FileField
     */
    public $targetElement;

    public $targetFolder;

    /**
     * @var \finfo
     */
    private static $finfo = null;

    /**
     * @param $id
     * @param $max_allowed
     * @param array $fileTypes
     * @param string $maxSize
     * @param int $init_count
     * @param array $removeElements
     * @param string $collectionLabel
     * @param string $itemLabel
     * @return File
     */
    public function addFileCollection(\Zend\Form\Form $form,
                                      $id,
                                      $max_allowed,
                                      $fileTypes = array(),
                                      $maxSize = '2MB',
                                      $init_count = 1,
                                      array $removeElements = array(),
                                      $collectionLabel = 'File',
                                      $itemLabel = 'File')
    {
        if ($init_count > $max_allowed)
            $init_count = $max_allowed;

        $should_create_template = true;
        $allow_add = true;

        $target = PUBLIC_FILE . '/temp';

        $this->targetElement = new FileField($fileTypes, $maxSize, $target);
        $this->targetElement->setLabel($itemLabel);
        if ($max_allowed == 1 || $max_allowed == $init_count) {
            $this->targetElement->remove('drop_collection_item');
            $should_create_template = false;
            $allow_add = false;
        }
        if (is_array($removeElements) && count($removeElements)) {
            foreach ($removeElements as $el) {
                $this->targetElement->remove($el);
            }
        }

        $this->collectionItem = new FileCollection($id, $init_count, $this->targetElement, $should_create_template, $allow_add);
        $this->collectionItem->setLabel($collectionLabel);

        $script = "var max_allowed_" . $id . " = " . $max_allowed . ";";
        $this->getServiceLocator()->get('viewhelpermanager')->get('headScript')->appendScript($script);
        $form->add($this->collectionItem);
//        $filters = $form->getInputFilter()->get($id);
        return $this;
    }

    public function processUpload($id, Form $form)
    {

    }

    public function save($entityType, $entityId, $files, $maxCountImages = 0)
    {
        $dataArray = array();
        $count = 0;
        if (!is_array($files))
            $files = array();
        $file_table = getSM()->get('file_table');

        //  for ($i = 0; $i < $maxCountImages; $i++) {
        foreach ($files as $key => $file) {
            if (!empty($file['tmp_name']) && ($count < $maxCountImages)) {
                $file_model = new \File\Model\File();
                $file_model->entityId = $entityId;
                $file_model->entityType = $entityType;
                if (isset($file['alt']))
                    $file_model->fAlt = $file['alt'];
                if (isset($file['title']))
                    $file_model->fTitle = $file['title'];
                if (isset($file['name']))
                    $file_model->fName = $file['name'];
                if (isset($file['fileType']))
                    $file_model->fileType = $file['fileType'];
                $file_model->fPath = self::MoveUploadedFile($file['tmp_name'], PUBLIC_FILE . '/' . $entityType, $file['name']);
                $file_table->save($file_model);
                $dataArray[] = $file_model->fPath;
                $count++;
            }
        }
        return $dataArray;
        //  }

    }

    /**
     * @param $temp_source
     * @param $destination_folder
     * @param $original_name
     * @param bool $keep_original_name
     * @return string
     */
    public static function MoveUploadedFile($temp_source, $destination_folder, $original_name, $keep_original_name = false)
    {
        $original_name = getFileName($original_name);
        $ext = getFileExt($original_name);
        $time = explode(' ', microtime());
        $time = substr($time[0], 2);
        $uid = uniqid();
        $name = $keep_original_name ? current(explode('.', $original_name)) : $uid . $time;

        if (!is_dir($destination_folder))
            mkdir($destination_folder, 0755, true);
        $file_name = $destination_folder . '/' . $name . '.' . $ext;
        rename($temp_source, $file_name);
        chmod($file_name, 0644);
        $file_name = self::getUploadedFilePath($file_name);
        return $file_name;
    }

    function getUploadedFilePath($temp)
    {
        $file_path = substr($temp, strlen(PUBLIC_PATH));
        $file_path = str_replace('\\', '/', $file_path);
        return $file_path;
    }

    /**
     * Checks if the given file is on image or not
     *
     * @param string $file the absolute path to the file
     * @return bool
     */
    public static function isImage($file)
    {
        if (is_file($file)) {
            $imageArray = array('image/jpeg; charset=binary', 'image/png; charset=binary', 'image/gif; charset=binary');
            if (is_null(self::$finfo))
                self::$finfo = new \finfo;

            return in_array(self::$finfo->file($file, FILEINFO_MIME), $imageArray);
        }
        return false;
    }
}