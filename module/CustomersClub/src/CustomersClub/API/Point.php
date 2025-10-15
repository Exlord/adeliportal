<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 4/13/13
 * Time: 3:21 PM
 */

namespace CustomersClub\API;

use System\API\BaseAPI;
use Zend\Db\Adapter\Adapter;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Input;

class Point extends BaseAPI
{
    private $admin = null;
    private $config = null;

    public function addPoint($module, $event, $userId, $note, $count = 1, $amount = null)
    {
        $config = $this->getConfig();
        if (isset($config['modules'][$module][$event])) {
            $userId = (int)$userId;
            if ($userId) {

                if (!$amount)
                    $points = $config['modules'][$module][$event];
                else {
                    $points = 0;
                    if (isset($config['modules'][$module][$event]['values'])) {
                        $values = $config['modules'][$module][$event]['values'];
                        if (count($values)) {
                            foreach ($values as $params) {
                                if (isset($params['points']) && has_value($params['points'])) {
                                    $from = 0;
                                    $to = null;
                                    $point = $params['points'];

                                    if (isset($params['from']) && has_value($params['from']))
                                        $from = $params['from'];

                                    if (isset($params['to']) && !empty($params['to']) & $params['to'] > $from)
                                        $to = $params['to'];

                                    if ($to) {
                                        if ($amount >= $from && $amount <= $to) {
                                            $points = $point;
                                            break;
                                        }
                                    } elseif ($amount >= $from) {
                                        $points = $point;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }

                if ($points) {
                    getSM('points_table')->save(array(
                        'userId' => $userId,
                        'points' => $points * $count,
                        'note' => $note,
                        'date' => time()
                    ));
                }
            }
        }
    }

//    public function getPoint($module, $event)
//    {
//        $config = $this->getConfig();
//        if (isset($config['modules'])) {
//            if (isset($config['modules'][$module])) {
//                if (isset($config['modules'][$module][$event]))
//                    return (double)$config['modules'][$module][$event];
//            }
//        }
//        return 0;
//    }

    /**
     * @param $userOrUserId
     * @param $points
     * @param $note
     * @param string $type new/edit/delete
     * @param int $pointBefore in case of edit or delete
     */
    public function notify($userOrUserId, $points, $note, $type = 'new', $pointBefore = null)
    {
        $userId = (int)(is_scalar($userOrUserId) ? $userOrUserId : ((array)$userOrUserId['id']));
        if ($userId) {
            $points = (int)$points;
            if ($notifyApi = getNotifyApi()) {
                $notifyApi->getInternal()->uId = $userId;

                $class = 'label-success';
                if ($points < 0)
                    $class = 'label-danger';
                else
                    $points = '+' . $points;

                if ($type == 'new')
                    $notifyApi->notify('CustomersClub', 'points', array(
                        '__POINT__' => "<span class='label {$class}' dir='ltr'>{$points}</span>",
                        '__POINT_NOTE__' => "<mark>{$note}</mark>"
                    ));
                elseif ($type == 'edit') {
                    $admin = getSM('user_table')->getUser(current_user()->id, array('table' => array('username', 'displayName'), 'profile' => array('firstName', 'lastName')));
                    $admin = getUserDisplayName($admin);
                    $class2 = 'label-success';
                    if ($pointBefore < 0)
                        $class2 = 'label-danger';
                    else
                        $pointBefore = '+' . $pointBefore;

                    $notifyApi->notify('CustomersClub', 'points_edit', array(
                        '__ADMIN__' => "<mark>{$admin}</mark>",
                        '__POINT_BEFORE__' => "<span class='label {$class2}' dir='ltr'>{$pointBefore}</span>",
                        '__POINT_AFTER__' => "<span class='label {$class}' dir='ltr'>{$points}</span>",
                        '__POINT_NOTE__' => "<mark>{$note}</mark>"
                    ));
                } elseif ($type == 'delete') {
                    if ($this->admin == null) {
                        $admin = getSM('user_table')->getUser(current_user()->id, array('table' => array('username', 'displayName'), 'profile' => array('firstName', 'lastName')));
                        $this->admin = getUserDisplayName($admin);
                    }
                    $admin = $this->admin;
                    $class2 = 'label-success';
                    if ($pointBefore < 0)
                        $class2 = 'label-danger';
                    else
                        $pointBefore = '+' . $pointBefore;

                    $notifyApi->notify('CustomersClub', 'points_delete', array(
                        '__ADMIN__' => "<mark>{$admin}</mark>",
                        '__POINT__' => "<span class='label {$class2}' dir='ltr'>{$pointBefore}</span>",
                        '__POINT_NOTE__' => "<mark>{$note}</mark>"
                    ));
                }
            }
        }
    }

    private function getConfig()
    {
        if ($this->config == null)
            $this->config = getConfig('customers-club')->varValue;
        return $this->config;
    }
}