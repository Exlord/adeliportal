<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 12/4/13
 * Time: 9:58 AM
 */

namespace ServerManager\API\Hosting\DA;

class DirectAdmin extends AbstractDirectAdmin
{
    /**
     * @param $dbName
     * @param $dbUser
     * @param $dbPass
     * @return Result
     */
    public function createDataBase($dbName, $dbUser, $dbPass)
    {
        $result = $this->retrieve(
            array(
                'method' => 'POST',
                'command' => 'CMD_API_DATABASES',
                'data' => array(
                    'action' => 'create',
                    'name' => $dbName,
                    'user' => $dbUser,
                    'passwd' => $dbPass,
                    'passwd2' => $dbPass
                )
            )
        );
        $result->result['dbUser'] = $this->getUsername() . '_' . $dbUser;
        $result->result['dbName'] = $this->getUsername() . '_' . $dbName;
        return $result;
    }

    public function deleteDataBase($dbNames)
    {
        $data = array(
            'action' => 'delete',
        );
        if (is_array($dbNames)) {
            for ($i = 0; $i < count($dbNames); $i++) {
                $data['select' . $i] = $this->getUsername() . '_' . $dbNames[$i];
            }
        } else
            $data['select0'] = $this->getUsername() . '_' . $dbNames;

        return $this->retrieve(
            array(
                'method' => 'POST',
                'command' => 'CMD_API_DATABASES',
                'data' => $data
            )
        );
    }

    public function listDataBases()
    {
        return $this->retrieve(
            array(
                'method' => 'POST',
                'command' => 'CMD_API_DATABASES',
                'data' => array()
            )
        );
    }

    public function listDomains()
    {
        return $this->retrieve(
            array(
                'method' => 'POST',
                'command' => 'CMD_API_SHOW_DOMAINS',
                'data' => array()
            )
        );
    }

//    public function createDomain($ubandwidth){
//        return $this->retrieve(
//            array(
//                'method' => 'POST',
//                'command' => 'CMD_API_DOMAIN',
//                'data' => array(
//                    'action'=>'create',
//                    'domain'=>$domain,
//                    'bandwidth'=>$bandwidth,
//                    'ubandwidth'=>$ubandwidth,
//                    'uquota'=>'unlimited',
//                    'ssl'=>$ssl,
//                    'cgi'=>$cgi,
//                    'php'=>$php
//                )
//            )
//        );
//    }
    public function listDomainPointers($domain)
    {
        return $this->retrieve(
            array(
                'method' => 'POST',
                'command' => 'CMD_API_DOMAIN_POINTER',
                'data' => array(
                    'domain' => $domain,
                )
            )
        );
    }

    public function createDomainPointer($from, $to, $alias = 'yes')
    {
        return $this->retrieve(
            array(
                'method' => 'POST',
                'command' => 'CMD_API_DOMAIN_POINTER',
                'data' => array(
                    'action' => 'add',
                    'domain' => $to,
                    'from' => $from,
                    'alias' => $alias
                )
            )
        );
    }

    public function deleteDomainPointer($domain, $pointers)
    {
        $data = array(
            'action' => 'delete',
            'domain' => $domain,
        );
        if (is_array($pointers)) {
            for ($i = 0; $i < count($pointers); $i++) {
                $data['select' . $i] = $pointers[$i];
            }
        } else
            $data['select0'] = $pointers;

        return $this->retrieve(
            array(
                'method' => 'POST',
                'command' => 'CMD_API_DOMAIN_POINTER',
                'data' => $data
            )
        );
    }
} 