<?php
namespace System\IO;
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 9:50 AM
 */
class File
{
    public static function getSize($file)
    {
        if (file_exists($file)) {
            $size = filesize($file);
            if ($size < 0)
                if (!(strtoupper(substr(PHP_OS, 0, 3)) == 'WIN'))
                    $size = trim(`stat -c%s $file`);
                else {
                    $fsobj = new COM("Scripting.FileSystemObject");
                    $f = $fsobj->GetFile($file);
                    $size = $file->Size;
                }
            return $size;
        }
        return 0;
    }

    public static function FormatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' ' . t('GB');
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' ' . t('MB');
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' ' . t('KB');
        } elseif ($bytes > 1) {
            return $bytes . ' ' . t('bytes');
        } elseif ($bytes == 1) {
            return '1 ' . t('byte');
        } else {
            return '0 ' . t('bytes');
        }
    }
}
