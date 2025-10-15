<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 12/4/13
 * Time: 11:50 AM
 */

namespace ServerManager\API\Hosting;


abstract class AbstractHost
{
    /**
     * @var Error
     */
    public $error;

    abstract protected function getUsername();

    abstract public function __construct($username, $password, $domain, $port, $scheme = 'http');

    /**
     * @param $dbName
     * @param $dbUser
     * @param $dbPass
     * @return Result
     */
    abstract public function createDataBase($dbName, $dbUser, $dbPass);

    /**
     * @param $dbNames
     * @return Result
     */
    abstract public function deleteDataBase($dbNames);

    /**
     * @return Result
     */
    abstract public function listDataBases();

    /**
     * @return Result
     */
    abstract public function listDomains();

    /**
     * @param $domain
     * @return Result
     */
    abstract public function listDomainPointers($domain);

    /**
     * @param $from
     * @param $to
     * @param string $alias
     * @return Result
     */
    abstract public function createDomainPointer($from, $to, $alias = 'yes');

    /**
     * @param $domain
     * @param $pointers
     * @return Result
     */
    abstract public function deleteDomainPointer($domain, $pointers);
}