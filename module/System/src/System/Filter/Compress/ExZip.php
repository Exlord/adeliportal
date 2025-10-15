<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/2/14
 * Time: 11:37 AM
 */
namespace System\Filter\Compress;

use Zend\Filter\Compress\Zip;
use Zend\Filter\Exception;
use ZipArchive;

class ExZip extends Zip
{
    private $ignore = null;

    public function setIgnore($value)
    {
        $this->ignore = $value;
    }

    public function compress($content)
    {
        $zip = new ZipArchive();
        $res = $zip->open($this->getArchive(), ZipArchive::CREATE | ZipArchive::OVERWRITE);

        if ($res !== true) {
            throw new Exception\RuntimeException($this->errorString($res));
        }

        if (file_exists($content)) {
            $content = str_replace(array('/', '\\'), '/', realpath($content));
            $basename = substr($content, strrpos($content, '/') + 1);
            if (is_dir($content)) {
                $index = strrpos($content, '/') + 1;
                $content .= '/';
                $stack = array($content);
                while (!empty($stack)) {
                    $current = array_pop($stack);
                    $files = array();

                    //------------ IGNORE -------------------
                    if ($this->ignore) {
                        if (isset($this->ignore['dir'])) {
                            if (in_array($current, $this->ignore['dir'])) {
                                if (isset($this->ignore['exception'])) {
                                    if (!array_key_exists($current, $this->ignore['exception']))
                                        continue;
                                } else
                                    continue;
                            }
                        }
                    }
                    //---------------------------------------

                    $dir = dir($current);
                    while (false !== ($node = $dir->read())) {
                        if (($node == '.') || ($node == '..')) {
                            continue;
                        }

                        //------------ IGNORE -------------------
                        if ($this->ignore) {
                            if (isset($this->ignore['dir'])) {
                                if (in_array($current, $this->ignore['dir'])) {
                                    if (isset($this->ignore['exception'])) {
                                        if (array_key_exists($current, $this->ignore['exception'])) {
                                            if (!in_array($node, $this->ignore['exception'][$current]))
                                                continue;
                                        } else
                                            continue;
                                    } else
                                        continue;
                                }
                            }
                            if (isset($this->ignore['file'])) {
                                if (in_array($node, $this->ignore['file']))
                                    continue;
                            }
                        }
                        //---------------------------------------

                        if (is_dir($current . $node)) {
                            array_push($stack, $current . $node . '/');
                        }

                        if (is_file($current . $node)) {
                            $files[] = $node;
                        }
                    }


                    $local = substr($current, $index);
                    if ($this->ignore && isset($this->ignore['self']) && $this->ignore['self'] == true) {
                        $local = substr($local, strpos($local, '/') + 1);
                    } else
                        $zip->addEmptyDir(substr($local, 0, -1));

                    foreach ($files as $file) {
                        $zip->addFile($current . $file, $local . $file);
                        if ($res !== true) {
                            throw new Exception\RuntimeException($this->errorString($res));
                        }
                    }
                }
            } else {
                $res = $zip->addFile($content, $basename);
                if ($res !== true) {
                    throw new Exception\RuntimeException($this->errorString($res));
                }
            }
        } else {
            $file = $this->getTarget();
            if (!is_dir($file)) {
                $file = basename($file);
            } else {
                $file = "zip.tmp";
            }

            $res = $zip->addFromString($file, $content);
            if ($res !== true) {
                throw new Exception\RuntimeException($this->errorString($res));
            }
        }

        $zip->close();
        return $this->options['archive'];
    }
} 