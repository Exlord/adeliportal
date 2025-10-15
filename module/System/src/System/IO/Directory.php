<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 9:50 AM
 */
namespace System\IO;

class Directory
{

    /**
     * @param string $base
     * @param bool $fullPath
     * @param array $ignore
     * @return array|bool return false if no directory is find.or if provided path is not a directory or can not be read.
     */
    public static function getDirs($base, $fullPath = false, $ignore = array())
    {
        if (is_dir($base) && is_readable($base)) {
            $dirs = array();

            if ($handle = opendir($base)) {
                $base .= '/';
                while (false !== ($entry = readdir($handle))) {

                    if (
                        $entry != "." &&
                        $entry != ".." &&
                        is_dir($base . $entry) &&
                        !in_array($entry, $ignore)
                    ) {
                        if ($fullPath)
                            $entry = $base . $entry;
                        $dirs[] = $entry;
                    }
                }
                closedir($handle);
            }
            if (count($dirs) > 0)
                return $dirs;
        }
        return false;
    }

    public static function getFiles($base, $fullPath = false, $ext = false, $ignore = array())
    {
        if (is_dir($base) && is_readable($base)) {
            $files = array();
            if ($handle = opendir($base)) {
                while (false !== ($entry = readdir($handle))) {
                    if (
                        $entry != "." &&
                        $entry != ".." &&
                        is_file($base . DIRECTORY_SEPARATOR . $entry) &&
                        !in_array($entry, $ignore)
                    ) {
                        if (is_array($ext) && !in_array(end(explode('.', $entry)), $ext))
                            continue;
                        if ($fullPath)
                            $entry = $base . DIRECTORY_SEPARATOR . $entry;
                        $files[] = $entry;
                    }
                }
                closedir($handle);
            }
            if (count($files) > 0)
                return $files;
        }
        return false;
    }

    /**
     * Calculate the site of the folder in bytes
     * @param $path string full path to the folder
     * @return int bytes
     */
    public static function getSize($path)
    {
        $size = 0;
        if (is_dir($path) && is_readable($path)) {
            if ($handle = opendir($path)) {
                while (false !== ($entry = readdir($handle))) {
                    if (
                        $entry != "." &&
                        $entry != ".."
                    ) {
                        $entry = $path . DIRECTORY_SEPARATOR . $entry;
                        if (is_file($entry))
                            $size += File::getSize($entry);
                        elseif(is_dir($entry))
                            $size += self::getSize($entry);
                    }
                }
                closedir($handle);
            }
        }
        return $size;
    }

    /**
     * Clears the entire content of a directory
     * @param $dir
     * @param bool $removeSelf
     */
    public static function clear($dir, $removeSelf = false)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir")
                        self::clear($dir . "/" . $object, true);
                    elseif (is_file($dir . "/" . $object))
                        unlink($dir . "/" . $object);
                }
            }
            reset($objects);
            if ($removeSelf)
                rmdir($dir);
        } elseif (is_file($dir))
            unlink($dir);
    }

    public static function copyDir($sourceDir, $destinationDir)
    {
        if (is_dir($sourceDir)) {
            $dir = opendir($sourceDir);
            if (!is_dir($destinationDir))
                mkdir($destinationDir, 0755, true);
            while (false !== ($file = readdir($dir))) {
                if (($file != '.') && ($file != '..')) {
                    if (is_dir($sourceDir . '/' . $file)) {
                        self::copyDir($sourceDir . '/' . $file, $destinationDir . '/' . $file);
                    } else {
                        copy($sourceDir . '/' . $file, $destinationDir . '/' . $file);
                    }
                }
            }
            closedir($dir);
        }
    }
}
