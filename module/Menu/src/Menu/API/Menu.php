<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Email adeli.farhad@gmail.com
 * Date: 6/3/13
 * Time: 12:53 PM
 */
namespace Menu\API;

use Application\API\App;
use System\API\BaseAPI;

/**
 * Class Menu
 * @package Menu\API
 */
class Menu extends BaseAPI
{
    const LOAD_MENU_TYPES = 'loading_menu_uri_types';

    public function LoadMenuTypes($form)
    {
        $this->getEventManager()->trigger(self::LOAD_MENU_TYPES, $this, array('form' => $form));
    }

    public static function makeMenuUrl($params)
    {
        $maniParams = $params;
        $type = current(array_keys($params));
        $params = $params[$type];
        $page = null;
        switch ($type) {
            case 'externalUrl':
                $url = $params['url'];
                if (strpos($url, 'http') === false)
                    $url = 'http://' . $url;
                $page = new \Zend\Navigation\Page\Uri();
                $page->setUri($url);
                break;
            //systemUrl -> uri
            case 'systemUrl':
                $url = $params['uri'];
                if (strpos($url, 'http') === false) {
                    if ($url[0] != '/')
                        $url = '/' . $url;
//                    $url = App::siteUrl() . $url;
                }
                $page = new \Menu\Navigation\Page\Uri();
                $page->setUri($url);
                break;
            default:
                if ($type == "") {
                    $page = new \Zend\Navigation\Page\Uri();
                    $page->setUri("#");
                } else {
                    $page = new \Menu\Navigation\Page\Mvc();
                    $page->setRoute('app');

                    try {
                        $page->setRouter(getSM('Router'));
                        if (isset($params['params'])) {
                            $route = $params['params']['route'];
                            if ($route == 'app/front-page' && !\Application\API\App::hasIntro())
                                $route = 'app';
                            $page->setRoute($route);
                            unset($params['params']['route']);

                            if (isset($params['params'])) {
                                $params = $params['params'];
                                array_walk($params, function (&$item, $index) {
                                    $item = App::prepareUrlString($item);
                                });
                                $page->setParams($params);
                            }
                            if (isset($params['query']))
                                $page->setQuery($params['query']);
                        } else {
                            db_log_error('MVC menu page should have params (route and params).edit the menu item to fix this');
                        }
                    } catch (\Exception $e) {
                        db_log_exception($e);
                    }
                }
                break;
        }
        return $page;
    }
}