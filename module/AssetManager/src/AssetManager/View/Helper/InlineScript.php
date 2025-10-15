<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/29/14
 * Time: 2:21 PM
 */

namespace AssetManager\View\Helper;


use Assetic\Asset\AssetInterface;
use Assetic\Asset\FileAsset;

class InlineScript extends \Zend\View\Helper\InlineScript{
    /**
     * Retrieve string representation
     *
     * @param  string|int $indent Amount of whitespaces or string to use for indention
     * @return string
     */
//    public function toString($indent = null)
//    {
//        $config = getConfig('system_optimization_config')->varValue;
//        $combineJs = @$config['combine_js'];
//        $indent = (null !== $indent)
//            ? $this->getWhitespace($indent)
//            : $this->getIndent();
//
//        if ($this->view) {
//            $useCdata = $this->view->plugin('doctype')->isXhtml() ? true : false;
//        } else {
//            $useCdata = $this->useCdata ? true : false;
//        }
//
//        $escapeStart = ($useCdata) ? '//<![CDATA[' : '//<!--';
//        $escapeEnd = ($useCdata) ? '//]]>' : '//-->';
//
//        $items = array();
//        $this->getContainer()->ksort();
//
//        /* @var $assetManager \AssetManager\Service\AssetManager */
//        $assetManager = getSM('AssetManager\Service\AssetManager');
//
//        $jsFiles = array();
//        foreach ($this as $item) {
//            if (!$this->isValid($item)) {
//                continue;
//            }
//            $minified = false;
//            if ($combineJs ) {
////                var_dump($item);
//                if (isset($item->attributes['src'])) {
//                    $src = $item->attributes['src'];
//                    if (strpos($src, 'jquery.watermark.min.js') === false && strpos($src, 'http') === false) {
//                        $asset = $assetManager->getResolver()->resolve($src);
//                        if ($asset instanceof AssetInterface) {
//                            $jsFiles[] = $asset;
//                            $minified = true;
//                        }
//                    }
//                }
//            }
//
//            if (!$minified)
//                $items[] = $this->itemToString($item, $indent, $escapeStart, $escapeEnd);
//        }
//        if (count($jsFiles)) {
//
//            $name = '';
//            /* @var $asset FileAsset */
//            foreach ($jsFiles as $asset) {
//                $name .= $asset->getSourcePath();
//            }
//            $name = md5($name) . '.js';
//
//            $destinationFolder = PUBLIC_FILE_PATH . '/cache/js';
//            $fileName = $destinationFolder . '/' . $name;
//            $destinationFolder = PUBLIC_PATH . $destinationFolder;
//            $src = $this->view->basePath() . $fileName;
//            $fileName = PUBLIC_PATH . $fileName;
//
//            $item = new \stdClass();
//            $item->type = 'text/javascript';
//            $item->attributes = array('src' => $src);
//            $item->source = null;
//            array_unshift($items, $this->itemToString($item, $indent, $escapeStart, $escapeEnd));
//
//            if (!file_exists($fileName)) {
//                $scriptFileContent = '';
//
//                foreach ($jsFiles as $asset) {
//                    $asset->load();
//                    $content = $asset->getContent();
//                    if (strpos($asset->getSourcePath(), '.min.') === false) {
//                        $content = \JSMin::minify($content);
//                    }
//                    $scriptFileContent .= $content;
//                }
//
//
//                if (!is_dir($destinationFolder))
//                    mkdir($destinationFolder, 0755, true);
//                file_put_contents($fileName, $scriptFileContent);
//            }
//        }
//
//        return implode($this->getSeparator(), $items);
//    }
} 