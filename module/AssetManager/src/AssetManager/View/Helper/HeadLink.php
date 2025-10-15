<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/21/13
 * Time: 1:15 PM
 */

namespace AssetManager\View\Helper;


use Application\API\App;
use Assetic\Asset\AssetInterface;
use Assetic\Asset\FileAsset;

class HeadLink extends \Zend\View\Helper\HeadLink
{
    /**
     * Render link elements as string
     *
     * @param  string|int $indent
     * @return string
     */
    public function toString($indent = null)
    {
        $config = getConfig('system_optimization_config')->varValue;
        $combineCss = @$config['combine_css'];

        $indent = (null !== $indent)
            ? $this->getWhitespace($indent)
            : $this->getIndent();

        $items = array();
        $this->getContainer()->ksort();

//        $filesToMinify = array();
        $cssFiles = array();
        $favicon = null;

        foreach ($this as $item) {
            $minified = false;
            if ($combineCss) {
                if (!(isset($item->conditionalStylesheet) && $item->conditionalStylesheet !== false)) {
                    if (isset($item->media) && $item->media == 'screen') {
                        if ($item->href) {
                            if (strpos($item->href, 'http') === false) {
                                $cssFiles[] = $item->href;
                                $minified = true;
                            }
                        }
                    }
                }
            }
            if (!$minified)
                $items[] = $this->itemToString($item);
        }

        if (count($cssFiles)) {

            $name = '';
            foreach ($cssFiles as $href) {
                $name .= $href;
            }
            $name = md5($name) . '.css';

            $destinationFolder = PUBLIC_FILE_PATH . '/cache/css';
            $fileName = $destinationFolder . '/' . $name;
            $itemHref = $this->view->basePath() . $fileName;
            $destinationFolder = PUBLIC_PATH . $destinationFolder;
            $fileName = PUBLIC_PATH . $fileName;

            $item = new \stdClass();
            $item->rel = 'stylesheet';
            $item->type = 'text/css';
            $item->href = $itemHref;
            $item->media = 'screen';
            $item->conditionalStylesheet = false;
            array_unshift($items, $this->itemToString($item));


            if (!file_exists($fileName)) {
                $cssMin = new \CSSmin();
                $styleFileContent = '';
                /* @var $assetManager \AssetManager\Service\AssetManager */
                $assetManager = getSM('AssetManager\Service\AssetManager');

                $cachedAssets = getCache(true)->getItem('public_assets_css');
                if (!$cachedAssets)
                    $cachedAssets = array();

                foreach ($cssFiles as $href) {

                    if (!array_key_exists($href, $cachedAssets)) {
                        $asset = $assetManager->getResolver()->resolve($href);
                        if ($asset instanceof AssetInterface) {
                            $root = $asset->getSourceRoot();
                            $root = str_replace('\\', '/', $root);
                            $path = $asset->getSourcePath();

                            $realFilePath = $root . '/' . $path;

                            $publicPos = strpos($root, '/public/');
                            $publicPath = substr($root, $publicPos + 7);
                            $root = PUBLIC_PATH . $publicPath;

                            $cachedAssets[$href] = array($realFilePath, $root);

                        } else {

                            $realFilePath = PUBLIC_PATH . $href;
                            $root = dirname($realFilePath);
                            $cachedAssets[$href] = array($realFilePath, $root);
                        }

                    }

                    $styleFileContent .= $this->_minify(
                        $cachedAssets[$href][0],
                        $destinationFolder,
                        $cachedAssets[$href][1],
                        $cssMin);

                }


                if (!is_dir($destinationFolder))
                    mkdir($destinationFolder, 0755, true);
                file_put_contents($fileName, $styleFileContent);

                getCache(true)->setItem('public_assets_css', $cachedAssets);
            }
        }

//        if (count($filesToMinify)) {
//
//            $cssMin = new \CSSmin();
//            $name = '';
//            foreach ($filesToMinify as $root => $files) {
//                foreach ($files as $path) {
//                    $name .= $path;
//                }
//            }
//
//            $name = md5($name) . '.css';
//            $fileName = PUBLIC_PATH . '/css/' . $name;
//            $item = new \stdClass();
//            $item->rel = 'stylesheet';
//            $item->type = 'text/css';
//            $item->href = $this->view->basePath() . '/css/' . $name;
//            $item->media = 'screen';
//            $item->conditionalStylesheet = false;
//            array_unshift($items, $this->itemToString($item));
//
//            if (!file_exists($fileName)) {
//                $styleFileContent = '';
//                foreach ($filesToMinify as $root => $files) {
//                    foreach ($files as $path) {
//                        $content = file_get_contents($path);
//                        if (strpos($path, '.min.') === false) {
//                            $content = $cssMin->run($content);
//                        }
//                        $root = str_replace('\\', '/', $root);
//                        if (trim($root) != '/css/') {
//                            $content = \Minify_CSS::minify($content, array('docRoot' => ROOT, 'currentDir' => ROOT . $root));
//                        }
//                        $styleFileContent .= $content . "\n";
//                    }
//                }
//                if (!is_dir(PUBLIC_PATH . $root))
//                    mkdir(PUBLIC_PATH . $root, 0755, true);
//                file_put_contents($fileName, $styleFileContent);
//            }
//        }
        return $indent . implode($this->escape($this->getSeparator()) . $indent, $items);
    }

