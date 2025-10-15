<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 12/4/13
 * Time: 10:58 AM
 */

namespace ServerManager\API\Hosting;

abstract class Host
{
    const DIRECT_ADMIN = 'DirectAdmin';
    public static $DefaultHostingPanel = self::DIRECT_ADMIN;
    private static $ValidHostingPanels = array(
        self::DIRECT_ADMIN
    );
    private static $HostingAPIs = array(
        self::DIRECT_ADMIN => 'ServerManager\API\Hosting\DA\DirectAdmin'
    );
    /**
     * @var AbstractHost
     */
    public static $hostApi = null;

    /**
     * @return AbstractHost
     * @throws \Exception
     */
    public static function GetApi()
    {
        if (self::$hostApi == null || !(self::$hostApi instanceof AbstractHost)) {
            $config = getSM('ApplicationConfig');

            if(!isset($config['host']) || !count($config))
                throw new \Exception('Server Configs have not been set. provide a host key in client config file.');

            $config = $config['host'];
            //--------------------- hosting panel -------------------------------
            if (isset($config['api']) && in_array($config['api'], self::$ValidHostingPanels))
                $hostingPanel = $config['api'];
            else {
                $hostingPanel = self::$DefaultHostingPanel;
            }

            if ($hostingPanel == null)
                throw new \Exception($hostingPanel . ' is NOT a Valid Hosting Panel API');

            $domain = $config['domain'];
            $port = $config['port'];
            $scheme = $config['scheme'];
            $username = $config['username'];
            $password = $config['password'];

            $panelApi = self::$HostingAPIs[$hostingPanel];
            self::$hostApi = new $panelApi($username, $password, $domain, $port, $scheme);
        }
        return self::$hostApi;
    }
}