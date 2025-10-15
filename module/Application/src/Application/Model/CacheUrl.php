<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 3/11/14
 * Time: 11:51 AM
 */

namespace Application\Model;


use System\Model\BaseModel;

class CacheUrl extends BaseModel
{
    public $url;
    public $matchedRoute;

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $matchedRoute
     */
    public function setMatchedRoute($matchedRoute)
    {
        $this->matchedRoute = $matchedRoute;
    }

    /**
     * @return mixed
     */
    public function getMatchedRoute()
    {
        return $this->matchedRoute;
    }


}