    private function _minify($realFilePath, $destinationFolder, $root, $cssMin)
    {

        $content = file_get_contents($realFilePath);
        if (strpos($realFilePath, '.min.') === false) {
            $content = $cssMin->run($content);
        }

        $content = \Minify_CSS::minify($content,
            array('docRoot' => PUBLIC_PATH, 'currentDir' => $root, 'preserveComments' => false));

        return $content;
    }

//    public function toString($indent = null)
//    {
//        $config = getConfig('system_optimization_config')->varValue;
//        $combineCss = @$config['combine_css'];
//        $indent = (null !== $indent)
//            ? $this->getWhitespace($indent)
//            : $this->getIndent();
//
//        $items = array();
//        $this->getContainer()->ksort();
//
//        /* @var $assetManager \AssetManager\Service\AssetManager */
//        $assetManager = getSM('AssetManager\Service\AssetManager');
//
//        $filesToMinify = array();
//
//        foreach ($this as $item) {
//            $minified = false;
//            if ($combineCss) {
//                if (!$item->conditionalStylesheet) {
//                    if ($item->href) {
//                        $asset = $assetManager->getResolver()->resolve($item->href);
//                        if ($asset instanceof AssetInterface) {
//                            $root = $asset->getSourceRoot();
//                            $pos = strpos($root, DIRECTORY_SEPARATOR . 'module' . DIRECTORY_SEPARATOR);
//                            $root = substr($root, $pos); //original directory
//                            $pos = strpos($root, DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
//                            $dest = substr($root, $pos + 7); //destination directory
//                            $filesToMinify[$dest . DIRECTORY_SEPARATOR][] = ROOT . $root . DIRECTORY_SEPARATOR . $asset->getSourcePath();
//                            $minified = true;
//                        }
////                    else {
////                        if (strpos($item->href, '/themes/') > -1) {
////                            $root = substr($item->href, 0, strrpos($item->href, '/') + 1);
////                            $file = str_replace($root, '', $item->href);
////                            $root = str_replace('/', DIRECTORY_SEPARATOR, $root);
////                            $filesToMinify[$root][] = PUBLIC_PATH . $root . $file;
////                            $minified = true;
////                        }
////                    }
//                    }
//                }
//            }
//            if (!$minified)
//                $items[] = $this->itemToString($item);
//        }
//
//        if (count($filesToMinify)) {
//
//            $cssMin = new \CSSmin();
//            foreach ($filesToMinify as $root => $files) {
//                $name = '';
//                foreach ($files as $path) {
//                    $name .= $path;
//                }
//
//                $name = md5($name) . '.css';
//                $fileName = PUBLIC_PATH . $root . $name;
//                $item = new \stdClass();
//                $item->rel = 'stylesheet';
//                $item->type = 'text/css';
//                $item->href = $this->view->basePath() . str_replace('\\', '/', $root) . $name;
//                $item->media = 'screen';
//                $item->conditionalStylesheet = false;
//
//                array_unshift($items, $this->itemToString($item));
//
//                if (!file_exists($fileName)) {
//                    $styleFileContent = '';
//                    foreach ($files as $path) {
//                        $content = file_get_contents($path);
//                        if (strpos($path, '.min.') === false){
//
//                            $content = $cssMin->run($content);
//                        }
//                        $styleFileContent .= $content . "\n";
//                    }
//                    if (!is_dir(PUBLIC_PATH . $root))
//                        mkdir(PUBLIC_PATH . $root, 0755, true);
//                    file_put_contents($fileName, $styleFileContent);
//                }
//            }
//        }
//
//        return $indent . implode($this->escape($this->getSeparator()) . $indent, $items);
//    }
}