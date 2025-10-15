<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/21/13
 * Time: 10:41 AM
 */

namespace AssetManager\View\Helper;


use Application\API\App;
use Assetic\Asset\AssetInterface;
use Assetic\Asset\FileAsset;

class HeadScript extends \Zend\View\Helper\HeadScript
{
    /**
     * Retrieve string representation
     *
     * @param  string|int $indent Amount of whitespaces or string to use for indention
     * @return string
     */
    public function toString($indent = null)
    {
        $config = getConfig('system_optimization_config')->varValue;
        $combineJs = @$config['combine_js'];
        $indent = (null !== $indent)
            ? $this->getWhitespace($indent)
            : $this->getIndent();

        if ($this->view) {
            $useCdata = $this->view->plugin('doctype')->isXhtml() ? true : false;
        } else {
            $useCdata = $this->useCdata ? true : false;
        }

        $escapeStart = ($useCdata) ? '//<![CDATA[' : '//<!--';
        $escapeEnd = ($useCdata) ? '//]]>' : '//-->';

        $items = array();
        $this->getContainer()->ksort();

        /* @var $assetManager \AssetManager\Service\AssetManager */
        $assetManager = getSM('AssetManager\Service\AssetManager');

        $jsFiles = array();
        $cachedAssets = getCache(true)->getItem('public_assets_js');
        if (!$cachedAssets)
            $cachedAssets = array();

        foreach ($this as $item) {
            if (!$this->isValid($item)) {
                continue;
            }
            $minified = false;
            if ($combineJs) {
//                var_dump($item);
                if (!isset($item->attributes['conditional'])) {
                    if (isset($item->attributes['src'])) {
                        $src = $item->attributes['src'];
                        //strpos($src, 'jquery.watermark.min.js') === false &&
                        if (strpos($src, 'http') === false) {
                            if (array_key_exists($src, $cachedAssets)) {
                                $jsFiles[$src] = $cachedAssets[$src];
                                $minified = true;
                            } else {
                                $asset = $assetManager->getResolver()->resolve($src);
                                if ($asset instanceof AssetInterface) {
                                    $file = $asset->getSourceRoot() . '/' . $asset->getSourcePath();
                                    $cachedAssets[$src] = $file;
                                    $jsFiles[$src] = $file;
                                    $minified = true;
                                }
                            }
                        }
                    }
                }
            }

            if (!$minified)
                $items[] = $this->itemToString($item, $indent, $escapeStart, $escapeEnd);
        }
        if (count($jsFiles)) {

            $name = '';
            /* @var $asset FileAsset */
            foreach ($jsFiles as $src => $file) {
                $name .= $src;
            }
            $name = md5($name) . '.js';

            $destinationFolder = PUBLIC_FILE_PATH . '/cache/js';
            $fileName = $destinationFolder . '/' . $name;
            $destinationFolder = PUBLIC_PATH . $destinationFolder;
            $src = $this->view->basePath() . $fileName;
            $fileName = PUBLIC_PATH . $fileName;

            $item = new \stdClass();
            $item->type = 'text/javascript';
            $item->attributes = array('src' => $src);
            $item->source = null;
            array_unshift($items, $this->itemToString($item, $indent, $escapeStart, $escapeEnd));

            if (!file_exists($fileName)) {
                $scriptFileContent = '';

                foreach ($jsFiles as $src => $file) {
                    if (file_exists($file)) {
                        $content = file_get_contents($file);
                        if (strpos($src, '.min.') === false) {
                            $content = \JSMin::minify($content);
                        }
                        $scriptFileContent .= $content;
                    }
                }


                if (!is_dir($destinationFolder))
                    mkdir($destinationFolder, 0755, true);
                file_put_contents($fileName, $scriptFileContent);
            }
        }

        getCache(true)->setItem('public_assets_js', $cachedAssets);
        return implode($this->getSeparator(), $items);
    }
} 