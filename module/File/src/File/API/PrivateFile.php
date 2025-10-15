<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 11/8/2014
 * Time: 11:59 AM
 */

namespace File\API;


use System\API\BaseAPI;

class PrivateFile extends BaseAPI
{
    private static $_uploadValidators = null;

    public static function getUploadValidators()
    {
        if (is_null(self::$_uploadValidators)) {
            $config = getSM('Config');
            $config = $config['private_file'];
            $appConfig = getSM('ApplicationConfig');
            if (isset($appConfig['private_file'])) {
                $appConfig = $appConfig['private_file'];
                if (isset($appConfig['max_upload_size']))
                    $config['max_upload_size'] = $appConfig['max_upload_size'];

                if (isset($appConfig['extensions']))
                    $config['extensions'] = $appConfig['extensions'];

                if (isset($appConfig['mime_types']))
                    $config['mime_types'] = $appConfig['mime_types'];
            }
            self::$_uploadValidators = $config;
        }

        return self::$_uploadValidators;
    }

    /**
     * @param array $file
     * @param string $destination_folder folder name including a preceding /
     * @return string
     */
    public static function MoveUploadedFile($file, $destination_folder)
    {
        $ext = getFileExt($file['name']);
        $name = md5(uniqid(mt_rand(), true));

        if (!is_dir(PRIVATE_FILE . $destination_folder))
            mkdir(PRIVATE_FILE . $destination_folder, 0755, true);

        $file_name = PRIVATE_FILE . $destination_folder . '/' . $name . '.' . $ext;
        rename($file['tmp_name'], $file_name);
        chmod($file_name, 0644);
        $file_name = self::getUploadedFilePath($file_name);
        return $file_name;
    }

    private static function getUploadedFilePath($temp)
    {
        $file_path = substr($temp, strlen(PRIVATE_FILE));
        $file_path = str_replace('\\', '/', $file_path);
        return $file_path;
    }

    public static function SetUsage($files, $entityType, $entityId)
    {
        getSM('private_file_usage')->saveAll($entityType, $entityId, $files);
    }

    public static function GetUsedFiles($entityType, $entityId)
    {
        return getSM('private_file_usage')->getFiles($entityType, $entityId);
    }

    public static function HasAccess($fileIdOrFileModel)
    {
        $model = null;
        if (is_object($fileIdOrFileModel))
            $fileIdOrFileModel = (array)$fileIdOrFileModel;

        if (is_array($fileIdOrFileModel))
            $model = $fileIdOrFileModel;

        if ($model === null) {
            $model = getSM('private_file_table')->get($fileIdOrFileModel);
        }

        if ($model) {
            $model = (array)$model;
            $roles = array();
            foreach (current_user()->roles as $r)
                $roles[] = $r['id'];

            if (count(array_intersect($roles, $model['accessibility'])) > 0)
                return true;
        }

        return false;
    }
} 