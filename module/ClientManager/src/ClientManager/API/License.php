<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 12/30/13
 * Time: 9:20 AM
 */

namespace ClientManager\API;


use Zend\Crypt\BlockCipher;

class License
{
    const BASIC = 1;//use only no update
    const NORMAL = 2;//can update
    const WEB_MASTER = 3;//can update and request license for sub-sites

    private static $validTypes = array(
        self::BASIC,
        self::NORMAL,
        self::WEB_MASTER
    );

    public static $allowedToUpdateTypes = array(
        self::NORMAL,
        self::WEB_MASTER
    );

    /**
     * @param $clientId
     * @param $expireDate
     * @param int $type
     * @return \ClientManager\Model\License
     */
    public static function makeNewLicense($clientId, $expireDate, $type = License::NORMAL)
    {
        $license = new \ClientManager\Model\License();
        $license->clientId = $clientId;
        $license->expireDate = $expireDate;
        $license->startDate = time();
        $license->key = self::makeLicenseKey();
        if (!in_array($type, self::$validTypes))
            $type = self::BASIC;

        $license->type = $type;

        return $license;
    }

    /**
     * @param \ClientManager\Model\License $license
     * @return string
     */
    public static function encrypt(\ClientManager\Model\License $license)
    {
        $data = json_encode(array(
            'clientId' => $license->clientId,
            'type' => $license->type,
            'startDate' => $license->startDate,
            'expireDate' => $license->expireDate
        ));
        $blockCipher = BlockCipher::factory('mcrypt', array('algo' => 'aes'));
        $blockCipher->setKey($license->key);
        return $blockCipher->encrypt($data);
    }

    /**
     * @param $license
     * @param $key
     * @return bool|array
     */
    public static function decrypt($license,$key)
    {
        $blockCipher = BlockCipher::factory('mcrypt', array('algo' => 'aes'));
        $blockCipher->setKey($key);
        $data = $blockCipher->decrypt($license);
        if($data){
            $data = json_decode($data);
        }
        return $data;
    }

    /**
     * @return string
     */
    private static function makeLicenseKey()
    {
        return sha1(md5(uniqid()));
    }
} 