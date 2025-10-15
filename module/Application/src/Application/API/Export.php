<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 12/10/13
 * Time: 10:05 AM
 */

namespace Application\API;


class Export
{
    /**
     * @param $type ,example type= word or ...
     * @param $html ,body
     * @param $css ,name css file
     * @return \Zend\Http\PhpEnvironment\Response
     */
//    public static function export($type, $html, $css)
//    {
//        if ($type == 'word') {
//
//        }
//    }

    /**
     * @param $html
     * @param $css
     * @return \Zend\Http\PhpEnvironment\Response
     */
    public static function exportToWord($html, $css)
    {
        $basePath = getSM()->get('viewhelpermanager')->get('basePath');

        $titleFormat = array(
            'text-align' => 'center',
            'font-weight' => 'bold',
            'font-size' => '18pt',
            'color' => 'blue');

        $doc = new clsMsDocGenerator($pageOrientation = 'PORTRAIT', $pageType = 'A4', $cssFile = $basePath . TEMPLATE_PATH . '/css/' . $css . '.css');
        $doc->addParagraph(($html), array('text-align' => 'right', 'font-family' => 'Tahoma','direction'=>'rtl'));
        $html = $doc->output();
        $response = new \Zend\Http\PhpEnvironment\Response();
        $response->getHeaders()->addHeaders(array(
            'Content-type' => 'application/msword ; charset=UTF-8',
            'Content-Disposition' => 'attachment ; filename=export.doc',
        ));
        $response->setContent($html);
        return $response;
    }
} 